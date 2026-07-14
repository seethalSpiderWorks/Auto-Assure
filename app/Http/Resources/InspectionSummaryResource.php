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
        // step id => saved answer (detail)
        $byStep = $this->details->keyBy('inspection_step_id');

        // section id => per-section summary + rating
        $summaryBySection = $this->relationLoaded('sectionSummaries')
            ? $this->sectionSummaries->keyBy('inspection_section_id')
            : collect();

        // Pass / Fail / N-A for a saved answer — same rule used by the report and
        // the web details/summary screens.
        $stateOf = fn ($d): string => Inspection::choiceState($d);

        $sections = collect($this->type?->sections ?? [])->map(function ($section) use ($byStep, $stateOf, $summaryBySection) {
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
                    'media'               => ($d && $d->relationLoaded('media'))
                        ? $d->media->map(fn ($m) => [
                            'id'   => $m->id,
                            'type' => $m->type,
                            'url'  => $m->url,
                        ])->values()
                        : [],
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
                'steps'        => $steps,
            ];
        })->values();

        // Completion computed the SAME way as the web details screen: over the
        // inspection type's steps, counting only steps with a meaningful answer
        // (Inspection::detailIsAnswered). This deliberately does NOT use the
        // model's progress(), which counts raw detail rows (incl. empty ones and
        // answers for steps outside the current type) and can disagree.
        $progressTotal    = (int) $sections->sum('total');
        $progressAnswered = (int) $sections->sum('answered');

        return [
            'id'              => $this->id,
            'status'          => $this->status,
            'inspection_type' => $this->whenLoaded('type', fn () => $this->type->name),

            'scheduled_at' => optional($this->scheduled_at)->toIso8601String(),
            'started_at'   => optional($this->started_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),

            'customer' => [
                'name'  => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
            ],

            'vehicle' => [
                'name'                => $this->car(),
                'make'                => $this->car_make,
                'model'               => $this->car_model,
                'year'                => $this->car_year,
                'vin'                 => $this->vin,
                'registration_number' => $this->registration_number,
                'variant'             => $this->variant,
                'color'               => $this->color,
                'body_type'           => $this->body_type,
                'vehicle_type'        => $this->vehicle_type,
                'fuel_type'           => $this->fuel_type,
                'transmission'        => $this->transmission,
                'cylinders_cc'        => $this->cylinders_cc,
                'motor_power_kw'      => $this->motor_power_kw,
                'passengers'          => $this->passengers,
                'number_of_keys'      => $this->number_of_keys,
                'odometer'            => $this->odometer,
                'fuel_economy'        => $this->fuel_economy,
                'manufacturer_name'   => $this->manufacturer_name,
                'country_of_origin'   => $this->country_of_origin,
                'country_of_export'   => $this->country_of_export,
            ],

            'verdict' => [
                'overall_condition'       => $this->overall_condition,
                'overall_condition_label' => Inspection::CONDITIONS[$this->overall_condition] ?? null,
                'recommendation'          => $this->recommendation,
                'recommendation_label'    => Inspection::RECOMMENDATIONS[$this->recommendation] ?? null,
                'estimated_repair_cost'   => $this->estimated_repair_cost,
                'summary'                 => $this->summary,
            ],

            'progress' => [
                'answered' => $progressAnswered,
                'total'    => $progressTotal,
                'percent'  => $progressTotal > 0 ? (int) round($progressAnswered / $progressTotal * 100) : 0,
            ],

            'sections' => $sections,
        ];
    }
}
