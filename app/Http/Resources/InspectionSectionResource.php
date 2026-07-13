<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'section_name' => $this->section_name,
            'description' => $this->description,
            'sequence' => $this->sequence,
            'steps' => InspectionStepResource::collection($this->whenLoaded('steps')),
        ];
    }
}
