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
            // Ready-to-display schedule, e.g. "24 Jul 2026, 02:30 PM".
            'scheduled_at_label' => optional($this->scheduled_at)->format('d M Y, h:i A'),
            'started_at'   => optional($this->started_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),

            // Cancellation record — null unless status is "cancelled".
            'is_cancelled'       => $this->isCancelled(),
            'cancelled_at'       => optional($this->cancelled_at)->toIso8601String(),
            'cancelled_at_label' => optional($this->cancelled_at)->format('d M Y, h:i A'),
            'cancel_reason'      => $this->cancel_reason,
            'cancelled_by'       => $this->cancelled_by,
            'cancelled_by_name'  => $this->whenLoaded('cancelledBy', fn () => $this->cancelledBy?->name),

            'customer_name'    => $this->customer_name,
            'customer_name_ar' => $this->customer_name_ar,
            'customer_email'   => $this->customer_email,
            'customer_phone'   => $this->customer_phone,
            'whatsapp_number'  => $this->whatsapp_number,

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
            'overall_condition'       => $this->overall_condition,
            'overall_condition_label' => Inspection::CONDITIONS[$this->overall_condition] ?? null,
            'overall_rating'          => $this->overall_rating,
            'recommendation'          => $this->recommendation,
            'recommendation_label'  => Inspection::RECOMMENDATIONS[$this->recommendation] ?? null,
            'estimated_repair_cost' => $this->estimated_repair_cost,
            'currency'              => $this->currency ?? 'AED',
            'summary'               => $this->summary,

            'progress' => $this->progress(),

            // Per-section summary + rating (manual rating if set, else derived from answers).
            'section_summaries' => $this->when(
                $this->relationLoaded('type') && $this->type && $this->type->relationLoaded('sections'),
                function () {
                    $summaryBySection = $this->relationLoaded('sectionSummaries')
                        ? $this->sectionSummaries->keyBy('inspection_section_id')
                        : collect();

                    // Only the rating the technician actually recorded — null when the
                    // section was never rated. Formatted exactly as
                    // Inspection::sectionRating() formats a recorded one (clamp 0.5–5,
                    // one decimal, so 4.6 stays 4.6); only its derived fallback is
                    // dropped, since that invented a star from the answers for a section
                    // nobody assessed. Matches the summary endpoint and the report.
                    $rating = fn ($manual): ?float => filled($manual) && (float) $manual > 0
                        ? round(max(0.5, min(5, (float) $manual)), 1)
                        : null;

                    return $this->type->sections->map(fn ($section) => [
                        'section_id'   => $section->id,
                        'section_name' => $section->section_name,
                        'summary'      => optional($summaryBySection->get($section->id))->summary,
                        'rating'       => $rating(optional($summaryBySection->get($section->id))->rating),
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

            // Section-level media (photos/videos not tied to a specific step).
            // Grouped by section so the mobile app can render them per category.
            'section_media' => $this->when(
                $this->relationLoaded('details') && $this->relationLoaded('type') && $this->type && $this->type->relationLoaded('sections'),
                function () {
                    $sections = $this->type->sections->keyBy('id');

                    return $this->details
                        ->filter(fn ($d) => is_null($d->inspection_step_id) && ! is_null($d->inspection_section_id))
                        ->map(fn ($d) => [
                            'section_id'   => (int) $d->inspection_section_id,
                            'section_name' => optional($sections->get($d->inspection_section_id))->section_name,
                            'media'        => $d->relationLoaded('media')
                                ? $d->media->map(fn ($m) => [
                                    'id'   => $m->id,
                                    'type' => $m->type,
                                    'url'  => $m->url,
                                ])->values()
                                : [],
                        ])->values();
                }
            ),

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
