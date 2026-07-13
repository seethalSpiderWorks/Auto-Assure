<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sequence' => $this->sequence,
            'question' => $this->question,
            'description' => $this->description,
            'show_rating' => (bool) $this->show_rating,
            'show_text_answer' => (bool) $this->show_text_answer,
            'show_multiple_choice' => (bool) $this->show_multiple_choice,
            'multiple_choice_options' => $this->multiple_choice_options ?? [],
            'show_remedial_suggestions' => (bool) $this->show_remedial_suggestions,
            'photos' => $this->photos,
            'videos' => $this->videos,
        ];
    }
}
