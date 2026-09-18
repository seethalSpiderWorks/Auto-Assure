<?php

namespace App\Http\Resources;

use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full section-wise inspection summary — the same data the web "details" screen
 * renders: vehicle + customer, the verdict, overall completion, and every
 * section with its steps, each step's answer (choice / rating / text / remedial),
 * its pass/fail/na state and attached media.
 *
 * Expects the inspection to be loaded with: type.sections.steps, details.media.
 */
class InspectionSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // step id => saved answer (detail). Section/extra media buckets carry a
        // null step id, so they are excluded here — keyBy would collapse them all
        // onto one key and hide their media.
        $byStep = $this->details
            ->filter(fn ($d) => ! is_null($d->inspection_step_id))
            ->keyBy('inspection_step_id');

        // section id => media uploaded against the section itself (not a step).
        // These details have a null step id and a set section id.
        $mediaBySection = $this->details
            ->filter(fn ($d) => is_null($d->inspection_step_id) && ! is_null($d->inspection_section_id))
            ->mapWithKeys(fn ($d) => [$d->inspection_section_id => $this->mediaItems($d)]);

        // Media with neither a step nor a section — the "extra"/additional bucket.
        $extraMedia = $this->mediaItems(
            $this->details->first(fn ($d) => is_null($d->inspection_step_id) && is_null($d->inspection_section_id))
        );

        // section id => per-section summary + rating
        $summaryBySection = $this->relationLoaded('sectionSummaries')
            ? $this->sectionSummaries->keyBy('inspection_section_id')
            : collect();

        // Pass / Fail / N-A for a saved answer — same rule used by the report and
        // the web details/summary screens.
        $stateOf = fn ($d): string => Inspection::choiceState($d);

        $verdict = $this->calculatedVerdict();

        // A recorded section rating, formatted exactly as Inspection::sectionRating()
        // formats one (clamp 0.5–5, one decimal); null when nothing was recorded.
        $sectionRating = fn ($manual): ?float => filled($manual) && (float) $manual > 0
            ? round(max(0.5, min(5, (float) $manual)), 1)
            : null;

        $sections = collect($this->type?->sections ?? [])->map(function ($section) use ($byStep, $stateOf, $sectionRating, $summaryBySection, $mediaBySection) {
            $steps = $section->steps->map(function ($step) use ($byStep, $stateOf) {
                $d = $byStep->get($step->id);

                return [
                    'id'                  => $step->id,
                    'sequence'            => $step->sequence,
                    'question'            => $step->question,
                    'description'         => $step->description,
                    'answered'            => Inspection::detailIsAnswered($d),
                    'state'               => $stateOf($d),          // pass | fail | na
                    'rating'              => $d->rating ?? null,
                    'choice'              => $d->choice ?? null,
                    'descriptive_answer'  => $d->descriptive_answer ?? null,
                    'remedial_suggestion' => $d->remedial_suggestion ?? null,
                    'media'               => $this->mediaItems($d),
                ];
            })->values();

            $total    = $steps->count();
            $answered = $steps->where('answered', true)->count();

            return [
                'id'           => $section->id,
                'section_name' => $section->section_name,
                'sequence'     => $section->sequence,
                'weight'       => $section->weight,
                'description'  => $section->description,
                'summary'      => optional($summaryBySection->get($section->id))->summary,
                // Only what the technician actually recorded — null when the section
                // was never rated. Same value and formatting Inspection::sectionRating()
                // gives a recorded rating (clamped to 0.5–5, one decimal, so 4.6 stays
                // 4.6); only its derived fallback is dropped, since that invented a
                // star for a section nobody assessed. Matches section_summaries[] below
                // and the printed report.
                'rating'       => $sectionRating(optional($summaryBySection->get($section->id))->rating),
                'total'        => $total,
                'answered'     => $answered,
                'done'         => $total > 0 && $answered >= $total,
                // Media uploaded against the section as a whole, separate from
                // the per-step media inside 'steps'.
                'media'        => $mediaBySection->get($section->id, []),
                'steps'        => $steps,
            ];
        })->values();

        // Completion computed the SAME way as the web details screen: over the
        // inspection type's steps, counting only steps with a meaningful answer
        // (Inspection::detailIsAnswered). This deliberately does NOT use the
        // model's progress(), which counts raw detail rows (incl. empty ones and
        // answers for steps outside the current type) and can disagree.
        // Saved per-area summary notes (inspection_summaries), keyed by area id.
        $savedSummaries = collect($this->relationLoaded('summaries') ? $this->resource->summaryNotes() : []);
        $savedSummariesAr = collect($this->relationLoaded('summaries') ? $this->resource->summaryNotes('summary_ar') : []);
        $typesAr = $this->resource->summaryAreas(true);

        // Every summary area (template options, or tbl_summary_type when the
        // template has none) with its saved note, in display order.
        $summaryAreas = collect($this->resource->summaryAreas())->map(fn ($name, $id) => [
            'id'         => $id,
            'name'       => $name,
            'name_ar'    => $typesAr[$id] ?? null,
            'summary'    => $savedSummaries->get($id),
            'summary_ar' => $savedSummariesAr->get($id),
        ])->values();

        // The OTHER summary: the note + star the technician records against each
        // checklist section (inspection_section_summaries), printed per section on
        // the report. Distinct from $summaryAreas above, which is the per-area
        // block (Exterior, Engine, Brakes…) from tbl_summary_type.
        //
        // 'rating' here is only what was actually recorded — matching the report
        // (report.blade.php:672), which deliberately skips Inspection::sectionRating()
        // so no stars are printed for a section nobody assessed. sections[].rating
        // now follows the same rule, so the two agree.
        $sectionSummaries = collect($this->type?->sections ?? [])->map(function ($section) use ($summaryBySection) {
            $meta = $summaryBySection->get($section->id);

            return [
                'section_id'   => $section->id,
                'section_name' => $section->section_name,
                'sequence'     => $section->sequence,
                'weight'       => $section->weight,
                'rating'       => $meta?->rating !== null ? (float) $meta->rating : null,
                'summary'      => $meta?->summary,
            ];
        })->values();

        // Damage diagrams — the same block GET /inspections/{inspection} returns,
        // so the app parses one shape either way: the blank body view, the
        // mark-up saved for this inspection, the dots behind it and the palette
        // they were drawn from. Empty when the template carries no diagram.
        $damageDiagrams = collect($this->type?->sections ?? [])
            ->filter(fn ($section) => $section->relationLoaded('damageDiagrams'))
            ->flatMap(fn ($section) => $section->damageDiagrams
                ->filter(fn ($d) => $d->is_active && $d->imageExists())
                ->map(function ($d) use ($section) {
                    $palette = $d->palette();

                    if ($palette->isEmpty()) {
                        return null;
                    }

                    return [
                        'id'               => $d->id,
                        'key'              => $d->key,
                        'name'             => $d->name,
                        'sequence'         => $d->sequence,
                        'section_id'       => $section->id,
                        'section_name'     => $section->section_name,
                        'image_url'        => $d->imageUrl(),
                        // This section is not finished until the canvas is saved:
                        // `is_saved` false means the technician has not marked it yet.
                        'is_required' => true,
                        'is_saved' => filled($this->damageImagePath($d->key)),
                        'marked_image_url' => $this->damageDiagramUrl($d->key),
                        'marks'            => $this->damageMarks($d->key),
                        'colours'          => $palette->map(fn ($c) => [
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

        $progressTotal    = (int) $sections->sum('total');
        $progressAnswered = (int) $sections->sum('answered');

        return [
            'id'              => $this->id,
            'reference'       => $this->reference,
            'status'          => $this->status,
            'inspection_type' => $this->whenLoaded('type', fn () => $this->type->name),
            // The template's "Do you want to use the Calculated Overall Verdict?"
            // switch: 1 = show verdict.calculated_verdict and its Recommendations,
            // 0 = no calculated verdict for this inspection.
            'has_calculated_verdict' => $this->usesCalculatedVerdict() ? 1 : 0,

            'scheduled_at' => optional($this->scheduled_at)->toIso8601String(),
            'started_at'   => optional($this->started_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),

            // Cancellation record — null unless status is "cancelled".
            'is_cancelled'       => $this->isCancelled(),
            'cancelled_at'       => optional($this->cancelled_at)->toIso8601String(),
            'cancelled_at_label' => optional($this->cancelled_at)->format('d M Y, h:i A'),
            'cancel_reason'      => $this->cancel_reason,
            'cancelled_by'       => $this->cancelled_by,
            'cancelled_by_name'  => $this->whenLoaded('cancelledBy', fn () => $this->cancelledBy?->name),

            'customer' => [
                'name'    => $this->customer_name,
                'name_ar' => $this->customer_name_ar,
                'email'   => $this->customer_email,
                'phone'   => $this->customer_phone,
                // Keyed as whatsapp_number, not whatsapp, to match the column and
                // InspectionResource — the app reads both endpoints.
                'whatsapp_number' => $this->whatsapp_number,
            ],

            'vehicle' => [
                'name'                => $this->car(),
                'make'                => $this->car_make,
                'model'               => $this->car_model,
                'year'                => $this->car_year,
                'manufacturing_year'   => $this->manufacturing_year,
                'vehicle_condition'    => $this->vehicle_condition,
                'vin'                  => $this->vin,
                'plate_no'             => $this->plate_no,
                'exterior_color'       => $this->exterior_color,
                'vehicle_image'        => $this->vehicle_image,
                'vehicle_image_url'    => $this->vehicleImageUrl(),
                'region'               => $this->region,
                'body_type'            => $this->body_type,
                'fuel_type'            => $this->fuel_type,
                'gearbox'              => $this->gearbox,
                'cylinders'            => $this->cylinders,
                'number_of_keys'       => $this->number_of_keys,
                'odometer'             => $this->odometer,
                'with_service_history' => $this->with_service_history,
                'last_service_date'    => optional($this->last_service_date)->toDateString(),
            ],

            // Weighted templates (Comprehensive, Premium) return the Calculated
            // Overall Verdict in these same fields, same types as before;
            // `recommendation` keeps the stored code.
            'verdict' => [
                'overall_condition'       => $this->overall_condition,
                'overall_condition_label' => $verdict ? $verdict['band']['condition'] : (function () {
                    $rating = (float) ($this->overall_rating ?? 0);
                    return match (true) {
                        $rating >= 4.5 => 'Excellent',
                        $rating >= 3.5 => 'Very Good',
                        $rating >= 2.5 => 'Good',
                        $rating >= 1.5 => 'Fair',
                        $rating > 0    => 'Poor',
                        default        => Inspection::CONDITIONS[$this->overall_condition] ?? null,
                    };
                })(),
                'overall_rating'          => $verdict ? number_format($verdict['rating'], 1, '.', '') : $this->overall_rating,
                'recommendation'          => $this->recommendation,
                'recommendation_label'    => $verdict ? $verdict['band']['guide'] : (Inspection::RECOMMENDATIONS[$this->recommendation] ?? null),
                'recommendation_label_ar' => $verdict ? $verdict['band']['guide_ar'] : (Inspection::RECOMMENDATIONS_AR[$this->recommendation] ?? null),
                'calculated_verdict'      => $this->calculatedVerdictPayload($verdict),
                'estimated_repair_cost'   => $this->estimated_repair_cost,
                'currency'                => $this->currency ?? 'AED',
                'summary'                 => $this->summary,
                'summary_ar'              => $this->summary_ar,
            ],

            'progress' => [
                'answered' => $progressAnswered,
                'total'    => $progressTotal,
                'percent'  => $progressTotal > 0 ? (int) round($progressAnswered / $progressTotal * 100) : 0,
            ],

            'sections' => $sections,

            // Per-area summary notes from inspection_summaries (Exterior, Interior, Engine, Brakes, …).
            'summary_areas' => $summaryAreas,

            // Per-section note + recorded star from inspection_section_summaries —
            // the section-wise summary the report prints under each section header.
            'section_summaries' => $sectionSummaries,

            // The marked-up damage canvases for this inspection.
            'damage_diagrams' => $damageDiagrams,

            // Additional media not tied to any step or section (the "extra"
            // bucket — a detail row with both ids null).
            'extra_media' => $extraMedia,

            // Every photo/video on the inspection in one flat list, so the app
            // can show a gallery without walking sections and steps.
            'media' => $this->details->flatMap(fn ($d) => $this->mediaItems($d))->values(),
        ];
    }

    /**
     * Media rows of one detail, in the shape the app consumes. Returns [] when
     * the detail is missing or its media relation was not eager-loaded.
     *
     * @return array<int, array<string, mixed>>
     */
    private function mediaItems($detail): array
    {
        if (! $detail || ! $detail->relationLoaded('media')) {
            return [];
        }

        return $detail->media->map(fn ($m) => [
            'id'            => $m->id,
            'type'          => $m->type,
            'url'           => $m->url,
            'label'         => $m->label,
            'original_name' => $m->original_name,
            'size'          => $m->size,
        ])->values()->all();
    }
}
