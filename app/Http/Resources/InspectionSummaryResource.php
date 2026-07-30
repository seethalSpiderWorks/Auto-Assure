<?php

namespace App\Http\Resources;

use App\Models\Inspection;
use App\Models\InspectionSummary;
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

        $sections = collect($this->type?->sections ?? [])->map(function ($section) use ($byStep, $stateOf, $summaryBySection, $mediaBySection) {
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
                'description'  => $section->description,
                'summary'      => optional($summaryBySection->get($section->id))->summary,
                'rating'       => Inspection::sectionRating($section, $byStep, optional($summaryBySection->get($section->id))->rating),
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
        // Saved per-area summary notes (inspection_summaries), keyed by summary_type_id.
        $savedSummaries = $this->relationLoaded('summaries')
            ? $this->summaries->pluck('summary', 'summary_type_id')
            : collect();

        // All active summary types with their saved notes, in lookup order.
        $summaryAreas = collect(InspectionSummary::types())->map(fn ($name, $id) => [
            'id'      => $id,
            'name'    => $name,
            'summary' => $savedSummaries->get($id),
        ])->values();

        $progressTotal    = (int) $sections->sum('total');
        $progressAnswered = (int) $sections->sum('answered');

        return [
            'id'              => $this->id,
            'reference'       => $this->reference,
            'status'          => $this->status,
            'inspection_type' => $this->whenLoaded('type', fn () => $this->type->name),

            'scheduled_at' => optional($this->scheduled_at)->toIso8601String(),
            'started_at'   => optional($this->started_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),

            'customer' => [
                'name'    => $this->customer_name,
                'name_ar' => $this->customer_name_ar,
                'email'   => $this->customer_email,
                'phone'   => $this->customer_phone,
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
                'region'               => $this->region,
                'body_type'            => $this->body_type,
                'fuel_type'            => $this->fuel_type,
                'gearbox'              => $this->gearbox,
                'cylinders'            => $this->cylinders,
                'steering_side'        => $this->steering_side,
                'number_of_keys'       => $this->number_of_keys,
                'odometer'             => $this->odometer,
                'with_service_history' => $this->with_service_history,
                'last_service_date'    => optional($this->last_service_date)->toDateString(),
            ],

            'verdict' => [
                'overall_condition'       => $this->overall_condition,
                'overall_condition_label' => (function () {
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
                'overall_rating'          => $this->overall_rating,
                'recommendation'          => $this->recommendation,
                'recommendation_label'    => Inspection::RECOMMENDATIONS[$this->recommendation] ?? null,
                'estimated_repair_cost'   => $this->estimated_repair_cost,
                'currency'                => $this->currency ?? 'AED',
                'summary'                 => $this->summary,
            ],

            'progress' => [
                'answered' => $progressAnswered,
                'total'    => $progressTotal,
                'percent'  => $progressTotal > 0 ? (int) round($progressAnswered / $progressTotal * 100) : 0,
            ],

            'sections' => $sections,

            // Per-area summary notes from inspection_summaries (Exterior, Interior, Engine, Brakes, …).
            'summary_areas' => $summaryAreas,

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
