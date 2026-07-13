<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InspectionTypeResource;
use App\Models\Inspection;
use App\Models\InspectionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InspectionTypeController extends Controller
{
    /**
     * Screen 2 — list selectable inspection types with their full section/step template.
     */
    public function index(): AnonymousResourceCollection
    {
        $types = InspectionType::where('is_active', true)
            ->with(['sections.steps'])
            ->orderBy('sequence')
            ->get();

        return InspectionTypeResource::collection($types);
    }

    public function show(InspectionType $inspectionType): InspectionTypeResource
    {
        $inspectionType->load(['sections.steps']);

        return new InspectionTypeResource($inspectionType);
    }

   
  
}
