<?php

namespace Database\Seeders;

use App\Models\InspectionSection;
use App\Models\InspectionStep;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The "Quick Inspection" template, built from the reference report
 * auto-assure.com/report/inspection/XCwZmPTXWI (LD01393, JETOUR X50, 03-09-2026).
 *
 * That report is a different shape from the Premium and Comprehensive ones: it
 * carries the vehicle card, a Vehicle Overview, one note per assessed area,
 * the photo gallery and the Damage Points canvas — and NO per-question
 * checklist and NO Vehicle Specifications tick lists. So this template has
 * neither: it is one section per area the report reports on, in its order.
 *
 *   Inspection Checklist — Exterior, Interior, Engine, Brakes, Transmission,
 *                          Undercarriage, Road Test
 *
 * Each section holds a single overall verdict for that area, which is exactly
 * what the report prints: an area heading, a Good/Bad/NA/Average judgement and
 * the technician's note, plus whatever photos were taken. A section cannot be
 * left empty — Inspection::sectionProgress() marks a section with no questions
 * as never done, which would make the inspection impossible to complete.
 *
 * Damage diagrams are not seeded: Full Body and Under Body already exist in
 * Damage Setup, and a diagram belongs to exactly one section.
 *
 * Like the other template seeders this declares a DESIRED STATE and is safe to
 * re-run: sections and questions are created or updated, anything left in this
 * type that is no longer listed here is removed along with its answers and
 * media, and no other template is touched.
 */
class QuickInspectionTemplateSeeder extends Seeder
{
    protected const TYPE_NAME = 'Quick Inspection';

    protected const TYPE_DESCRIPTION = 'Quick pre-purchase check — a visual assessment of the exterior, interior, '
        .'engine, brakes, transmission and undercarriage with photos of any faults, a damage mark-up and a short '
        .'note per area. No per-question checklist and no vehicle specification list. Based on the Auto Assure '
        .'Quick report.';

    protected const CHOICES = ['Good', 'Bad', 'NA', 'Average'];

    /** group => section => questions, all in display order. */
    protected const GROUPS = [
        'Inspection Checklist' => [
            'Exterior' => ['Exterior condition'],
            'Interior' => ['Interior condition'],
            'Engine' => ['Engine condition'],
            'Brakes' => ['Brakes'],
            'Transmission' => ['Transmission'],
            'Undercarriage' => ['Undercarriage / chassis condition'],
            'Road Test' => ['Road test'],
        ],
    ];

    public function run(): void
    {
        $type = InspectionType::firstOrNew(['name' => static::TYPE_NAME]);

        $type->fill([
            'description' => static::TYPE_DESCRIPTION,
            'is_active' => true,
            // Diagnostic Media is the photo bucket for these templates.
            'has_diagnostic_media' => true,
            'sequence' => $type->sequence ?: ((int) InspectionType::max('sequence') + 1),
        ])->save();

        $this->command?->info(($type->wasRecentlyCreated ? 'Created' : 'Updated')." template “{$type->name}” (id {$type->id}).");

        $sequence = 0;
        $keptSectionIds = [];
        $questionCount = 0;

        foreach (static::GROUPS as $group => $sections) {
            foreach ($sections as $sectionName => $questions) {
                $sequence++;

                $section = InspectionSection::updateOrCreate(
                    ['inspection_type_id' => $type->id, 'group_name' => $group, 'section_name' => $sectionName],
                    ['sequence' => $sequence]
                );
                $keptSectionIds[] = $section->id;

                $keptStepIds = [];
                foreach (array_values($questions) as $index => $question) {
                    $step = InspectionStep::updateOrCreate(
                        ['inspection_section_id' => $section->id, 'question' => $question],
                        [
                            'sequence' => $index + 1,
                            'show_multiple_choice' => true,
                            'multiple_choice_options' => static::CHOICES,
                            'show_rating' => false,
                            'show_text_answer' => true,   // Observation box — hidden until Bad or Average is picked
                            'show_remedial_suggestions' => false,
                            'photos' => InspectionStep::MEDIA_NOT_REQUIRED,
                            'videos' => InspectionStep::MEDIA_NOT_REQUIRED,
                        ]
                    );
                    $keptStepIds[] = $step->id;
                    $questionCount++;
                }

                // Questions removed from this section.
                $this->pruneSteps(
                    InspectionStep::where('inspection_section_id', $section->id)
                        ->whereNotIn('id', $keptStepIds)->pluck('id')->all()
                );
            }

            $this->command?->info("  {$group}: ".count($sections).' sections.');
        }

        // Sections removed from this template.
        $staleSections = InspectionSection::where('inspection_type_id', $type->id)
            ->whereNotIn('id', $keptSectionIds)
            ->pluck('id')->all();

        if ($staleSections) {
            $this->pruneSteps(
                InspectionStep::whereIn('inspection_section_id', $staleSections)->pluck('id')->all()
            );
            DB::table('inspection_section_summaries')->whereIn('inspection_section_id', $staleSections)->delete();
            InspectionSection::whereIn('id', $staleSections)->delete();
            $this->command?->warn('  Removed '.count($staleSections).' stale section(s).');
        }

        $this->command?->info("  {$questionCount} questions in ".count($keptSectionIds).' sections.');
    }

    /**
     * Delete steps plus the answers and media hanging off them.
     *
     * @param  array<int, int>  $stepIds
     */
    protected function pruneSteps(array $stepIds): void
    {
        if (! $stepIds) {
            return;
        }

        $detailIds = DB::table('inspection_details')->whereIn('inspection_step_id', $stepIds)->pluck('id')->all();

        if ($detailIds) {
            DB::table('inspection_media')->whereIn('inspection_detail_id', $detailIds)->delete();
            DB::table('inspection_details')->whereIn('id', $detailIds)->delete();
        }

        InspectionStep::whereIn('id', $stepIds)->delete();
    }
}
