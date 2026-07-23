<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentBranch;
use App\Models\Inspection;
use App\Models\InspectionDetail;
use App\Models\InspectionMedia;
use App\Models\InspectionSection;
use App\Models\InspectionSectionSummary;
use App\Models\InspectionStep;
use App\Models\InspectionSummary;
use App\Models\Lead;
use App\Support\VehicleLookups;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionController extends Controller
{
    use ResolvesCurrentBranch;

     public function index(Request $request): View
    {
        $branch = $this->requireBranch();
        $user = $request->user();

        // CRM staff manage all inspections; technicians use the app/API only.
        // Order by most-recently-updated so edited inspections bubble to the top,
        // and a brand-new inspection (updated_at == created_at) also shows first.
        $query = Inspection::with(['lead', 'technician'])->latest('updated_at');

        if ($user->isTechnician()) {
            $query->where('technician_id', $user->id);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        // Filter by assigned technician (CRM staff only).
        if (! $user->isTechnician() && ($techId = (int) $request->input('technician_id'))) {
            $query->where('technician_id', $techId);
        }

        // Free-text search across customer, vehicle, phone, registration and lead reference.
        if ($search = trim((string) $request->input('q'))) {
            $like = '%'.$search.'%';
            $query->where(function ($w) use ($like) {
                $w->where('customer_name', 'like', $like)
                  ->orWhere('customer_name_ar', 'like', $like)
                  ->orWhere('customer_phone', 'like', $like)
                  ->orWhere('car_make', 'like', $like)
                  ->orWhere('car_model', 'like', $like)
                  ->orWhere('plate_no', 'like', $like)
                  // Names are matched against the inspection's own columns above;
                  // the lead is only consulted for its reference (lead_unq_id).
                  ->orWhereHas('lead', fn ($l) => $l->where('lead_unq_id', 'like', $like));
            });
        }

        // Scheduled date filter:
        //  - From only        -> inspections scheduled ON or AFTER that date.
        //  - From + To        -> inspections in that date range.
        //  - To only          -> inspections scheduled ON or BEFORE that date.
        if ($from = $request->input('from')) {
            $query->whereDate('scheduled_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('scheduled_at', '<=', $to);
        }

        // When filtering by scheduled date, list chronologically (earliest first)
        // so the results read as a proper range from the "from" date; otherwise
        // keep the default newest-created-first order.
        if ($request->filled('from') || $request->filled('to')) {
            $query->reorder('scheduled_at', 'asc');
        }

        $inspections = $query->paginate(12)->withQueryString();

        // Technicians for the filter dropdown (CRM staff only; a technician sees
        // only their own inspections, so the filter is unnecessary for them).
        $technicians = $user->isTechnician()
            ? collect()
            : \DB::table('users')
                ->selectRaw('id, trim(concat(name, " ", coalesce(lname, ""))) as name')
                ->where('status', 0)
                ->where('previlage', 49)
                ->orderBy('name')
                ->get();

        return view('inspections.index', compact('inspections', 'branch', 'technicians'));
    }


    public function edit(Inspection $inspection): View
    {
        $this->authorizeInspection($inspection);

        $inspection->load([
            'lead', 'technician',
            'type.sections.steps',
            'details.media',
            'sectionSummaries',
        ]);

        // Index existing answers by step id for easy rendering.
        $answers = $inspection->details->keyBy('inspection_step_id');

        // Section-level summaries, keyed by section id for the textareas.
        $sectionSummaries = $inspection->sectionSummaries->keyBy('inspection_section_id');

        // Step-less bucket of extra/additional media.
        // The global bucket has BOTH ids null — a per-section bucket also has a
        // null step id, so it must be excluded here or its photos leak into this list.
        $extraDetail = $inspection->details
            ->first(fn ($d) => is_null($d->inspection_step_id) && is_null($d->inspection_section_id));
        $extraMedia = $extraDetail ? $extraDetail->media : collect();

        // Per-category media, keyed by section id, for the section cards.
        $sectionMedia = $inspection->details
            ->filter(fn ($d) => ! is_null($d->inspection_section_id))
            ->mapWithKeys(fn ($d) => [$d->inspection_section_id => $d->media]);

        // Technicians (previlage 49) for the assigned-technician dropdown.
        $technicians = \DB::table('users')
            ->selectRaw('id, trim(concat(name, " ", coalesce(lname, ""))) as name')
            ->where('status', 0)
            ->where('previlage', 49)
            ->orderBy('name')
            ->get();

        // Active inspection templates (plus the current one, even if inactive) for the type selector.
        $inspectionTypes = \App\Models\InspectionType::where('is_active', 1)
            ->orWhere('id', $inspection->inspection_type_id)
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();

        $lookups = $this->vehicleLookups($inspection);

        // Summary types (Exterior, Engine, Brakes, …) and this inspection's notes.
        $summaryTypes = InspectionSummary::types();
        $summaries = $inspection->summaries()->pluck('summary', 'summary_type_id')->all();

        return view('inspections.edit', compact('inspection', 'answers', 'sectionSummaries', 'technicians', 'inspectionTypes', 'extraMedia', 'sectionMedia', 'lookups', 'summaryTypes', 'summaries'));
    }

    /**
     * Dropdown options for the vehicle-detail selects, read from the same lookup
     * tables the legacy /inspectionreport form uses so both screens offer the
     * identical choices. Names (not ids) are returned — inspections stores the
     * name, so the report views can print the column without a join.
     *
     * @return array<string, array<int, string>>
     */
    private function vehicleLookups(Inspection $inspection): array
    {
        // Same range the legacy form offers (1995 .. current year), newest first.
        $years = range((int) date('Y'), 1995);

        // A stored year outside that range (legacy data) must still be selectable,
        // otherwise the select renders blank and silently clears it on save.
        foreach ([$inspection->car_year, $inspection->manufacturing_year] as $stored) {
            if ($stored && ! in_array((int) $stored, $years, true)) {
                $years[] = (int) $stored;
            }
        }
        rsort($years);

        // The table-backed selects come from VehicleLookups so this screen and the
        // API (GET /api/vehicle-lookups) always offer exactly the same choices.
        return [
            'exterior_color' => VehicleLookups::names('exterior_color'),
            'gearbox' => VehicleLookups::names('gearbox'),
            'fuel_type' => VehicleLookups::names('fuel_type'),
            'steering_side' => VehicleLookups::names('steering_side'),
            'years' => $years,
            'vehicle_condition' => ['Used', 'New'],
        ];
    }

    /**
     * Printable inspection report (SASO-style) for a completed inspection.
     */
    public function report(Inspection $inspection): View
    {
        $this->authorizeInspection($inspection);

        $inspection->load([
            'lead', 'technician', 'branch',
            'type.sections.steps',
            'details.media',
            'sectionSummaries',
        ]);

        $answers = $inspection->details->keyBy('inspection_step_id');
        $sectionSummaries = $inspection->sectionSummaries->keyBy('inspection_section_id');

        return view('inspections.report', compact('inspection', 'answers', 'sectionSummaries'));
    }

    /**
     * Dummy/preview version of the printable report — a sandbox copy used to
     * iterate on the report UI without touching the live `report` view.
     */
    public function reportPreview(Inspection $inspection): View
    {
        $this->authorizeInspection($inspection);

        $inspection->load([
            'lead', 'technician', 'branch',
            'type.sections.steps',
            'details.media',
            'sectionSummaries',
        ]);

        $answers = $inspection->details->keyBy('inspection_step_id');
        $sectionSummaries = $inspection->sectionSummaries->keyBy('inspection_section_id');

        return view('inspections.report_preview', compact('inspection', 'answers', 'sectionSummaries'));
    }

    /**
     * Full inspection details page (read-only) — customer, vehicle, verdict
     * and the complete checklist with answers and media.
     */
    public function show(Inspection $inspection): View
    {
        $this->authorizeInspection($inspection);

        $inspection->load([
            'lead', 'technician', 'branch',
            'type.sections.steps',
            'details.media',
        ]);

        $answers = $inspection->details->keyBy('inspection_step_id');
        $progress = $inspection->progress();
        $sections = $inspection->sectionProgress();

        // Inspection templates (active + this inspection's own type, even if now
        // inactive) with their full section/step tree. The Completion card and the
        // checklist are both rendered from this collection, so without it the card
        // shows 0/0 and the checklist reads "No inspection template configured".
        $inspectionTypes = \App\Models\InspectionType::with('sections.steps')
            ->where('is_active', 1)
            ->orWhere('id', $inspection->inspection_type_id)
            ->orderBy('id')
            ->get();

        return view('inspections.show', compact('inspection', 'answers', 'progress', 'sections', 'inspectionTypes'));
    }

    /**
     * Inspection summary overview — progress, overall condition and a
     * per-section breakdown (Good / Needs Attention / Not answered).
     */
    public function summary(Inspection $inspection): View
    {
        $this->authorizeInspection($inspection);

        $inspection->load([
            'lead', 'technician',
            'type.sections.steps',
            'details.media',
            'sectionSummaries',
        ]);

        $byStep = $inspection->details->keyBy('inspection_step_id');
        $summaryBySection = $inspection->sectionSummaries->keyBy('inspection_section_id');

        // Same pass/fail rule the printable report and API use.
        $stateOf = fn ($detail): string => Inspection::choiceState($detail);

        $sections = [];
        $totalSteps = 0;
        $totalAnswered = 0;
        $totalPass = 0;
        $totalFail = 0;

        foreach ($inspection->type?->sections ?? [] as $index => $section) {
            $total = $section->steps->count();
            $answered = 0;
            $fail = 0;
            $pass = 0;

            foreach ($section->steps as $step) {
                $detail = $byStep->get($step->id);
                if (! Inspection::detailIsAnswered($detail)) {
                    continue;
                }
                $answered++;
                $state = $stateOf($detail);
                if ($state === 'fail') {
                    $fail++;
                } elseif ($state === 'pass') {
                    $pass++;
                }
            }

            // Completion-based status: fully answered = Completed (green),
            // partially answered = Need Attention (yellow), none = Not answered.
            if ($answered === 0) {
                $status = 'Not answered';
            } elseif ($answered >= $total) {
                $status = 'Completed';
            } else {
                $status = 'Need Attention';
            }

            $sections[] = [
                'number' => $index + 1,
                'name' => $section->section_name,
                'total' => $total,
                'answered' => $answered,
                'fail' => $fail,
                'status' => $status,
                'summary' => optional($summaryBySection->get($section->id))->summary,
                // Dynamic rating from the section's answers; a manual rating overrides it.
                'rating' => Inspection::sectionRating(
                    $section,
                    $byStep,
                    optional($summaryBySection->get($section->id))->rating
                ),
            ];

            $totalSteps += $total;
            $totalAnswered += $answered;
            $totalPass += $pass;
            $totalFail += $fail;
        }

        // Progress bar = how many checklist questions have been answered.
        $percent = $totalSteps > 0 ? (int) round($totalAnswered / $totalSteps * 100) : 0;

        // Overall condition comes ONLY from the technician's verdict
        // (inspections.overall_condition) — it is not derived from the answers.
        $stars = 0;
        if ($inspection->overall_condition) {
            $condition = Inspection::CONDITIONS[$inspection->overall_condition] ?? ucfirst($inspection->overall_condition);
            $stars = ['excellent' => 5, 'good' => 4, 'fair' => 3, 'poor' => 2][$inspection->overall_condition] ?? 0;
        } else {
            $condition = 'Not Assessed';
        }

        $conditionNote = match ($inspection->overall_condition) {
            'excellent' => 'Vehicle is in excellent condition.',
            'good' => 'Vehicle is in good condition. Minor attention may be needed.',
            'fair' => 'Vehicle needs attention on several items.',
            'poor' => 'Vehicle requires significant attention.',
            default => 'Overall condition has not been set yet.',
        };

        $overview = [
            'condition' => $condition,
            'conditionNote' => $inspection->summary ?: $conditionNote,
            'stars' => $stars,
            'percent' => $percent,
            'completed' => $totalAnswered,
            'total' => $totalSteps,
            'allAnswered' => $totalSteps > 0 && $totalAnswered >= $totalSteps,
            'recommendation' => Inspection::RECOMMENDATIONS[$inspection->recommendation] ?? null,
        ];

        return view('inspections.summary', compact('inspection', 'overview', 'sections'));
    }

    public function start(Inspection $inspection): RedirectResponse
    {
        $this->authorizeInspection($inspection);

        $this->markStarted($inspection);
        $inspection->save();

        return redirect()->route('inspections.edit', $inspection)->with('success', 'Inspection started.');
    }

    /**
     * Auto-save a single step's answer (AJAX). Keeps progress safe mid-inspection.
     */
    public function autosaveStep(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        $data = $request->validate([
            'step_id' => ['required', 'integer'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'choice' => ['nullable', 'string', 'max:255'],
            'descriptive_answer' => ['nullable', 'string', 'max:5000'],
            'remedial_suggestion' => ['nullable', 'string', 'max:5000'],
        ]);

        $stepId = (int) $data['step_id'];
        abort_unless($this->stepBelongsToInspection($inspection, $stepId), 422, 'Unknown step.');

        InspectionDetail::updateOrCreate(
            ['inspection_id' => $inspection->id, 'inspection_step_id' => $stepId],
            [
                'rating' => $data['rating'] ?? null,
                'choice' => $data['choice'] ?? null,
                'descriptive_answer' => $data['descriptive_answer'] ?? null,
                'remedial_suggestion' => $data['remedial_suggestion'] ?? null,
            ]
        );

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['saved' => true, 'progress' => $inspection->progress()]);
    }

    /**
     * Auto-save a single section's summary note and optional rating (AJAX).
     */
    public function autosaveSectionSummary(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        $data = $request->validate([
            'section_id' => ['required', 'integer'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $sectionId = (int) $data['section_id'];

        // Only accept summaries for sections that belong to this inspection's template.
        $belongs = $inspection->type
            && $inspection->type->sections()->whereKey($sectionId)->exists();
        abort_unless($belongs, 422, 'Unknown section.');

        InspectionSectionSummary::updateOrCreate(
            ['inspection_id' => $inspection->id, 'inspection_section_id' => $sectionId],
            [
                'summary' => filled($data['summary'] ?? null) ? $data['summary'] : null,
                'rating' => $data['rating'] ?? null,
            ]
        );

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['saved' => true]);
    }

    /**
     * Auto-save the customer / vehicle snapshot (AJAX) on the initial-setup step.
     */
    public function autosaveCustomer(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_name_ar' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'car_make' => ['nullable', 'string', 'max:100'],
            'car_model' => ['nullable', 'string', 'max:100'],
            'car_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_of_inspection' => ['nullable', 'date'],
            // Extended vehicle details
            'manufacturing_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'vehicle_condition' => ['nullable', 'string', 'max:20'],
            'vin' => ['nullable', 'string', 'max:50'],
            'plate_no' => ['nullable', 'string', 'max:50'],
            'exterior_color' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:100'],
            'fuel_type' => ['nullable', 'string', 'max:30'],
            'gearbox' => ['nullable', 'string', 'max:30'],
            'cylinders' => ['nullable', 'string', 'max:50'],
            'steering_side' => ['nullable', 'string', 'max:50'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'number_of_keys' => ['nullable', 'integer', 'min:0', 'max:20'],
            'with_service_history' => ['nullable', 'boolean'],
            'last_service_date' => ['nullable', 'date'],
        ]);

        $inspection->fill($data);
        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['saved' => true]);
    }

    /**
     * Upload one photo/video for a step immediately (AJAX), returning the stored media.
     */
    public function uploadMedia(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        $data = $request->validate([
            'step_id' => ['required', 'integer'],
            'type' => ['required', 'in:photo,video'],
            'file' => ['required', 'file', 'max:102400'],
        ]);

        $stepId = (int) $data['step_id'];
        abort_unless($this->stepBelongsToInspection($inspection, $stepId), 422, 'Unknown step.');

        $detail = InspectionDetail::firstOrCreate([
            'inspection_id' => $inspection->id,
            'inspection_step_id' => $stepId,
        ]);

        $file = $request->file('file');
        $path = $file->store("inspections/{$inspection->id}/{$data['type']}s", 'public');

        $media = $detail->media()->create([
            'type' => $data['type'],
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['id' => $media->id, 'type' => $media->type, 'url' => $media->url]);
    }

    /**
     * Upload an extra photo/video not tied to a checklist step (AJAX).
     * Stored against a single step-less "additional media" detail bucket.
     */
    public function uploadExtraMedia(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        $data = $request->validate([
            'type' => ['required', 'in:photo,video'],
            'file' => ['required', 'file', 'max:102400'],
        ]);

        $detail = InspectionDetail::firstOrCreate([
            'inspection_id' => $inspection->id,
            'inspection_step_id' => null,
            'inspection_section_id' => null,
        ]);

        $file = $request->file('file');
        $path = $file->store("inspections/{$inspection->id}/extra/{$data['type']}s", 'public');

        $media = $detail->media()->create([
            'type' => $data['type'],
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['id' => $media->id, 'type' => $media->type, 'url' => $media->url, 'label' => $media->label]);
    }

    /**
     * Upload one photo/video against a category (section) rather than a single
     * question. The browser sends one request per selected file, so a multi-file
     * pick lands as several calls against the same section bucket.
     */
    public function uploadSectionMedia(Request $request, Inspection $inspection, InspectionSection $section): JsonResponse
    {
        $this->authorizeInspection($inspection);

        // The section must belong to this inspection's template.
        abort_unless((int) $section->inspection_type_id === (int) $inspection->inspection_type_id, 404);

        $data = $request->validate([
            'type' => ['required', 'in:photo,video'],
            'file' => ['required', 'file', 'max:102400'],
        ]);

        $detail = InspectionDetail::firstOrCreate([
            'inspection_id' => $inspection->id,
            'inspection_step_id' => null,
            'inspection_section_id' => $section->id,
        ]);

        $file = $request->file('file');
        $path = $file->store("inspections/{$inspection->id}/sections/{$section->id}/{$data['type']}s", 'public');

        $media = $detail->media()->create([
            'type' => $data['type'],
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['id' => $media->id, 'type' => $media->type, 'url' => $media->url, 'label' => $media->label]);
    }

    /**
     * Save/update the descriptive label of a media item (AJAX).
     */
    public function updateMediaLabel(Request $request, InspectionMedia $media): JsonResponse
    {
        $this->authorizeInspection($media->detail->inspection);

        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $media->update(['label' => $data['label'] ?? null]);

        return response()->json(['saved' => true]);
    }

    private function markStarted(Inspection $inspection): void
    {
        if ($inspection->status === Inspection::STATUS_PENDING) {
            $inspection->status = Inspection::STATUS_IN_PROGRESS;
            $inspection->started_at ??= now();
            $inspection->lead?->update(['status' => Lead::STATUS_IN_PROGRESS]);
        }
    }

    private function stepBelongsToInspection(Inspection $inspection, int $stepId): bool
    {
        return in_array($stepId, $inspection->type->steps()->pluck('inspection_steps.id')->all(), true);
    }

    public function update(Request $request, Inspection $inspection): RedirectResponse
    {
        $this->authorizeInspection($inspection);

        $validated = $request->validate([
            // Customer / vehicle snapshot (editable)
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_name_ar' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'car_make' => ['nullable', 'string', 'max:100'],
            'car_model' => ['nullable', 'string', 'max:100'],
            'car_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
            'inspection_type_id' => ['nullable', 'integer', 'exists:inspection_types,id'],
            'date_of_inspection' => ['nullable', 'date'],
            // Extended vehicle details
            'manufacturing_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'vehicle_condition' => ['nullable', 'string', 'max:20'],
            'vin' => ['nullable', 'string', 'max:50'],
            'plate_no' => ['nullable', 'string', 'max:50'],
            'exterior_color' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:100'],
            'fuel_type' => ['nullable', 'string', 'max:30'],
            'gearbox' => ['nullable', 'string', 'max:30'],
            'cylinders' => ['nullable', 'string', 'max:50'],
            'steering_side' => ['nullable', 'string', 'max:50'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'number_of_keys' => ['nullable', 'integer', 'min:0', 'max:20'],
            'with_service_history' => ['nullable', 'boolean'],
            'last_service_date' => ['nullable', 'date'],
            // Overall verdict
            'odometer' => ['nullable', 'integer', 'min:0'],
            'overall_condition' => ['nullable', 'in:'.implode(',', array_keys(Inspection::CONDITIONS))],
            'recommendation' => ['nullable', 'in:'.implode(',', array_keys(Inspection::RECOMMENDATIONS))],
            'estimated_repair_cost' => ['nullable', 'numeric', 'min:0'],
            'summary' => ['nullable', 'string', 'max:5000'],
            // Per-step answers
            'answers' => ['nullable', 'array'],
            'answers.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'answers.*.choice' => ['nullable', 'string', 'max:255'],
            'answers.*.text' => ['nullable', 'string', 'max:5000'],
            'answers.*.remedial' => ['nullable', 'string', 'max:5000'],
            // Per-section summaries (section id => text) and optional ratings (section id => 1-5)
            'section_summaries' => ['nullable', 'array'],
            'section_summaries.*' => ['nullable', 'string', 'max:5000'],
            // Per-summary-type notes (Exterior, Engine, Brakes, …)
            'summaries' => ['nullable', 'array'],
            'summaries.*' => ['nullable', 'string', 'max:5000'],
            'section_ratings' => ['nullable', 'array'],
            'section_ratings.*' => ['nullable', 'integer', 'min:1', 'max:5'],
            // Media
            'photos.*.*' => ['nullable', 'image', 'max:10240'],
            'videos.*.*' => ['nullable', 'mimetypes:video/mp4,video/quicktime,video/x-matroska', 'max:102400'],
        ]);

        // Capture the steps of the type the form was rendered with, BEFORE any type
        // change, so the submitted answers are saved against the right steps.
        $stepIds = $inspection->type ? $inspection->type->steps()->pluck('inspection_steps.id')->all() : [];
        $prevTypeId = (int) $inspection->inspection_type_id;

        $inspection->fill([
            'customer_name' => $validated['customer_name'],
            'customer_name_ar' => $validated['customer_name_ar'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'car_make' => $validated['car_make'] ?? null,
            'car_model' => $validated['car_model'] ?? null,
            'car_year' => $validated['car_year'] ?? null,
            'odometer' => $validated['odometer'] ?? null,
            'overall_condition' => $validated['overall_condition'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'estimated_repair_cost' => $validated['estimated_repair_cost'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'date_of_inspection' => $validated['date_of_inspection'] ?? null,
            // Extended vehicle details
            'manufacturing_year' => $validated['manufacturing_year'] ?? null,
            'vehicle_condition' => $validated['vehicle_condition'] ?? null,
            'vin' => $validated['vin'] ?? null,
            'plate_no' => $validated['plate_no'] ?? null,
            'exterior_color' => $validated['exterior_color'] ?? null,
            'region' => $validated['region'] ?? null,
            'fuel_type' => $validated['fuel_type'] ?? null,
            'gearbox' => $validated['gearbox'] ?? null,
            'cylinders' => $validated['cylinders'] ?? null,
            'steering_side' => $validated['steering_side'] ?? null,
            'body_type' => $validated['body_type'] ?? null,
            'number_of_keys' => $validated['number_of_keys'] ?? null,
            'with_service_history' => $validated['with_service_history'] ?? null,
            'last_service_date' => $validated['last_service_date'] ?? null,
        ]);

        if (! empty($validated['technician_id'])) {
            $inspection->technician_id = $validated['technician_id'];
        }

        $typeChanged = ! empty($validated['inspection_type_id'])
            && (int) $validated['inspection_type_id'] !== $prevTypeId;

        if (! empty($validated['inspection_type_id'])) {
            $inspection->inspection_type_id = $validated['inspection_type_id'];
        }

        // Switching the template invalidates any completion — the new checklist
        // hasn't been filled, so this save can never leave it completed.
        if ($typeChanged) {
            if ($inspection->status === Inspection::STATUS_COMPLETED) {
                $inspection->completed_at = null;
            }
            $inspection->status = Inspection::STATUS_IN_PROGRESS;
            $inspection->started_at ??= now();
            $inspection->lead?->update(['status' => Lead::STATUS_IN_PROGRESS]);
        }

        if ($inspection->status === Inspection::STATUS_PENDING) {
            $inspection->status = Inspection::STATUS_IN_PROGRESS;
            $inspection->started_at ??= now();
            $inspection->lead?->update(['status' => Lead::STATUS_IN_PROGRESS]);
        }

        // Persist per-step answers (against the type the form was rendered with).
        $answers = $request->input('answers', []);

        foreach ($stepIds as $stepId) {
            $a = $answers[$stepId] ?? [];
            $hasAnswer = ! empty($a['rating']) || ! empty($a['choice'])
                || filled($a['text'] ?? null) || filled($a['remedial'] ?? null);
            $hasUpload = $request->hasFile("photos.$stepId") || $request->hasFile("videos.$stepId");

            if (! $hasAnswer && ! $hasUpload) {
                continue;
            }

            $detail = InspectionDetail::updateOrCreate(
                ['inspection_id' => $inspection->id, 'inspection_step_id' => $stepId],
                [
                    'rating' => $a['rating'] ?? null,
                    'choice' => $a['choice'] ?? null,
                    'descriptive_answer' => $a['text'] ?? null,
                    'remedial_suggestion' => $a['remedial'] ?? null,
                ]
            );

            $this->storeUploads($request, $inspection, $detail, $stepId);
        }

        // Persist per-section summaries and optional ratings (only for sections
        // that belong to the template the form was rendered with).
        $sectionSummaries = (array) $request->input('section_summaries', []);
        $sectionRatings = (array) $request->input('section_ratings', []);
        $formSectionIds = array_unique(array_merge(array_keys($sectionSummaries), array_keys($sectionRatings)));
        if ($formSectionIds) {
            $validSectionIds = $inspection->type
                ? $inspection->type->sections()->pluck('id')->all()
                : [];

            foreach ($formSectionIds as $sectionId) {
                if (! in_array((int) $sectionId, $validSectionIds, true)) {
                    continue;
                }

                $text = $sectionSummaries[$sectionId] ?? null;
                $rating = $sectionRatings[$sectionId] ?? null;

                InspectionSectionSummary::updateOrCreate(
                    ['inspection_id' => $inspection->id, 'inspection_section_id' => (int) $sectionId],
                    [
                        'summary' => filled($text) ? $text : null,
                        'rating' => filled($rating) ? (int) $rating : null,
                    ]
                );
            }
        }

        // Per-summary-type notes shown under the Overall Verdict. Unknown type ids
        // are ignored; a cleared box removes the row rather than storing "".
        $typeSummaries = (array) $request->input('summaries', []);
        if ($typeSummaries) {
            $validTypeIds = array_keys(InspectionSummary::types());

            foreach ($typeSummaries as $typeId => $text) {
                if (! in_array((int) $typeId, $validTypeIds, true)) {
                    continue;
                }

                if (filled($text)) {
                    InspectionSummary::updateOrCreate(
                        ['inspection_id' => $inspection->id, 'summary_type_id' => (int) $typeId],
                        ['summary' => $text]
                    );
                } else {
                    InspectionSummary::where('inspection_id', $inspection->id)
                        ->where('summary_type_id', (int) $typeId)
                        ->delete();
                }
            }
        }

        // Completion (with mandatory-media check). Skipped when the template was just
        // changed — the new checklist still needs to be completed first.
        $completing = $request->boolean('complete') && ! $typeChanged;

        if ($completing) {
            // Every templated question must be answered before completion.
            $inspection->load(['type.sections.steps', 'details']);

            if (! $inspection->isFullyAnswered()) {
                $inspection->save();

                $pending = collect($inspection->sectionProgress())->reject(fn ($s) => $s['done']);
                $names = $pending->take(4)->map(fn ($s) => $s['name'].' ('.$s['answered'].'/'.$s['total'].')')->implode(', ');
                $more = $pending->count() > 4 ? ' …and '.($pending->count() - 4).' more' : '';

                return back()->withErrors(['complete' => 'Cannot complete — answer all questions first. Pending: '.$names.$more.'.']);
            }

            if ($missing = $inspection->missingMandatoryMedia()) {
                $inspection->save();

                return back()->withErrors(['complete' => 'Cannot complete — mandatory media missing for: '.implode(', ', $missing).'.']);
            }

            $inspection->status = Inspection::STATUS_COMPLETED;
            $inspection->completed_at = now();
            $inspection->lead?->update(['status' => Lead::STATUS_COMPLETED]);
        }

        $inspection->save();

        return redirect()->route('inspections.edit', $inspection)
            ->with('success', $completing ? 'Inspection completed.' : ($typeChanged ? 'Inspection template updated.' : 'Inspection saved.'));
    }

    public function destroyMedia(Request $request, InspectionMedia $media): RedirectResponse|JsonResponse
    {
        $inspection = $media->detail->inspection;
        $this->authorizeInspection($inspection);

        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        if ($request->expectsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Media removed.');
    }

    private function storeUploads(Request $request, Inspection $inspection, InspectionDetail $detail, int $stepId): void
    {
        foreach (['photos' => 'photo', 'videos' => 'video'] as $field => $type) {
            foreach ((array) $request->file("$field.$stepId", []) as $file) {
                if (! $file) {
                    continue;
                }
                $path = $file->store("inspections/{$inspection->id}/{$type}s", 'public');
                $detail->media()->create([
                    'type' => $type,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }
    }

    private function authorizeInspection(Inspection $inspection): void
    {
        // Technicians (app/API only) may touch only their own inspections;
        // CRM staff may manage any inspection.
        $user = request()->user();
        if ($user->isTechnician()) {
            abort_unless($inspection->technician_id === $user->id, 403);
        }
    }
}
