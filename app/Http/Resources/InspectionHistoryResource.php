<?php

namespace App\Http\Resources;

use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lightweight history view of an inspection — customer, vehicle, status and
 * verdict only. Deliberately excludes the checklist questions (type/sections/
 * steps) and the recorded answers (details/media).
 */
class InspectionHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'lead_id'            => $this->lead_id,
            'branch_id'          => $this->branch_id,
            'technician_id'      => $this->technician_id,
            'inspection_type_id' => $this->inspection_type_id,
            'inspection_type'    => $this->whenLoaded('type', fn () => $this->type->name),

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

            'odometer'                => $this->odometer,
            'overall_condition'       => $this->overall_condition,
            'overall_condition_label' => Inspection::CONDITIONS[$this->overall_condition] ?? null,
            'recommendation'          => $this->recommendation,
            'recommendation_label'    => Inspection::RECOMMENDATIONS[$this->recommendation] ?? null,
            'estimated_repair_cost'   => $this->estimated_repair_cost,
            'summary'                 => $this->summary,

            'progress' => $this->progress(),

            'technician' => $this->whenLoaded('technician', fn () => [
                'id'   => $this->technician->id,
                'name' => trim($this->technician->name.' '.($this->technician->lname ?? '')),
            ]),
            'branch' => $this->whenLoaded('branch', fn () => [
                'id'   => $this->branch->id ?? null,
                'name' => $this->branch->name ?? null,
            ]),
        ];
    }
}
