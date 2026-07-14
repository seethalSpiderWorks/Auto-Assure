<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InspectionResource;
use App\Models\Inspection;
use App\Models\InspectionDetail;
use App\Models\InspectionMedia;
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
        $inspection->load(['lead', 'type.sections.steps', 'details.media']);

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
     */
    public function updateCustomer(Request $request, Inspection $inspection): InspectionResource|JsonResponse
    {
        $this->authorizeTechnician($request, $inspection);

        // Validate manually so failures always return JSON errors (never redirect to login).
        $validator = Validator::make($request->all(), [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'car_make' => ['nullable', 'string', 'max:100'],
            'car_model' => ['nullable', 'string', 'max:100'],
            'car_year' => ['nullable', 'integer', 'min:1950', 'max:2100'],
            // Extended vehicle details
            'vin' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'variant' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'fuel_type' => ['nullable', 'string', 'max:30'],
            'transmission' => ['nullable', 'string', 'max:30'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'number_of_keys' => ['nullable', 'integer', 'min:0', 'max:20'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'manufacturer_name' => ['nullable', 'string', 'max:100'],
            'country_of_origin' => ['nullable', 'string', 'max:100'],
            'country_of_export' => ['nullable', 'string', 'max:100'],
            'motor_power_kw' => ['nullable', 'integer', 'min:0'],
            'cylinders_cc' => ['nullable', 'string', 'max:50'],
            'passengers' => ['nullable', 'integer', 'min:0', 'max:100'],
            'fuel_economy' => ['nullable', 'string', 'max:30'],
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
