<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'is_active' => (bool) $this->is_active,
            // Whether the app should offer the step-less Diagnostic Media bucket.
            'has_diagnostic_media' => (bool) $this->has_diagnostic_media,
            // Verdict and Recommendations are calculated from the section weights;
            // false means the app submits overall_rating / recommendation as before.
            'has_calculated_verdict' => (bool) $this->has_calculated_verdict,
            'sequence' => $this->sequence,
            'sections' => InspectionSectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
