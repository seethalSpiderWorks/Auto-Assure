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
use Illuminate\Support\Carbon;
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
     * Technician's assigned jobs (optionally filtered by status and day).
     *
     * Ordered today → upcoming (latest day first) → overdue → unscheduled.
     *
     * Query params:
     *   status  explicit status filter (default: anything but completed)
     *   date    limit to one scheduled day — "today" or a Y-m-d date. Filters on
     *           scheduled_at, the same column the list is ordered by, so jobs
     *           with no slot yet are excluded from a day filter.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        // Day boundaries in the app timezone: today's slots sit between the two,
        // anything earlier is overdue and anything later is upcoming.
        $todayStart    = now()->startOfDay();
        $tomorrowStart = now()->addDay()->startOfDay();

        // Active inspections assigned to the authenticated technician (from the token).
        $query = Inspection::where('technician_id', $request->user()->id)
            // cancelledBy so a cancelled job reports who cancelled it (the resource
            // omits cancelled_by_name entirely when the relation isn't loaded).
            ->with(['lead', 'cancelledBy'])
            // Today first, then upcoming days (newest first), then overdue days,
            // then jobs with no slot yet. A job stays in the "today" group for the
            // whole day, so today's list keeps reading chronologically (10:00
            // before 15:00) even once its slot time has passed.
            ->orderByRaw(
                'CASE WHEN scheduled_at IS NULL THEN 3
                       WHEN scheduled_at < ? THEN 2
                       WHEN scheduled_at < ? THEN 0
                       ELSE 1 END',
                [$todayStart, $tomorrowStart]
            )
            // Today only: earliest slot first. Other groups get NULL here (a
            // constant), so this clause leaves their order alone.
            ->orderByRaw(
                'CASE WHEN scheduled_at >= ? AND scheduled_at < ? THEN scheduled_at END',
                [$todayStart, $tomorrowStart]
            )
            ->orderByDesc('scheduled_at')          // upcoming & overdue: latest day first
            ->orderBy('id');                       // id breaks exact ties

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);          // explicit filter still works
        } else {
            // Hide completed and cancelled by default — neither is work the
            // technician can still act on.
            $query->whereNotIn('status', [Inspection::STATUS_COMPLETED, Inspection::STATUS_CANCELLED]);
        }

        if ($date = $request->string('date')->toString()) {
            // A day the DB can't read would silently match nothing, which reads
            // as "no jobs today" — reject it instead so the app sees the typo.
            $day = $this->resolveDay($date);

            if ($day === null) {
                return response()->json([
                    'message' => 'Invalid date filter. Use "today" or a YYYY-MM-DD date.',
                    'errors'  => ['date' => ['The date filter must be "today" or a YYYY-MM-DD date.']],
                ], 422);
            }

            $query->whereDate('scheduled_at', $day);
        }

        return InspectionResource::collection($query->get());
    }

    /**
     * Normalise the ?date= filter to a Y-m-d string, or null when unparseable.
     */
    private function resolveDay(string $date): ?string
    {
        if ($date === 'today') {
            return now()->toDateString();
        }

        try {
            $day = Carbon::createFromFormat('Y-m-d', $date)->toDateString();
        } catch (\Throwable) {
            return null;
        }

        // createFromFormat overflows rather than throwing — "2026-13-45" quietly
        // becomes 2027-02-14. Only accept a date that round-trips unchanged.
        return $day === $date ? $day : null;
    }

    public function show(Request $request, Inspection $inspection): InspectionResource
    {
        $this->authorizeTechnician($request, $inspection);
        $inspection->load(['lead', 'type.sections.steps', 'details.media', 'sectionSummaries', 'summaries', 'cancelledBy']);

        return new InspectionResource($inspection);
    }
    
     public function summary(Request $request, Inspection $inspection): InspectionSummaryResource
    {
        $this->authorizeTechnician($request, $inspection);
        $inspection->load(['lead', 'type.sections.steps', 'details.media', 'sectionSummaries', 'summaries', 'cancelledBy']);

        return new InspectionSummaryResource($inspection);
    }


  
    /**
     * Technician history — in progress, completed and cancelled jobs.
     *
     * Query params:
     *   status  in_progress | completed | cancelled
     */
    public function history(Request $request): AnonymousResourceCollection
    {
        $query = Inspection::where('technician_id', $request->user()->id)
            ->where('status', '!=', Inspection::STATUS_PENDING)   // history = started/completed/cancelled only
            // cancelledBy is eager-loaded so cancelled_by_name is present in the
            // payload; the resource omits the key entirely when it isn't loaded.
            ->with(['lead', 'type', 'branch', 'technician', 'cancelledBy'])
            ->latest('updated_at');   // most recently updated first

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return InspectionHistoryResource::collection($query->get());
    }

    public function historyDetail(Request $request, Inspection $inspection): InspectionHistoryResource
    {
        $this->authorizeTechnician($request, $inspection);

        // cancelledBy included so a cancelled inspection reports who cancelled it.
        $inspection->load(['lead', 'type', 'branch', 'technician', 'cancelledBy']);

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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        // Validate manually so failures always return JSON errors (never redirect to login).
        $validator = Validator::make($request->all(), [
            // Owner
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            // Inspection schedule (date + time), e.g. 2026-07-24T14:30 or 2026-07-24 14:30:00.
            'scheduled_at' => ['nullable', 'date'],
            // Vehicle
            'car_make' => ['nullable', 'string', 'max:100'],
            'car_model' => ['nullable', 'string', 'max:100'],
            'car_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'manufacturing_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            'vehicle_condition' => ['nullable', 'string', 'max:20'],
            'odometer' => ['nullable', 'integer', 'min:0'],
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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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
            // Section ratings take one decimal place (0.5, 4.6); per-step
            // ratings above stay whole 1–5 stars.
            'sections.*.rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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
        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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

    /**
     * Additional media — photos/videos that belong to the inspection as a whole
     * rather than to a checklist step or a section. This is the same bucket as
     * the "Additional media" box on the web edit screen: a single detail row per
     * inspection with BOTH inspection_step_id and inspection_section_id NULL.
     *
     * POST /api/inspections/{inspection}/extra-media   (multipart/form-data)
     *   files[]   one or more photos/videos (required)
     *   labels[]  optional caption per file, paired with files[] by index
     *
     * A single upload may also be posted as `file` (+ optional `label`).
     * The photo/video type is derived from the file's mime type.
     */
    public function uploadExtraMedia(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        // Accept files[] (+ labels[]) or the single-file form file (+ label), and
        // normalise both into plain arrays. Built locally rather than merged back
        // into the request: hasFile()/file() cache the converted upload set, so a
        // later $request->files->set() would not be seen by the validator.
        $files = $request->file('files');
        $labels = $request->input('labels', []);

        if (empty($files) && $request->hasFile('file')) {
            $files = [$request->file('file')];
            $labels = $request->filled('label') ? [$request->input('label')] : [];
        }

        $files = is_array($files) ? array_values($files) : (($files === null) ? [] : [$files]);
        $labels = is_array($labels) ? array_values($labels) : [];

        $validator = Validator::make(['files' => $files, 'labels' => $labels], [
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:102400', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/heic,video/mp4,video/quicktime,video/x-matroska,application/pdf'],
            'labels' => ['nullable', 'array'],
            'labels.*' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Labels are optional, but when sent there must be one per file — a
        // mismatched array would silently caption the wrong photos.
        if ($labels !== [] && count($labels) !== count($files)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['labels' => ['The number of labels must match the number of files.']],
            ], 422);
        }

        // PDFs are capped tighter than photos/videos. Checked across the whole
        // batch before anything is stored, so a rejected upload leaves no
        // half-saved files behind.
        $tooLarge = [];

        foreach ($files as $i => $file) {
            if ($this->mediaTypeFor($file) === 'document' && $file->getSize() > InspectionMedia::MAX_DOCUMENT_BYTES) {
                $tooLarge["files.{$i}"] = [
                    InspectionMedia::documentTooLargeMessage($file->getClientOriginalName(), (int) $file->getSize()),
                ];
            }
        }

        if ($tooLarge !== []) {
            return response()->json([
                'message' => reset($tooLarge)[0],
                'errors' => $tooLarge,
            ], 422);
        }

        $detail = $this->extraMediaDetail($inspection, create: true);

        $items = [];

        foreach ($files as $i => $file) {
            $type = $this->mediaTypeFor($file);
            // photos / videos / documents
            $path = $file->store("inspections/{$inspection->id}/extra/{$type}s", 'public');

            $media = $detail->media()->create([
                'type' => $type,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'label' => $labels[$i] ?? null,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);

            $items[] = $this->mediaPayload($media);
        }

        $this->markStarted($inspection);
        $inspection->save();

        return response()->json([
            'uploaded' => count($items),
            'media' => $items,
        ], 201);
    }

    /**
     * List an inspection's additional media.
     *
     * GET /api/inspections/{inspection}/extra-media
     */
    public function extraMedia(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        $media = $this->extraMediaItems($inspection);

        return response()->json([
            'count' => count($media),
            'media' => $media,
        ]);
    }

    /**
     * Delete one additional-media item.
     *
     * DELETE /api/inspections/{inspection}/extra-media/{media}
     */
    public function deleteExtraMedia(Request $request, Inspection $inspection, InspectionMedia $media): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        $detail = $this->extraMediaDetail($inspection);

        // The id must be one of THIS inspection's additional media — not another
        // inspection's, and not a step/section photo that happens to exist.
        if (! $detail || (int) $media->inspection_detail_id !== (int) $detail->id) {
            return response()->json([
                'message' => "Media does not belong to this inspection's additional media.",
            ], 404);
        }

        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json([
            'message' => 'Media deleted.',
            'id' => (int) $media->id,
            'remaining' => count($this->extraMediaItems($inspection)),
        ]);
    }

    /**
     * Delete several additional-media items at once.
     *
     * DELETE /api/inspections/{inspection}/extra-media
     * Body: media_ids[] — ids to delete (required)
     */
    public function deleteExtraMediaBulk(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        $validator = Validator::make($request->all(), [
            'media_ids' => ['required', 'array', 'min:1'],
            'media_ids.*' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $ids = array_map('intval', $request->input('media_ids', []));
        $detail = $this->extraMediaDetail($inspection);

        $rows = $detail
            ? $detail->media()->whereIn('id', $ids)->get()
            : collect();

        // Nothing matched: every id was for another inspection, another bucket, or
        // already deleted. Report it rather than answering "deleted 0" with a 200.
        if ($rows->isEmpty()) {
            return response()->json([
                'message' => "None of the given ids belong to this inspection's additional media.",
            ], 404);
        }

        $deleted = [];

        foreach ($rows as $m) {
            Storage::disk($m->disk)->delete($m->path);
            $deleted[] = (int) $m->id;
            $m->delete();
        }

        return response()->json([
            'deleted' => count($deleted),
            'ids' => $deleted,
            // Ids that matched nothing, so the app can reconcile its local list.
            'not_found' => array_values(array_diff($ids, $deleted)),
            'remaining' => count($this->extraMediaItems($inspection)),
        ]);
    }

    /**
     * The inspection's additional-media bucket: the detail row with BOTH a null
     * step id and a null section id. Both keys must be matched explicitly — a
     * per-section bucket also has a null step id, so omitting the section would
     * pick up whichever section row happens to match first.
     */
    private function extraMediaDetail(Inspection $inspection, bool $create = false): ?InspectionDetail
    {
        $keys = [
            'inspection_id' => $inspection->id,
            'inspection_step_id' => null,
            'inspection_section_id' => null,
        ];

        return $create
            ? InspectionDetail::firstOrCreate($keys)
            : InspectionDetail::where($keys)->first();
    }

    /**
     * The inspection's additional media, in payload form.
     */
    private function extraMediaItems(Inspection $inspection): array
    {
        $detail = $this->extraMediaDetail($inspection);

        return $detail
            ? $detail->media()->get()->map(fn ($m) => $this->mediaPayload($m))->values()->all()
            : [];
    }

    /**
     * Classify an upload as photo / video / document. The client's declared MIME
     * is unreliable (the app often sends application/octet-stream), so the file
     * extension is the fallback — same rule InspectionMedia::isVideo() uses.
     */
    private function mediaTypeFor(\Illuminate\Http\UploadedFile $file): string
    {
        $mime = (string) $file->getClientMimeType();
        $ext = strtolower((string) $file->getClientOriginalExtension());

        if ($mime === 'application/pdf' || in_array($ext, InspectionMedia::DOCUMENT_EXTENSIONS, true)) {
            return 'document';
        }

        if (str_starts_with($mime, 'video/') || in_array($ext, InspectionMedia::VIDEO_EXTENSIONS, true)) {
            return 'video';
        }

        return 'photo';
    }

    /**
     * The shape a media row is returned in by the additional-media endpoints.
     */
    private function mediaPayload(InspectionMedia $media): array
    {
        return [
            'id' => $media->id,
            'type' => $media->type,
            'url' => $media->url,
            'label' => $media->label,
            'original_name' => $media->original_name,
            'size' => $media->size,
        ];
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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

        $validator = Validator::make($request->all(), [
            'odometer' => ['nullable', 'integer', 'min:0'],
            'overall_condition' => ['nullable', 'in:'.implode(',', array_keys(Inspection::CONDITIONS))],
            'overall_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'recommendation' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:10'],
            'estimated_repair_cost' => ['nullable', 'string', 'max:50'],
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

        // Stamp the start before the finish: an inspection submitted without any
        // prior save (answers/media/customer all optional) is still PENDING here,
        // and would otherwise land as "completed" with a NULL started_at. The web
        // update() does the same pending -> in_progress step before completing.
        $this->markStarted($inspection);

        $inspection->status = Inspection::STATUS_COMPLETED;
        // First submit wins — a repeated POST must not push the completion time
        // forward. A reopen (template change on web) clears completed_at, so a
        // genuine re-completion still gets a fresh stamp.
        $inspection->completed_at ??= now();
        $inspection->save();
        $inspection->lead?->update(['status' => Lead::STATUS_COMPLETED]);

        return response()->json([
            'message' => 'Inspection submitted.',
            'inspection' => new InspectionResource($inspection->fresh(['lead', 'type.sections.steps', 'details.media'])),
        ]);
    }

    /**
     * Cancel an inspection. An admin (privilege 1 / 2) may cancel any inspection;
     * a technician may cancel one assigned to them — the same rule the CRM web
     * screens enforce. Records who cancelled it, when and why, and marks the
     * lead "Inspection Cancelled".
     *
     * The cancelled inspection is kept as a permanent record; assigning the lead
     * again from the CRM creates a NEW inspection rather than reviving this one.
     *
     * POST /api/inspections/{inspection}/cancel
     * Body: cancel_reason (required, 5–500 chars)
     */
    public function cancel(Request $request, Inspection $inspection): JsonResponse
    {
        $user = $request->user();

        // An admin may cancel any inspection; a technician only their own.
        if (! $user?->isAdmin() && (int) $inspection->technician_id !== (int) $user?->id) {
            return response()->json([
                'message' => 'You are not allowed to cancel this inspection.',
            ], 403);
        }

        if ($inspection->isCancelled()) {
            return response()->json([
                'message' => 'This inspection is already cancelled.',
                'inspection' => new InspectionResource($inspection),
            ], 422);
        }

        // Cancellable while the job is still open — pending or in progress.
        if (! $inspection->isCancellable()) {
            return response()->json([
                'message' => 'A completed inspection cannot be cancelled.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'cancel_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'cancel_reason.required' => 'Please give a reason for cancelling this inspection.',
            'cancel_reason.min' => 'The reason must be at least 5 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $inspection->fill([
            'status' => Inspection::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancel_reason' => trim($validator->validated()['cancel_reason']),
            'cancelled_by' => $user->id,
        ])->save();

        $inspection->lead?->update(['status' => Lead::STATUS_CANCELLED]);

        return response()->json([
            'message' => 'Inspection cancelled.',
            'inspection' => new InspectionResource($inspection->fresh(['lead', 'cancelledBy'])),
        ]);
    }

    /**
     * Summary areas (Exterior, Engine, Brakes, …) from tbl_summary_type for a
     * given inspection — with the inspection details and any note already saved
     * against each area.
     *
     * GET /api/inspections/{inspection}/summary/list
     */
    public function summaryTypeList(Request $request, Inspection $inspection): JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        // Saved notes for this inspection, keyed by summary_type_id.
        $saved = $inspection->summaries()->pluck('summary', 'summary_type_id');

        $areas = \Illuminate\Support\Facades\DB::table('tbl_summary_type')
            ->where('summary_type_status', 0)
            ->orderBy('summary_type_id')
            ->get()
            ->map(fn ($t) => [
                'id' => (int) $t->summary_type_id,
                'summary_type_name' => $t->summary_type_name,
                'summary' => $saved->get($t->summary_type_id),
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

        if ($cancelled = $this->cancelledResponse($inspection)) {
            return $cancelled;
        }

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

    /**
     * A cancelled inspection accepts no further work from the app. Returns the
     * JSON error to send back, or null when the inspection is still open.
     */
    private function cancelledResponse(Inspection $inspection): ?JsonResponse
    {
        if (! $inspection->isCancelled()) {
            return null;
        }

        return response()->json([
            'message' => 'This inspection has been cancelled.',
        ], 422);
    }

    // NOT named authorize() — that clashes with the inherited AuthorizesRequests::authorize().
    private function authorizeTechnician(Request $request, Inspection $inspection): void
    {
        abort_unless($inspection->technician_id === $request->user()->id, 403, 'This inspection is not assigned to you.');
    }
}
