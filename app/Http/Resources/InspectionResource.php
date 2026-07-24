<?php

namespace App\Http\Resources;

use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'lead_id'            => $this->lead_id,
            'branch_id'          => $this->branch_id,
            'technician_id'      => $this->technician_id,
            'inspection_type_id' => $this->inspection_type_id,

            'status'       => $this->status,
            'scheduled_at' => optional($this->scheduled_at)->toIso8601String(),
            'started_at'   => optional($this->started_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),

            'customer_name'    => $this->customer_name,
            'customer_name_ar' => $this->customer_name_ar,
            'customer_email'   => $this->customer_email,
            'customer_phone'   => $this->customer_phone,

            'reference'          => $this->reference,
            'date_of_inspection' => optional($this->date_of_inspection)->toDateString(),

            'car_make'  => $this->car_make,
            'car_model' => $this->car_model,
            'car_year'  => $this->car_year,
            'car'       => $this->car(),

            // Extended vehicle details
            'manufacturing_year'   => $this->manufacturing_year,
            'vehicle_condition'    => $this->vehicle_condition,
            'vin'                  => $this->vin,
            'plate_no'             => $this->plate_no,
            'exterior_color'       => $this->exterior_color,
            'region'               => $this->region,
            'fuel_type'            => $this->fuel_type,
            'gearbox'              => $this->gearbox,
            'cylinders'            => $this->cylinders,
            'steering_side'        => $this->steering_side,
            'body_type'            => $this->body_type,
            'number_of_keys'       => $this->number_of_keys,
            'with_service_history' => $this->with_service_history,
            'last_service_date'    => optional($this->last_service_date)->toDateString(),

            'odometer'              => $this->odometer,
            'overall_condition'     => $this->overall_condition,
            'overall_condition_label' => Inspection::CONDITIONS[$this->overall_condition] ?? null,
            'recommendation'        => $this->recommendation,
            'recommendation_label'  => Inspection::RECOMMENDATIONS[$this->recommendation] ?? null,
            'estimated_repair_cost' => $this->estimated_repair_cost,
            'summary'               => $this->summary,

            'progress' => $this->progress(),

            // Per-section summary + rating (manual rating if set, else derived from answers).
            'section_summaries' => $this->when(
                $this->relationLoaded('type') && $this->type && $this->type->relationLoaded('sections'),
                function () {
                    $byStep = $this->relationLoaded('details')
                        ? $this->details->keyBy('inspection_step_id')
                        : collect();
                    $summaryBySection = $this->relationLoaded('sectionSummaries')
                        ? $this->sectionSummaries->keyBy('inspection_section_id')
                        : collect();

                    return $this->type->sections->map(fn ($section) => [
                        'section_id'   => $section->id,
                        'section_name' => $section->section_name,
                        'summary'      => optional($summaryBySection->get($section->id))->summary,
                        'rating'       => Inspection::sectionRating($section, $byStep, optional($summaryBySection->get($section->id))->rating),
                    ])->values();
                }
            ),

            // Per-area summary notes (Exterior, Engine, Brakes, …) from tbl_summary_type.
            'summaries' => $this->whenLoaded('summaries', function () {
                $types = \App\Models\InspectionSummary::types();

                return $this->summaries->map(fn ($s) => [
                    'summary_type_id'   => (int) $s->summary_type_id,
                    'summary_type_name' => $types[$s->summary_type_id] ?? null,
                    'summary'           => $s->summary,
                ])->values();
            }),

            'type'    => new InspectionTypeResource($this->whenLoaded('type')),
            'details' => $this->whenLoaded('details', fn () => $this->details->map(fn ($detail) => [
                'id'                     => $detail->id,
                'inspection_step_id'     => $detail->inspection_step_id,
                'inspection_section_id'  => $detail->inspection_section_id,
                'rating'                 => $detail->rating,
                'choice'                 => $detail->choice,
                'descriptive_answer'     => $detail->descriptive_answer,
                'remedial_suggestion'    => $detail->remedial_suggestion,
                'media'                  => $detail->relationLoaded('media')
                    ? $detail->media->map(fn ($m) => [
                        'id'   => $m->id,
                        'type' => $m->type,
                        'url'  => $m->url,
                    ])->values()
                    : [],
            ])->values()),
        ];
    }
}
