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
        // Build a make → [models] map for client-side filtering, using the same
        // active/published filters the VehicleLookups class applies.
        $activeModels = \DB::table('tbl_model')
            ->where('model_status', 0)
            ->where('model_publish_status', 1)
            ->select('model_name', 'model_make')
            ->get();
        $activeMakes  = VehicleLookups::options('car_make');
        $makeIdToName = collect($activeMakes)->pluck('name', 'id');
        $modelsByMake = [];
        foreach ($activeModels as $m) {
            $mn = $makeIdToName[$m->model_make] ?? null;
            if ($mn) $modelsByMake[$mn][] = $m->model_name;
        }

        return [
            'car_make' => VehicleLookups::names('car_make'),
            'car_model' => VehicleLookups::names('car_model'),
            'modelsByMake' => $modelsByMake,
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
    public function report(Inspection $inspection): View|RedirectResponse
    {
        $this->authorizeInspection($inspection);

        // A cancelled inspection has no report — it was never finished.
        if ($inspection->isCancelled()) {
            return back()->with('error', 'This inspection is cancelled — no report is available for it.');
        }

        $inspection->load([
            'lead', 'technician', 'branch',
            'type.sections.steps',
            'details.media',
            'sectionSummaries',
            'summaries',
        ]);

        $answers = $inspection->details->keyBy('inspection_step_id');
        $sectionSummaries = $inspection->sectionSummaries->keyBy('inspection_section_id');

        // Per-area summary notes (Exterior, Engine, …) shown in their own report section.
        $summaryTypes = InspectionSummary::types();
        $summaries = $inspection->summaries->pluck('summary', 'summary_type_id')->all();

        return view('inspections.report', compact('inspection', 'answers', 'sectionSummaries', 'summaryTypes', 'summaries'));
    }

    /**
     * Dummy/preview version of the printable report — a sandbox copy used to
     * iterate on the report UI without touching the live `report` view.
     */
    public function reportPreview(Inspection $inspection): View|RedirectResponse
    {
        $this->authorizeInspection($inspection);

        // A cancelled inspection has no report — it was never finished.
        if ($inspection->isCancelled()) {
            return back()->with('error', 'This inspection is cancelled — no report is available for it.');
        }

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
            'summaries',
            'sectionSummaries',
        ]);

        $answers = $inspection->details->keyBy('inspection_step_id');
        $progress = $inspection->progress();
        $sections = $inspection->sectionProgress();

        // Section-level summaries/ratings, keyed by section id.
        $sectionSummaries = $inspection->sectionSummaries->keyBy('inspection_section_id');

        // Summary types (Exterior, Engine, Brakes, …) and this inspection's notes.
        $summaryTypes = InspectionSummary::types();
        $summaries = $inspection->summaries->pluck('summary', 'summary_type_id')->all();

        // Inspection templates (active + this inspection's own type, even if now
        // inactive) with their full section/step tree. The Completion card and the
        // checklist are both rendered from this collection, so without it the card
        // shows 0/0 and the checklist reads "No inspection template configured".
        $inspectionTypes = \App\Models\InspectionType::with('sections.steps')
            ->where('is_active', 1)
            ->orWhere('id', $inspection->inspection_type_id)
            ->orderBy('id')
            ->get();

        return view('inspections.show', compact('inspection', 'answers', 'progress', 'sections', 'inspectionTypes', 'summaryTypes', 'summaries', 'sectionSummaries'));
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

        // A rating the technician recorded on the edit screen, kept to one decimal
        // (4.6 stays 4.6); null when the section was never rated.
        $recordedRating = fn ($manual): ?float => filled($manual) && (float) $manual > 0
            ? round(max(0.5, min(5, (float) $manual)), 1)
            : null;

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
            // partially answered = In Progress (yellow), none = Not answered.
            // These describe data entry, not vehicle condition — a section can
            // be Completed and still contain failed items.
            if ($answered === 0) {
                $status = 'Not answered';
            } elseif ($answered >= $total) {
                $status = 'Completed';
            } else {
                $status = 'In Progress';
            }

            $sections[] = [
                'number' => $index + 1,
                'name' => $section->section_name,
                'total' => $total,
                'answered' => $answered,
                'fail' => $fail,
                'status' => $status,
                'summary' => optional($summaryBySection->get($section->id))->summary,
                // Only the star actually set on the edit screen (section_ratings[]).
                // Deliberately NOT Inspection::sectionRating(), whose fallback derived
                // a score from the answers and so printed stars for sections nobody
                // rated. No rating given = no stars. Same rule as the report and the API.
                'rating' => $recordedRating(optional($summaryBySection->get($section->id))->rating),
            ];

            $totalSteps += $total;
            $totalAnswered += $answered;
            $totalPass += $pass;
            $totalFail += $fail;
        }

        // Progress bar = how many checklist questions have been answered.
        $percent = $totalSteps > 0 ? (int) round($totalAnswered / $totalSteps * 100) : 0;

        // Overall condition comes from the technician's star rating
        // (inspections.overall_rating).
        $rating = (float) ($inspection->overall_rating ?? 0);
        if ($rating > 0) {
            $condition = match (true) {
                $rating >= 4.5 => 'Excellent',
                $rating >= 3.5 => 'Very Good',
                $rating >= 2.5 => 'Good',
                $rating >= 1.5 => 'Fair',
                default        => 'Poor',
            };
            $stars = match (true) {
                $rating >= 4.5 => 5,
                $rating >= 3.5 => 4,
                $rating >= 2.5 => 3,
                $rating >= 1.5 => 2,
                default        => 1,
            };
        } else {
            $condition = 'Not Assessed';
            $stars = 0;
        }

        $conditionNote = match ($condition) {
            'Excellent' => 'Vehicle is in excellent condition.',
            'Very Good' => 'Vehicle is in very good condition.',
            'Good' => 'Vehicle is in good condition. Minor attention may be needed.',
            'Fair' => 'Vehicle needs attention on several items.',
            'Poor' => 'Vehicle requires significant attention.',
            default => 'Overall condition has not been set yet.',
        };

        // Rating as a percentage (same formula as the edit page & report).
        $ratingPercent = $rating > 0 ? (int) round(($rating / 5) * 100) : 0;

        $overview = [
            'condition' => $condition,
            'conditionNote' => $inspection->summary ?: $conditionNote,
            'stars' => $stars,
            'percent' => $percent,
            'ratingPercent' => $ratingPercent,
            'completed' => $totalAnswered,
            'total' => $totalSteps,
            'allAnswered' => $totalSteps > 0 && $totalAnswered >= $totalSteps,
            'recommendation' => Inspection::RECOMMENDATIONS[$inspection->recommendation] ?? null,
            'overall_rating' => $inspection->overall_rating,
        ];

        return view('inspections.summary', compact('inspection', 'overview', 'sections'));
    }

    public function start(Inspection $inspection): RedirectResponse
    {
        $this->authorizeInspection($inspection);

        if ($inspection->isCancelled()) {
            return back()->with('error', 'This inspection is cancelled and cannot be started.');
        }

        $this->markStarted($inspection);
        $inspection->save();

        return redirect()->route('inspections.edit', $inspection)->with('success', 'Inspection started.');
    }

    /**
     * Cancel an inspection (admin only). Records who cancelled it, when, and the
     * reason, and marks the lead "Inspection Cancelled" so the lead screens show
     * it. The inspection can be re-opened later by assigning it again from the lead.
     */
    public function cancel(Request $request, Inspection $inspection): RedirectResponse
    {
        $user = $request->user();

        // Admin-only. The assigned technician is notified, not authorised.
        abort_unless($user?->isAdmin(), 403, 'Only an admin can cancel an inspection.');

        if ($inspection->isCancelled()) {
            return back()->with('error', 'This inspection is already cancelled.');
        }

        // Cancellable while the job is still open — pending or in progress.
        // A completed inspection is locked, the same as everywhere else.
        if (! $inspection->isCancellable()) {
            return back()->with('error', 'A completed inspection cannot be cancelled.');
        }

        // Same rules the modal enforces client-side, so a bypassed form fails here too.
        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'cancel_reason.required' => 'Please give a reason for cancelling this inspection.',
            'cancel_reason.min' => 'The reason must be at least 5 characters.',
        ]);

        $inspection->fill([
            'status' => Inspection::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancel_reason' => trim($data['cancel_reason']),
            'cancelled_by' => $request->user()->id,
        ])->save();

        // Mirror it on the lead so the lead list/detail show the cancellation.
        $inspection->lead?->update(['status' => Lead::STATUS_CANCELLED]);

        // Tell the assigned technician (stored notification + FCM push).
        $inspection->notifyCancelled($user->id);

        return back()->with('success', 'Inspection cancelled.');
    }

    /**
     * Auto-save a single step's answer (AJAX). Keeps progress safe mid-inspection.
     */
    public function autosaveStep(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        $data = $request->validate([
            'section_id' => ['required', 'integer'],
            'summary' => ['nullable', 'string', 'max:5000'],
            // Decimal to one place, e.g. 0.5 / 4.6. Stored in a decimal(2,1).
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_name_ar' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'car_make' => ['nullable', 'string', 'max:100'],
            'car_model' => ['nullable', 'string', 'max:100'],
            'car_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_of_inspection' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            // Extended vehicle details
            'manufacturing_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'vehicle_condition' => ['nullable', 'string', 'max:20'],
            'vin' => ['nullable', 'string', 'max:50'],
            'plate_no' => ['nullable', 'string', 'max:50'],
            'exterior_color' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:100'],
            'fuel_type' => ['nullable', 'string', 'max:30'],
            'gearbox' => ['nullable', 'string', 'max:30'],
            'steering_side' => ['nullable', 'string', 'max:50'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'number_of_keys' => ['nullable', 'integer', 'min:0', 'max:20'],
            'with_service_history' => ['nullable', 'boolean'],
            'last_service_date' => ['nullable', 'date'],
        ]);

        // Captured before the fill so a reassignment / reschedule can be reported.
        // This endpoint — not update() — is what the edit screen posts to for the
        // Customer & Vehicle card, so the notifications have to fire here too.
        $prevTechnicianId = (int) $inspection->technician_id;
        $prevScheduledAt = $inspection->scheduled_at ? $inspection->scheduled_at->copy() : null;

        $inspection->fill($data);
        $this->markStarted($inspection);
        $inspection->save();

        $this->notifyAssignmentChanges($inspection, $prevTechnicianId, $prevScheduledAt, $request->user()?->id);

        return response()->json(['saved' => true]);
    }

    /**
     * Fire the technician-facing notifications after an inspection is saved:
     * a reassignment when the technician changed, otherwise a reschedule when the
     * slot moved. Never both — a reassignment message already carries the new
     * schedule's context, and two pushes for one action reads as a bug.
     */
    private function notifyAssignmentChanges(
        Inspection $inspection,
        int $prevTechnicianId,
        $prevScheduledAt,
        ?int $actorId
    ): void {
        $technicianChanged = (int) $inspection->technician_id !== $prevTechnicianId;

        if ($technicianChanged) {
            $inspection->notifyReassigned($actorId);

            return;
        }

        $moved = optional($inspection->scheduled_at)->format('Y-m-d H:i')
            !== optional($prevScheduledAt)->format('Y-m-d H:i');

        if ($moved) {
            $inspection->notifyRescheduled($prevScheduledAt, $actorId);
        }
    }

    /**
     * Upload one photo/video for a step immediately (AJAX), returning the stored media.
     */
    public function uploadMedia(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        // PHP silently discards an upload bigger than upload_max_filesize/
        // post_max_size — the file never reaches Laravel, so plain validation
        // reports the useless "file is required". Say what actually happened.
        if (! $request->hasFile('file') && $this->uploadExceededPhpLimit($request)) {
            return response()->json([
                'message' => 'That file is larger than the server allows ('.ini_get('upload_max_filesize').' per file, '.ini_get('post_max_size').' per request). Ask your administrator to raise upload_max_filesize and post_max_size.',
            ], 422);
        }

        $data = $request->validate([
            'type' => ['required', 'in:photo,video,document'],
            'file' => ['required', 'file', 'max:102400'],
        ]);

        // Trust the file over the posted type: a PDF picked in the "photos" box
        // must still be stored as a document, or it renders as a broken image.
        $upload = $request->file('file');
        $ext = strtolower((string) $upload->getClientOriginalExtension());

        if ($upload->getClientMimeType() === 'application/pdf'
            || in_array($ext, InspectionMedia::DOCUMENT_EXTENSIONS, true)) {
            $data['type'] = 'document';

            // PDFs are capped tighter than photos/videos.
            if ($upload->getSize() > InspectionMedia::MAX_DOCUMENT_BYTES) {
                return response()->json([
                    'message' => InspectionMedia::documentTooLargeMessage(
                        $upload->getClientOriginalName(), (int) $upload->getSize()
                    ),
                ], 422);
            }
        }

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

        return response()->json(['id' => $media->id, 'type' => $media->type, 'url' => $media->url, 'label' => $media->label, 'original_name' => $media->original_name]);
    }

    /**
     * Upload one photo/video against a category (section) rather than a single
     * question. The browser sends one request per selected file, so a multi-file
     * pick lands as several calls against the same section bucket.
     */
    public function uploadSectionMedia(Request $request, Inspection $inspection, InspectionSection $section): JsonResponse
    {
        $this->authorizeInspection($inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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

        return response()->json(['id' => $media->id, 'type' => $media->type, 'url' => $media->url, 'label' => $media->label, 'original_name' => $media->original_name]);
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

        // A cancelled inspection is read-only until it is re-opened from the lead.
        if ($inspection->isCancelled()) {
            return back()->with('error', 'This inspection is cancelled and kept as a record. Assign the lead again to start a new inspection.');
        }

        $validated = $request->validate([
            // Customer / vehicle snapshot (editable)
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_name_ar' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'car_make' => ['nullable', 'string', 'max:100'],
            'car_model' => ['nullable', 'string', 'max:100'],
            'car_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
            'inspection_type_id' => ['nullable', 'integer', 'exists:inspection_types,id'],
            'date_of_inspection' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            // Extended vehicle details
            'manufacturing_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'vehicle_condition' => ['nullable', 'string', 'max:20'],
            'vin' => ['nullable', 'string', 'max:50'],
            'plate_no' => ['nullable', 'string', 'max:50'],
            'exterior_color' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:100'],
            'fuel_type' => ['nullable', 'string', 'max:30'],
            'gearbox' => ['nullable', 'string', 'max:30'],
            'steering_side' => ['nullable', 'string', 'max:50'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'number_of_keys' => ['nullable', 'integer', 'min:0', 'max:20'],
            'with_service_history' => ['nullable', 'boolean'],
            'last_service_date' => ['nullable', 'date'],
            // Overall verdict
            'odometer' => ['nullable', 'integer', 'min:0'],
            'overall_condition' => ['nullable', 'in:'.implode(',', array_keys(Inspection::CONDITIONS))],
            'overall_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'recommendation' => ['nullable', 'in:'.implode(',', array_keys(Inspection::RECOMMENDATIONS))],
            'estimated_repair_cost' => ['nullable', 'string', 'max:50'],
            'currency' => ['nullable', 'string', 'max:10'],
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
            // Decimal to one place, e.g. 0.5 / 4.6. Stored in a decimal(2,1).
            'section_ratings.*' => ['nullable', 'numeric', 'min:0', 'max:5'],
            // Media
            'photos.*.*' => ['nullable', 'image', 'max:10240'],
            'videos.*.*' => ['nullable', 'mimetypes:video/mp4,video/quicktime,video/x-matroska', 'max:102400'],
        ]);

        // Capture the steps of the type the form was rendered with, BEFORE any type
        // change, so the submitted answers are saved against the right steps.
        $stepIds = $inspection->type ? $inspection->type->steps()->pluck('inspection_steps.id')->all() : [];
        $prevTypeId = (int) $inspection->inspection_type_id;
        $prevTechnicianId = (int) $inspection->technician_id;
        // Kept so a schedule change can be reported to the technician after save.
        $prevScheduledAt = $inspection->scheduled_at ? $inspection->scheduled_at->copy() : null;
        // A completed inspection is locked — its technician can no longer change.
        $wasCompleted = $inspection->status === Inspection::STATUS_COMPLETED;

        // Decided before the fill below, because a template change must leave the
        // Customer & Vehicle card (the first wizard step) completely untouched.
        $typeChanged = ! $wasCompleted
            && ! empty($validated['inspection_type_id'])
            && (int) $validated['inspection_type_id'] !== $prevTypeId;

        // Only cards 2..N are replaced by a template switch. The Customer & Vehicle
        // card is left exactly as stored: those fields are already persisted by
        // the per-field autosave, and re-applying the posted form here would blank
        // any select the browser submitted empty — car_model is rendered with no
        // options at all (its list is injected by JS from the chosen make), so a
        // template-change save was wiping the make and model.
        if (! $typeChanged) {
        $inspection->fill([
            'customer_name' => $validated['customer_name'],
            'customer_name_ar' => $validated['customer_name_ar'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'car_make' => $validated['car_make'] ?? null,
            'car_model' => $validated['car_model'] ?? null,
            'car_year' => $validated['car_year'] ?? null,
            'odometer' => $validated['odometer'] ?? null,
            'overall_condition' => $validated['overall_condition'] ?? null,
            'overall_rating' => $validated['overall_rating'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'estimated_repair_cost' => $validated['estimated_repair_cost'] ?? null,
            'currency' => $validated['currency'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'date_of_inspection' => $validated['date_of_inspection'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? $inspection->scheduled_at,
            // Extended vehicle details
            'manufacturing_year' => $validated['manufacturing_year'] ?? null,
            'vehicle_condition' => $validated['vehicle_condition'] ?? null,
            'vin' => $validated['vin'] ?? null,
            'plate_no' => $validated['plate_no'] ?? null,
            'exterior_color' => $validated['exterior_color'] ?? null,
            'region' => $validated['region'] ?? null,
            'fuel_type' => $validated['fuel_type'] ?? null,
            'gearbox' => $validated['gearbox'] ?? null,
            'steering_side' => $validated['steering_side'] ?? null,
            'body_type' => $validated['body_type'] ?? null,
            'number_of_keys' => $validated['number_of_keys'] ?? null,
            'with_service_history' => $validated['with_service_history'] ?? null,
            'last_service_date' => $validated['last_service_date'] ?? null,
        ]);
        }

        // Technician can only be (re)assigned while the inspection isn't completed.
        if (! $wasCompleted && ! empty($validated['technician_id'])) {
            $inspection->technician_id = $validated['technician_id'];
        }

        // The template is likewise locked once completed — the edit screen renders
        // it read-only, and this makes that real rather than cosmetic: a hand-rolled
        // POST cannot swap the checklist out from under a finished report.
        if (! $wasCompleted && ! empty($validated['inspection_type_id'])) {
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

            // The recorded answers belong to the OLD checklist — its steps and
            // sections don't exist in the new template, so they can never be
            // shown or edited again. Clear them so the inspection genuinely
            // restarts, rather than leaving dead rows behind. The user is warned
            // and has to confirm before this save is submitted.
            $this->resetChecklistData($inspection);
        }

        if ($inspection->status === Inspection::STATUS_PENDING) {
            $inspection->status = Inspection::STATUS_IN_PROGRESS;
            $inspection->started_at ??= now();
            $inspection->lead?->update(['status' => Lead::STATUS_IN_PROGRESS]);
        }

        // Persist per-step answers (against the type the form was rendered with).
        // Skipped entirely on a template change: the submitted answers belong to
        // the OLD checklist that resetChecklistData() just cleared, so writing
        // them back would immediately re-create the rows it deleted.
        $answers = $typeChanged ? [] : $request->input('answers', []);

        foreach ($typeChanged ? [] : $stepIds as $stepId) {
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
        // Same reasoning as the answers above — these are keyed by the old
        // template's section ids.
        $sectionSummaries = $typeChanged ? [] : (array) $request->input('section_summaries', []);
        $sectionRatings = $typeChanged ? [] : (array) $request->input('section_ratings', []);
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
                        'rating' => filled($rating) ? round((float) $rating, 1) : null,
                    ]
                );
            }
        }

        // Per-summary-type notes shown under the Overall Verdict. Unknown type ids
        // are ignored; a cleared box removes the row rather than storing "".
        // Skipped on a template change — these notes describe the discarded
        // checklist and were just cleared by resetChecklistData().
        $typeSummaries = $typeChanged ? [] : (array) $request->input('summaries', []);
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

            // The Overall Verdict block drives the report headline and cannot be
            // derived from the answers, so it has to be filled in by hand.
            // A cost of 0 is a real answer (nothing to repair); only a null/empty
            // box counts as missing, which is exactly what blank() tests.
            $verdictMissing = [];
            if (blank($inspection->estimated_repair_cost)) { $verdictMissing[] = 'Est. repair cost'; }
            if (blank($inspection->recommendation))        { $verdictMissing[] = 'Recommendation'; }
            if (blank($inspection->summary))               { $verdictMissing[] = 'Technician note'; }

            if ($verdictMissing !== []) {
                $inspection->save();

                return back()->withErrors(['complete' => 'Cannot complete — fill the Overall Verdict first. Missing: '.implode(', ', $verdictMissing).'.']);
            }

            // Every summary area (Exterior, Engine, Brakes, …) needs its note —
            // the report prints one per area, and the technician API enforces
            // the same "a note for every area" rule.
            $noted = $inspection->summaries()
                ->pluck('summary', 'summary_type_id')
                ->filter(fn ($text) => filled($text));

            $missingNotes = collect(InspectionSummary::types())
                ->reject(fn ($name, $typeId) => $noted->has($typeId))
                ->values();

            if ($missingNotes->isNotEmpty()) {
                $inspection->save();

                $names = $missingNotes->take(4)->implode(', ');
                $more = $missingNotes->count() > 4 ? ' …and '.($missingNotes->count() - 4).' more' : '';

                return back()->withErrors(['complete' => 'Cannot complete — add a Summary note for every area. Missing: '.$names.$more.'.']);
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

        // Reassigned, or the slot moved — notify the technician. Same helper the
        // autosave endpoint uses, so both save paths behave identically.
        $this->notifyAssignmentChanges($inspection, $prevTechnicianId, $prevScheduledAt, $request->user()?->id);

        if ($completing) {
            return redirect()->route('inspections.summary', $inspection)
                ->with('success', 'Inspection completed.');
        }

        return redirect()->route('inspections.edit', $inspection)
            ->with('success', $typeChanged ? 'Inspection template updated.' : 'Inspection saved.');
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

    /**
     * Wipe everything tied to the previous checklist when the template changes:
     * every step answer, every section-level media bucket and every per-section
     * summary, along with the files behind them.
     *
     * Deliberately kept: the customer & vehicle details (the first wizard step),
     * the additional-media bucket (step_id AND section_id both null — it belongs
     * to the inspection, not the checklist), the vehicle image and the verdict.
     */
    private function resetChecklistData(Inspection $inspection): void
    {
        // Anything bound to a step or a section — i.e. everything except the
        // step-less/section-less additional-media bucket.
        $details = InspectionDetail::where('inspection_id', $inspection->id)
            ->where(function ($q) {
                $q->whereNotNull('inspection_step_id')
                  ->orWhereNotNull('inspection_section_id');
            })
            ->with('media')
            ->get();

        foreach ($details as $detail) {
            foreach ($detail->media as $media) {
                Storage::disk($media->disk)->delete($media->path);
                $media->delete();
            }
            $detail->delete();
        }

        // Per-section notes/ratings reference the old template's sections.
        InspectionSectionSummary::where('inspection_id', $inspection->id)->delete();

        // Per-area notes (Exterior, Engine, Brakes, …). The areas themselves are
        // template-independent, but the notes describe the checklist that was
        // just discarded.
        InspectionSummary::where('inspection_id', $inspection->id)->delete();

        // The overall verdict summarises the old checklist — an inspection that
        // restarts from the first section cannot keep a rating and a
        // recommendation derived from answers that no longer exist.
        $inspection->forceFill([
            'overall_condition' => null,
            'overall_rating' => null,
            'recommendation' => null,
            'estimated_repair_cost' => null,
            'summary' => null,
            // Odometer is deliberately NOT cleared: it is captured on the
            // Customer & Vehicle card, which a template change must preserve.
        ]);
    }

    /**
     * Upload (or replace) the primary vehicle photo shown on the
     * "Customer & Vehicle" step. One image per inspection — a new upload
     * deletes the previous file so orphans don't pile up on disk.
     */
    public function uploadVehicleImage(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        if (! $request->hasFile('file') && $this->uploadExceededPhpLimit($request)) {
            return response()->json([
                'message' => 'That image is larger than the server allows ('.ini_get('upload_max_filesize').' per file). Ask your administrator to raise upload_max_filesize.',
            ], 422);
        }

        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,jpg,png,webp,heic', 'max:10240'],
        ], [
            'file.image' => 'The vehicle image must be a photo (JPG, PNG, WEBP or HEIC).',
            'file.max' => 'The vehicle image must be 10 MB or smaller.',
        ]);

        $file = $request->file('file');
        $path = $file->store("inspections/{$inspection->id}/vehicle", 'public');

        // Replace: drop the old file once the new one is safely stored.
        $previous = $inspection->vehicle_image;

        $inspection->vehicle_image = $path;
        $this->markStarted($inspection);
        $inspection->save();

        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return response()->json([
            'url' => $inspection->vehicleImageUrl(),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Remove the primary vehicle photo.
     */
    public function deleteVehicleImage(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeInspection($inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        if ($inspection->vehicle_image) {
            Storage::disk('public')->delete($inspection->vehicle_image);
            $inspection->vehicle_image = null;
            $inspection->save();
        }

        return response()->json(['message' => 'Vehicle image removed.']);
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

    /**
     * A cancelled inspection accepts no further edits (answers, media, customer
     * details) — it is kept as a record of why and when it was cancelled.
     * Returns the JSON error for the AJAX endpoints, or null when it's editable.
     */
    private function cancelledResponse(Inspection $inspection): ?JsonResponse
    {
        if (! $inspection->isCancelled()) {
            return null;
        }

        return response()->json([
            'ok' => false,
            'message' => 'This inspection is cancelled and can no longer be edited.',
        ], 422);
    }

    /**
     * True when the request looks like an upload that PHP dropped for exceeding
     * upload_max_filesize / post_max_size. In that case $_FILES is empty (or the
     * entry carries UPLOAD_ERR_INI_SIZE) and, when post_max_size is exceeded,
     * $_POST is emptied too — so a POST with a declared length but no fields at
     * all is the tell.
     */
    private function uploadExceededPhpLimit(Request $request): bool
    {
        foreach ($_FILES as $file) {
            if (($file['error'] ?? null) === UPLOAD_ERR_INI_SIZE) {
                return true;
            }
        }

        return $_FILES === [] && $_POST === [] && (int) $request->server('CONTENT_LENGTH', 0) > 0;
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
