<?php

namespace App\Http\Resources;

use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $verdict = $this->calculatedVerdict();

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
            'vehicle_image'        => $this->vehicle_image,
            'vehicle_image_url'    => $this->vehicleImageUrl(),
            'region'               => $this->region,
            'fuel_type'            => $this->fuel_type,
            'gearbox'              => $this->gearbox,
            'cylinders'            => $this->cylinders,
            'body_type'            => $this->body_type,
            'number_of_keys'       => $this->number_of_keys,
            'with_service_history' => $this->with_service_history,
            'last_service_date'    => optional($this->last_service_date)->toDateString(),

            'odometer'              => $this->odometer,
            'overall_condition'       => $this->overall_condition,
            // Weighted templates (Comprehensive, Premium) return the Calculated
            // Overall Verdict in these same fields — same types as before
            // (overall_rating stays a one-decimal string). `recommendation` keeps
            // the stored code so the app's existing handling is unaffected.
            'overall_condition_label' => $verdict ? $verdict['band']['condition'] : (Inspection::CONDITIONS[$this->overall_condition] ?? null),
            'overall_rating'          => $verdict ? number_format($verdict['rating'], 1, '.', '') : $this->overall_rating,
            'recommendation'          => $this->recommendation,
            'recommendation_label'  => $verdict ? $verdict['band']['guide'] : (Inspection::RECOMMENDATIONS[$this->recommendation] ?? null),
            'recommendation_label_ar' => $verdict ? $verdict['band']['guide_ar'] : (Inspection::RECOMMENDATIONS_AR[$this->recommendation] ?? null),
            // Score /100, rating /5, condition and recommendation from the section
            // weights; null when the template has no weights or nothing is rated.
            'calculated_verdict'      => $this->calculatedVerdictPayload($verdict),
            'estimated_repair_cost' => $this->estimated_repair_cost,
            'currency'              => $this->currency ?? 'AED',
            'summary'               => $this->summary,
            'summary_ar'            => $this->summary_ar,

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
                        'weight'       => $section->weight,
                        'summary'      => optional($summaryBySection->get($section->id))->summary,
                        'rating'       => $rating(optional($summaryBySection->get($section->id))->rating),
                    ])->values();
                }
            ),

            // Damage diagrams for this inspection's template — the blank body view,
            // the marked-up PNG saved for it, the dots behind that PNG and the
            // palette they were drawn from. Only present when the section's
            // diagrams are loaded, so the endpoints that do not load them keep
            // the payload they had. Mirrors the web edit screen: active diagrams
            // whose image is on disk and whose palette is not empty.
            'damage_diagrams' => $this->when(
                $this->relationLoaded('type') && $this->type
                    && $this->type->relationLoaded('sections')
                    && $this->type->sections->every(fn ($s) => $s->relationLoaded('damageDiagrams')),
                function () {
                    return $this->type->sections
                        ->flatMap(fn ($section) => $section->damageDiagrams
                            ->filter(fn ($d) => $d->is_active && $d->imageExists())
                            ->map(function ($d) use ($section) {
                                $palette = $d->palette();

                                if ($palette->isEmpty()) {
                                    return null;
                                }

                                return [
                                    'id'           => $d->id,
                                    'key'          => $d->key,
                                    'name'         => $d->name,
                                    'sequence'     => $d->sequence,
                                    'section_id'   => $section->id,
                                    'section_name' => $section->section_name,

                                    // The blank diagram to draw on.
                                    'image_url' => $d->imageUrl(),
                                    // This section is not finished until the canvas is saved:
                                    // `is_saved` false means the technician has not marked it yet.
                                    'is_required' => true,
                                    'is_saved' => filled($this->damageImagePath($d->key)),
                                    // The flattened mark-up saved for this inspection,
                                    // null when this view has never been marked.
                                    'marked_image_url' => $this->damageDiagramUrl($d->key),

                                    // The dots themselves, in the diagram's own pixel
                                    // space — {x, y, c} where c is one of the palette
                                    // colours below. Redraw from image_url + marks to
                                    // keep single dots erasable.
                                    'marks' => $this->damageMarks($d->key),

                                    'colours' => $palette->map(fn ($c) => [
                                        'id'          => $c->id,
                                        'label'       => $c->label,
                                        'colour'      => $c->colour,
                                        'description' => $c->description,
                                        'sequence'    => $c->sequence,
                                    ])->values(),
                                ];
                            }))
                        ->filter()
                        ->values();
                }
            ),

            // Per-area summary notes (Exterior, Engine, Brakes, …) — template
            // Summary options, or tbl_summary_type when the template has none.
            'summaries' => $this->whenLoaded('summaries', function () {
                $types = $this->resource->summaryAreas();
                $typesAr = $this->resource->summaryAreas(true);
                $key = $this->resource->summaryKey();

                return $this->summaries->whereNotNull($key)->map(fn ($s) => [
                    'summary_type_id'      => (int) $s->{$key},
                    'summary_type_name'    => $types[$s->{$key}] ?? null,
                    'summary_type_name_ar' => $typesAr[$s->{$key}] ?? null,
                    'summary'              => $s->summary,
                    'summary_ar'           => $s->summary_ar,
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

            // Additional media — the bucket with neither a step nor a section
            // (the "Additional media" box on the web edit screen). Section
            // buckets also carry a null step id, so the section id must be
            // checked too or their photos leak into this list.
            'extra_media' => $this->whenLoaded('details', function () {
                $extra = $this->details
                    ->first(fn ($d) => is_null($d->inspection_step_id) && is_null($d->inspection_section_id));

                if (! $extra || ! $extra->relationLoaded('media')) {
                    return [];
                }

                return $extra->media->map(fn ($m) => [
                    'id'            => $m->id,
                    'type'          => $m->type,
                    'url'           => $m->url,
                    'label'         => $m->label,
                    'original_name' => $m->original_name,
                    'size'          => $m->size,
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
