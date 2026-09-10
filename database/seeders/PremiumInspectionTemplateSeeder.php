<?php

namespace Database\Seeders;

use App\Models\InspectionSection;
use App\Models\InspectionStep;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The "Premium Inspection" template, transcribed verbatim from the reference
 * report auto-assure.com/report/inspection/IKdvlWOuqR (LD01405, Range Rover
 * Velar, 06-09-2026) at the client's instruction: the template holds the items
 * that report lists, and nothing else.
 *
 *   Vehicle Specifications — Performance, Safety, Interior - Entertainment,
 *                            Aftermarket Added Accessories
 *   Inspection Checklist   — Exterior, Interior, Tyre, Engine, Transmission,
 *                            Electrical, Underbody, Test Drive
 *
 * Worth knowing when this is next revised: a legacy report prints only what the
 * technician assessed on that car, so two Premium reports do not list the same
 * items. The earlier reference (LUFS9FaNrR) carries Molding, Seat Belts, Horn,
 * A/C Control & Cooling, Coolant Conditions, Suspension Noise and Road Holding
 * Stability, which this one omits; this one carries Turbo/ Supercharger,
 * Catalytic Converter and Instruments and Controls Functioning, which that one
 * omits. Spec lists are per-car equipment, not a menu of options. Anything not
 * listed below therefore cannot be recorded on a Premium inspection.
 *
 * Damage diagrams are deliberately NOT seeded: Full Body and Under Body already
 * exist in Damage Setup. A diagram belongs to exactly one section, so which
 * section shows a canvas is set there, not here.
 *
 * Like InspectionTemplateSeeder this declares a DESIRED STATE and is safe to
 * re-run: sections and questions are created or updated, and anything left in
 * this type that is no longer listed here is removed along with its answers and
 * media. Only the Premium type is ever touched.
 */
class PremiumInspectionTemplateSeeder extends Seeder
{
    private const TYPE_NAME = 'Premium Inspection';

    private const TYPE_DESCRIPTION = 'Premium pre-purchase inspection — engine and mechanical check, computerised '
        .'diagnostics, transmission, suspension, steering, underbody and chassis, electrical and battery health, '
        .'tyres and brakes, a full interior and exterior check with photos, and a road test based on the seller\'s '
        .'approval. Based on the Auto Assure Premium report.';

    private const CHOICES = ['Good', 'Bad', 'NA', 'Average'];

