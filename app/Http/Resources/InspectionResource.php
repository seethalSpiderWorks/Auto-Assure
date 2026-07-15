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

            'customer_name'  => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,

            'car_make'  => $this->car_make,
            'car_model' => $this->car_model,
            'car_year'  => $this->car_year,
            'car'       => $this->car(),

            // Extended vehicle details
            'vin'                 => $this->vin,
            'registration_number' => $this->registration_number,
            'variant'             => $this->variant,
            'color'               => $this->color,
            'fuel_type'           => $this->fuel_type,
            'transmission'        => $this->transmission,
            'body_type'           => $this->body_type,
            'number_of_keys'      => $this->number_of_keys,
            'vehicle_type'        => $this->vehicle_type,
            'manufacturer_name'   => $this->manufacturer_name,
            'country_of_origin'   => $this->country_of_origin,
            'country_of_export'   => $this->country_of_export,
            'motor_power_kw'      => $this->motor_power_kw,
            'cylinders_cc'        => $this->cylinders_cc,
            'passengers'          => $this->passengers,
            'fuel_economy'        => $this->fuel_economy,

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

            'type'    => new InspectionTypeResource($this->whenLoaded('type')),
            'details' => $this->whenLoaded('details', fn () => $this->details->map(fn ($detail) => [
                'id'                  => $detail->id,
                'inspection_step_id'  => $detail->inspection_step_id,
                'rating'              => $detail->rating,
                'choice'              => $detail->choice,
                'descriptive_answer'  => $detail->descriptive_answer,
                'remedial_suggestion' => $detail->remedial_suggestion,
                'media'               => $detail->relationLoaded('media')
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
