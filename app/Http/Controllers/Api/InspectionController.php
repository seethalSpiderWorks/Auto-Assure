<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InspectionResource;
use App\Models\Inspection;
use App\Models\InspectionDetail;
use App\Models\InspectionMedia;
use App\Models\InspectionSection;
use App\Models\InspectionSectionSummary;
use App\Models\InspectionSummary;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\InspectionSummaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\InspectionHistoryResource;

class InspectionController extends Controller
{
    /**
     * Technician's assigned jobs (optionally filtered by status).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Active inspections assigned to the authenticated technician (from the token).
        $query = Inspection::where('technician_id', $request->user()->id)
            ->with(['lead'])
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);          // explicit filter still works
        } else {
            $query->where('status', '!=', Inspection::STATUS_COMPLETED);   // hide completed by default
        }

        return InspectionResource::collection($query->get());
    }

    public function show(Request $request, Inspection $inspection): InspectionResource
    {
        $this->authorizeTechnician($request, $inspection);
        $inspection->load(['lead', 'type.sections.steps', 'details.media', 'sectionSummaries', 'summaries']);

        return new InspectionResource($inspection);
    }
    
     public function summary(Request $request, Inspection $inspection): InspectionSummaryResource
    {
        $this->authorizeTechnician($request, $inspection);
        $inspection->load(['lead', 'type.sections.steps', 'details.media', 'sectionSummaries']);

        return new InspectionSummaryResource($inspection);
    }


  
    public function history(Request $request): AnonymousResourceCollection
    {
        $query = Inspection::where('technician_id', $request->user()->id)
            ->where('status', '!=', Inspection::STATUS_PENDING)   // history = started/completed only
            ->with(['lead', 'type', 'branch', 'technician'])
            ->latest('updated_at');   // most recently updated first

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return InspectionHistoryResource::collection($query->get());
    }
 
    public function historyDetail(Request $request, Inspection $inspection): InspectionHistoryResource
    {
        $this->authorizeTechnician($request, $inspection);

        $inspection->load(['lead', 'type', 'branch', 'technician']);

        return new InspectionHistoryResource($inspection);
    }

    /**
     * Screen 3 — confirm/update customer & vehicle details.
     *
     * Accepts the same fields as the "Customer & Vehicle" step of the web
     * inspection edit screen, minus the ones the app must not change:
     *
     *   Reference           derived from the linked lead (Inspection::$reference)
     *   Date of Inspection  scheduling, set by the CRM
     *   Name in Arabic      maintained by the CRM
     *   Assigned Technician }  assignment, CRM-only — a technician must not be
     *   Inspection Template }  able to reassign or re-template their own job
     *
     * Anything not listed here is ignored, so posting technician_id or
     * inspection_type_id has no effect.
     */
    public function updateCustomer(Request $request, Inspection $inspection): InspectionResource|JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        // Validate manually so failures always return JSON errors (never redirect to login).
        $validator = Validator::make($request->all(), [
            // Owner
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            // Vehicle
            'car_make' => ['nullable', 'string', 'max:100'],
            'car_model' => ['nullable', 'string', 'max:100'],
            'car_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'manufacturing_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'vehicle_condition' => ['nullable', 'string', 'max:20'],
            // Vehicle details
            'vin' => ['nullable', 'string', 'max:50'],
            'plate_no' => ['nullable', 'string', 'max:50'],
            'exterior_color' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:100'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'number_of_keys' => ['nullable', 'integer', 'min:0', 'max:20'],
            // Powertrain
            'fuel_type' => ['nullable', 'string', 'max:30'],
            'gearbox' => ['nullable', 'string', 'max:30'],
            'cylinders' => ['nullable', 'string', 'max:50'],
            'steering_side' => ['nullable', 'string', 'max:50'],
            // Warranty / services
            'with_service_history' => ['nullable', 'boolean'],
            'last_service_date' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $inspection->fill($validator->validated());
        $this->markStarted($inspection);
        $inspection->save();

        return new InspectionResource($inspection->fresh(['lead']));
    }

    /**
     * Screen 4/5 — save a batch of step answers.
     */
    public function saveAnswers(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        $validator = Validator::make($request->all(), [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.step_id' => ['required', 'integer'],
            'answers.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'answers.*.choice' => ['nullable', 'string', 'max:255'],
            'answers.*.descriptive_answer' => ['nullable', 'string', 'max:5000'],
            'answers.*.remedial_suggestion' => ['nullable', 'string', 'max:5000'],
            // Optional per-section summaries + ratings (1–5) saved alongside the answers.
            'sections' => ['nullable', 'array'],
            'sections.*.section_id' => ['required_with:sections', 'integer'],
            'sections.*.summary' => ['nullable', 'string', 'max:5000'],
            'sections.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $validSteps = $inspection->type->steps()->pluck('inspection_steps.id')->all();

        foreach ($validated['answers'] as $a) {
            $stepId = (int) $a['step_id'];
            if (! in_array($stepId, $validSteps, true)) {
                continue;
            }
            InspectionDetail::updateOrCreate(
                ['inspection_id' => $inspection->id, 'inspection_step_id' => $stepId],
                [
                    'rating' => $a['rating'] ?? null,
                    'choice' => $a['choice'] ?? null,
                    'descriptive_answer' => $a['descriptive_answer'] ?? null,
                    'remedial_suggestion' => $a['remedial_suggestion'] ?? null,
                ]
            );
        }

        // Per-section summaries + ratings (only for sections in this template).
        if (! empty($validated['sections'])) {
            $validSections = $inspection->type
                ? $inspection->type->sections()->pluck('inspection_sections.id')->all()
                : [];

            foreach ($validated['sections'] as $s) {
                $sectionId = (int) $s['section_id'];
                if (! in_array($sectionId, $validSections, true)) {
                    continue;
                }
                InspectionSectionSummary::updateOrCreate(
                    ['inspection_id' => $inspection->id, 'inspection_section_id' => $sectionId],
                    [
                        'summary' => filled($s['summary'] ?? null) ? $s['summary'] : null,
                        'rating' => $s['rating'] ?? null,
                    ]
                );
            }
        }

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json([
            'message' => 'Answers saved.',
            'progress' => $inspection->progress(),
        ]);
    }

    /**
     * Upload one photo/video for a given step.
     */
    public function uploadMedia(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        // Section mode (preferred): flat, reliable format — section_ids[] paired
        // with files[] by index. Mirrors the web "upload per section" flow but
        // lets the app send many files across many sections in one request.
        if ($request->has('section_ids')) {
            return $this->uploadSectionMediaBatch($request, $inspection);
        }

        // Section mode (legacy): nested files[<section_id>][]. Kept for backward
        // compatibility; the nested $_FILES shape is unreliable on some PHP/
        // Symfony configs, so new clients should use section_ids[] + files[].
        if ($request->hasFile('files') && is_array($request->file('files'))) {
            return $this->uploadSectionMedia($request, $inspection);
        }

        $validator = Validator::make($request->all(), [
            'step_id' => ['required', 'integer'],
            'type' => ['required', 'in:photo,video'],
            'file' => ['required', 'file', 'max:102400'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $stepId = (int) $data['step_id'];
        $validSteps = $inspection->type->steps()->pluck('inspection_steps.id')->all();

        if (! in_array($stepId, $validSteps, true)) {
            return response()->json(['message' => 'Step does not belong to this inspection.'], 422);
        }

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

        return response()->json([
            'id' => $media->id,
            'type' => $media->type,
            'url' => $media->url,
        ], 201);
    }

    /**
     * Upload multiple photos/videos organised by section (category).
     *
     * Dedicated public endpoint — use when uploading files per inspection
     * section rather than per step.
     *
     * Files arrive keyed by section id, so one request can cover several
     * categories at once:
     *   files[12][] = engine-1.jpg
     *   files[12][] = engine-2.jpg
     *   files[9][]  = exterior-1.mp4
     *
     * Photo vs video is derived from each file's MIME type. Validation is
     * all-or-nothing — if any file or section id is rejected, nothing is stored.
     *
     * POST /api/inspections/{inspection}/sections/media
     */
    public function uploadSectionMedia(Request $request, Inspection $inspection): JsonResponse
    {
        $groups = $request->file('files');

        $validSections = InspectionSection::where('inspection_type_id', $inspection->inspection_type_id)
            ->pluck('section_name', 'id');

        $unknown = array_values(array_filter(
            array_keys($groups),
            fn ($id) => ! $validSections->has((int) $id)
        ));

        if ($unknown !== []) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['files' => ['Unknown section id(s) for this inspection: '.implode(', ', $unknown).'.']],
            ], 422);
        }

        $rules = [];
        foreach (array_keys($groups) as $sectionId) {
            $rules["files.{$sectionId}"] = ['required', 'array', 'min:1'];
            $rules["files.{$sectionId}.*"] = ['file', 'max:102400', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/heic,video/mp4,video/quicktime,video/x-matroska'];
        }

        $validator = Validator::make(['files' => $groups], $rules);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $sections = [];
        $total = 0;

        foreach ($groups as $sectionId => $files) {
            $sectionId = (int) $sectionId;

            $detail = InspectionDetail::firstOrCreate([
                'inspection_id' => $inspection->id,
                'inspection_step_id' => null,
                'inspection_section_id' => $sectionId,
            ]);

            $items = [];

            foreach ($files as $file) {
                $type = str_starts_with((string) $file->getClientMimeType(), 'video/') ? 'video' : 'photo';
                $path = $file->store("inspections/{$inspection->id}/sections/{$sectionId}/{$type}s", 'public');

                $media = $detail->media()->create([
                    'type' => $type,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);

                $items[] = [
                    'id' => $media->id,
                    'type' => $media->type,
                    'url' => $media->url,
                    'original_name' => $media->original_name,
                    'size' => $media->size,
                ];
                $total++;
            }

            $sections[] = [
                'section_id' => $sectionId,
                'section_name' => $validSections->get($sectionId),
                'uploaded' => count($items),
                'media' => $items,
            ];
        }

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['uploaded' => $total, 'sections' => $sections], 201);
    }

    /**
     * Batch upload files to inspection sections using a flat, reliable request
     * format — each file is paired with its section_id by array index.
     *
     * This avoids the deeply nested $_FILES structure (files[12][]) that
     * some PHP / Symfony configurations fail to parse correctly.
     *
     * Request format (multipart/form-data):
     *   section_ids[0]  (text)  = 12
     *   section_ids[1]  (text)  = 12
     *   section_ids[2]  (text)  = 9
     *   files[0]        (file)  = engine-1.jpg
     *   files[1]        (file)  = engine-2.jpg
     *   files[2]        (file)  = exterior.jpg
     *
     * POST /api/inspections/{inspection}/sections/media
     */
    public function uploadSectionMediaBatch(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        $validator = Validator::make($request->all(), [
            'section_ids' => ['required', 'array', 'min:1'],
            'section_ids.*' => ['required', 'integer', 'min:1'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:102400', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/heic,video/mp4,video/quicktime,video/x-matroska'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $sectionIds = $request->input('section_ids', []);
        $files = $request->file('files', []);

        // The two arrays must be the same length — one section_id per file.
        if (count($sectionIds) !== count($files)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['section_ids' => ['The number of section_ids must match the number of files.']],
            ], 422);
        }

        // Validate that all section IDs belong to this inspection's template.
        $validSections = InspectionSection::where('inspection_type_id', $inspection->inspection_type_id)
            ->pluck('section_name', 'id');

        $validSectionIds = $validSections->keys()->all();

        foreach ($sectionIds as $sid) {
            if (! in_array((int) $sid, $validSectionIds, true)) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['section_ids' => ["Section ID {$sid} does not belong to this inspection template."]],
                ], 422);
            }
        }

        // Group files by section ID.
        $groups = [];
        foreach ($sectionIds as $i => $sid) {
            $sid = (int) $sid;
            $groups[$sid][] = $files[$i];
        }

        $sections = [];
        $total = 0;

        foreach ($groups as $sectionId => $sectionFiles) {
            $detail = InspectionDetail::firstOrCreate([
                'inspection_id' => $inspection->id,
                'inspection_step_id' => null,
                'inspection_section_id' => $sectionId,
            ]);

            $items = [];

            foreach ($sectionFiles as $file) {
                $type = str_starts_with((string) $file->getClientMimeType(), 'video/') ? 'video' : 'photo';
                $path = $file->store("inspections/{$inspection->id}/sections/{$sectionId}/{$type}s", 'public');

                $media = $detail->media()->create([
                    'type' => $type,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);

                $items[] = [
                    'id' => $media->id,
                    'type' => $media->type,
                    'url' => $media->url,
                    'original_name' => $media->original_name,
                    'size' => $media->size,
                ];
                $total++;
            }

            $sections[] = [
                'section_id' => $sectionId,
                'section_name' => $validSections->get($sectionId),
                'uploaded' => count($items),
                'media' => $items,
            ];
        }

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json(['uploaded' => $total, 'sections' => $sections], 201);
    }

    public function deleteMedia(Request $request, InspectionMedia $media): JsonResponse
    {
        $inspection = $media->detail->inspection;
        $this->authorizeTechnician($request, $inspection);

        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(['message' => 'Media deleted.']);
    }

    /**
     * Screen 6 — finalize verdict and submit (complete) the inspection.
     */
    public function submit(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        $validator = Validator::make($request->all(), [
            'odometer' => ['nullable', 'integer', 'min:0'],
            'overall_condition' => ['nullable', 'in:'.implode(',', array_keys(Inspection::CONDITIONS))],
            'recommendation' => ['nullable', 'string', 'max:255'],
            'estimated_repair_cost' => ['nullable', 'numeric', 'min:0'],
            'summary' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $inspection->fill($validator->validated());

        if ($missing = $inspection->missingMandatoryMedia()) {
            return response()->json([
                'message' => 'Mandatory media missing.',
                'missing' => $missing,
            ], 422);
        }

        $inspection->status = Inspection::STATUS_COMPLETED;
        $inspection->completed_at = now();
        $inspection->save();
        $inspection->lead?->update(['status' => Lead::STATUS_COMPLETED]);

        return response()->json([
            'message' => 'Inspection submitted.',
            'inspection' => new InspectionResource($inspection->fresh(['lead', 'type.sections.steps', 'details.media'])),
        ]);
    }

    /**
     * Master list of summary areas (Exterior, Engine, Brakes, …) from
     * tbl_summary_type — the sections shown on the per-area notes screen.
     *
     * GET /api/summary/list
     */
    public function summaryTypeList(): JsonResponse
    {
        $areas = \Illuminate\Support\Facades\DB::table('tbl_summary_type')
            ->where('summary_type_status', 0)
            ->orderBy('summary_type_id')
            ->get()
            ->map(fn ($t) => [
                'id' => (int) $t->summary_type_id,
                'summary_type_name' => $t->summary_type_name,
            ])
            ->values();

        return response()->json(['summaries' => $areas]);
    }

    /**
     * Save the per-area summary notes. Every summary area is required — a note
     * must be provided for each type in tbl_summary_type.
     *
     * POST /api/inspections/{inspection}/summaries
     */
    public function saveSummaries(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        $types = InspectionSummary::types();   // [id => name]

        // Build "required for every area" rules so a missing/blank note fails
        // with a clear, per-area validation error.
        $rules = ['summaries' => ['required', 'array']];
        $attributes = [];
        foreach ($types as $id => $name) {
            $rules["summaries.{$id}"] = ['required', 'string', 'max:5000'];
            $attributes["summaries.{$id}"] = $name;
        }

        $validator = Validator::make($request->all(), $rules, [], $attributes);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        foreach ($validator->validated()['summaries'] as $typeId => $text) {
            InspectionSummary::updateOrCreate(
                ['inspection_id' => $inspection->id, 'summary_type_id' => (int) $typeId],
                ['summary' => $text]
            );
        }

        $this->markStarted($inspection);
        $inspection->save();

        $areas = $inspection->summaries()->get()->map(fn ($s) => [
            'summary_type_id' => $s->summary_type_id,
            'name' => $types[$s->summary_type_id] ?? null,
            'summary' => $s->summary,
        ])->values();

        return response()->json([
            'message' => 'Summaries saved.',
            'summaries' => $areas,
        ]);
    }

    private function markStarted(Inspection $inspection): void
    {
        if ($inspection->status === Inspection::STATUS_PENDING) {
            $inspection->status = Inspection::STATUS_IN_PROGRESS;
            $inspection->started_at ??= now();
            $inspection->lead?->update(['status' => Lead::STATUS_IN_PROGRESS]);
        }
    }

    // NOT named authorize() — that clashes with the inherited AuthorizesRequests::authorize().
    private function authorizeTechnician(Request $request, Inspection $inspection): void
    {
        abort_unless($inspection->technician_id === $request->user()->id, 403, 'This inspection is not assigned to you.');
    }
}
