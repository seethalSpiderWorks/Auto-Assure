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
            'group_name' => $this->group_name,
            'group_name_ar' => $this->group_name_ar,
            'section_name' => $this->section_name,
            'section_name_ar' => $this->section_name_ar,
            'description' => $this->description,
            'sequence' => $this->sequence,
            'steps' => InspectionStepResource::collection($this->whenLoaded('steps')),
        ];
    }
}
