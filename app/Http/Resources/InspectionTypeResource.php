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
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            // Whether the app should offer the step-less Diagnostic Media bucket.
            'has_diagnostic_media' => (bool) $this->has_diagnostic_media,
            'sequence' => $this->sequence,
            'sections' => InspectionSectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