    /** group => section => questions, all in display order. */
    private const GROUPS = [
        // Transcribed verbatim from the reference report, in the order it prints
        // them and with its wording.
        'Vehicle Specifications' => [
            'Performance' => [
                'Air Suspension', 'Differential Lock', 'Paddle Shifters', 'Auto Hold',
            ],
            'Safety' => [
                'Child Safety Seats (ISOFIX)', 'Rear View Camera', 'Front Parking Sensors',
                'Rear Parking Sensors', 'Anti-Lock Brakes (ABS)', 'EBD', 'Alarm', 'Front Airbags',
                'Side Airbags', 'Traction Control System', 'Blind Spot Monitor', 'Tyre Pressure Monitor',
                'Anti-Glare Rear View Mirror',
            ],
            'Interior - Entertainment' => [
                'Digital Driver Display', 'SD Card Player', 'Bluetooth Interface', 'Premium Sound System',
                'AUX Audio System', 'USB', 'Touchscreen', 'Navigation', 'Standard A/C', 'Keyless Entry',
                'Keyless Start', 'Power Steering', 'Passenger Memory Seat', 'Power Driver Seats',
                'Power Passenger Seats', 'Power Front Windows', 'Power Rear Windows', 'Power Trunk',
                'Power Locks', 'Power Mirrors', 'Power Folding Mirrors', 'Sunroof', 'Panoramic Roof',
                'Leather Seats', 'Front Fog Lights', 'Halogen Headlight',
            ],
            'Aftermarket Added Accessories' => [
                'Fire Extinguisher',
            ],
        ],

        'Inspection Checklist' => [
            'Exterior' => [
                'Door Locks / Operation', 'Fuel Filler Cover / Petrol', 'Glass', 'Bumper Grills',
                'Front Bumper', 'Rear Bumper', 'Front Left Headlights', 'Front Right Headlights',
                'Rear Left Tail Lights', 'Rear Right Tail Lights', 'General Body Condition',
            ],
            'Interior' => [
                'Headliner', 'Rearview Mirror', 'Steering Wheel', 'Gear Lever', 'Sun Visor',
                'Pillar Trim', 'Armrest Console', 'Floor Mats & Carpets', 'Trunk Liner', 'Dashboard',
                'Seats', 'Door Trims', 'A/C Grills', 'Sunroof Shade Liner',
            ],
            'Tyre' => [
                'Spare Tyre', 'Front Left Tyre', 'Back Right Tyre', 'Front Right Tyre', 'Back Left Tyre',
            ],
            'Engine' => [
                'Coolant Level', 'Coolant Leaks', 'Brake Master and Booster', 'Evidence of Overheating',
                'Radiator Cap', 'Radiator Fan', 'Fender Liner', 'Hoses Pipes', 'Cables, Harness & Connectors',
                'Engine Oil Level', 'External Engine Leaks', 'Engine Mounts', 'Turbo/ Supercharger',
                'Fuel Pump & Pipes', 'Cold Starting', 'Fast Idle When The Engine Cold',
                'Noise Level When The Engine Cold', 'Excess Smoke (Minor/Major)', 'Inlet Manifold',
                'Outlet Manifold', 'Exhaust Pipes', 'Silencer', 'Head Shields & Mountings',
                'Joints & Couplings', 'Engine Underside Leaks', 'Catalytic Converter', 'Engine Shield',
            ],
            'Transmission' => [
                'Gear Selector', 'Gear Shifting', 'Transmission Mount (Gear Mount)', 'Gear Noise',
                'Fluid Level & Oil Leak',
            ],
            'Electrical' => [
                'Door Locks (which side)', 'Central Locking', 'Ignition Lock / Starting System',
                'Instrument Panel', 'Headlights', 'Sidelights / Running Lights', 'Rear Lights',
                'Indicator / Hazard Lights', 'Boot / Tailgate Lock', 'Reverse Lights', 'Fog Lights',
                'Multimedia', 'Side Mirror', 'Auxiliary Lights', 'Panel Lights', 'Window Operation',
                'Sunroof Operation', 'Wipers Jet Washers', 'Keys Remote Controls', 'Warning Lights',
                'Number Plate Light',
            ],
            'Underbody' => [
                'Steering Joints and Ball Joints', 'Brakes Lines', 'Subframe',
                'Power Steering/ Steering Rack', 'Wheels, Hubs, and Bearings', 'Dampers and Bushes',
                'Evidence of Floor/Chassis Corrosion',
            ],
            'Test Drive' => [
                'Engine - Performance', 'Gearbox Operation', 'Steering Operation', 'Brake Operation',
                'Hand Brake/ Parking Brake', 'Drive Train (4WD,2WD,AWD)',
                'Instruments and Controls Functioning', 'Shock Absorber', 'Noise',
            ],
        ],
    ];

    public function run(): void
    {
        $type = InspectionType::firstOrNew(['name' => self::TYPE_NAME]);

        $type->fill([
            'description' => self::TYPE_DESCRIPTION,
            'is_active' => true,
            // The Premium package includes the advanced computerised diagnostic
            // check, so its inspections carry diagnostic media.
            'has_diagnostic_media' => true,
            'sequence' => $type->sequence ?: ((int) InspectionType::max('sequence') + 1),
        ])->save();

        $this->command?->info(($type->wasRecentlyCreated ? 'Created' : 'Updated')." template “{$type->name}” (id {$type->id}).");

        $sequence = 0;
        $keptSectionIds = [];
        $questionCount = 0;

        foreach (self::GROUPS as $group => $sections) {
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
                            'multiple_choice_options' => self::CHOICES,
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
    private function pruneSteps(array $stepIds): void
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
