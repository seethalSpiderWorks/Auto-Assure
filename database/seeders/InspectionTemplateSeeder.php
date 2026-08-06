<?php

namespace Database\Seeders;

use App\Models\InspectionSection;
use App\Models\InspectionStep;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The inspection checklist template, mirroring the legacy /inspectionreport tabs:
 *
 *   Vehicle Specifications — Performance, Safety, Interior - Entertainment, After Market
 *   Inspection Checklist   — Exterior, Interior, Tyre, Engine, Transmission,
 *                            Electrical, Underbody, Test Drive
 *
 * Every question is a Good / Bad / NA / Average multiple choice with no photo
 * or video requirement ("Attach Image: No").
 *
 * This seeder declares the DESIRED STATE of the two groups it manages: sections
 * and questions are created or updated, and anything left in those groups that
 * is no longer listed here is removed (along with any answers/media attached to
 * it). Groups outside GROUPS are never touched. Safe to re-run.
 */
class InspectionTemplateSeeder extends Seeder
{
    private const CHOICES = ['Good', 'Bad', 'NA', 'Average'];

    /** group => section => questions, all in display order. */
    private const GROUPS = [
        'Vehicle Specifications' => [
            'Performance' => [
                'Air Suspension', 'Adaptive Air Suspension', 'Differential Lock', 'Paddle Shifters',
                'Tiptronic', 'Hill Descent Assist', 'Hill Start Assist', 'Auto Hold', 'Comfort Seats',
                'Sport Seats', 'Sport Brakes', 'Sport Suspension', 'Sport Exhaust', 'Lane Change',
                'Assist Launch Control',
            ],
            'Safety' => [
                'Child Safety Seats (ISOFIX)', 'Front View Camera', 'Rear View Camera', '360 Degree Camera',
                'Front Parking Sensors', 'Rear Parking Sensors', 'Lane Departure', 'Anti-Lock Brakes (ABS)',
                'EBD', 'Alarm', 'Front Airbags', 'Side Airbags', 'Traction Control System', 'Park Assist',
                'Blind Spot Monitor', 'Tire Pressure Monitor', 'Anti Glare Rear View Mirror',
            ],
            'Interior - Entertainment' => [
                'Digital Driver Display', 'CD Player', 'DVD Player', 'MP3 Player', 'SD Card Player',
                'Bluetooth Interface', 'Premium Sound System', 'AUX Audio System', 'USB', 'USB-C',
                'Touch Screen', 'Rear Seat Entertain. Sys', 'Wireless', 'Ambient Lighting', 'Apple CarPlay',
                'Navigation', 'Standard AC', 'Dual-Zone Climate Ctrl AC', 'Multi-Zone Climate Ctrl AC',
                'Keyless Entry', 'Keyless Start', 'Power Steering', 'Heads Up Display', 'Cruise Control',
                'Adaptive Cruise Control', 'Seat Cooling Front', 'Seat Cooling Rear', 'Seat Massage Front',
                'Seat Massage Rear', 'Driver Memory Seat', 'Passenger Memory Seat', 'Power Driver Seats',
                'Power Passenger Seats', 'Power Rear Seats', 'Power Front Windows', 'Power Rear Windows',
                'Power Trunk', 'Power Locks', 'Power Mirrors', 'Power Folding Mirrors', 'Sun Roof',
                'Panoramic Roof', 'Cool Box', 'Seat Heated Front', 'Auto Park', 'Remote Start Engine',
                'Soft Close Doors', 'Adaptive Lights', 'Night Vision', 'Captain Rear Seats', 'Leather Seats',
                'Fabric Seats', 'Body Kit', 'Lift Kit', 'Front Spoiler', 'Rear Spoiler', 'Fog Light Front',
                'Roof Carrier Front', 'Halogen Headlight', 'LED Headlight', 'Xenon Headlight',
                'Trailer Hook Coupling',
            ],
            'After Market' => [
                'Winch', 'Body Kit', 'Lift Kit', 'Leather Seats', 'Rear Seat Entertain. Sys',
                'Parking Sensors', 'Rear View Camera', 'Navigation', 'Fire extinguisher',
            ],
        ],

        'Inspection Checklist' => [
            'Exterior' => [
                'Fuel filler cover/Petrol Cap', 'Door locks / operation', 'Glass', 'Molding',
                'Bumper Grills', 'Front bumper', 'Rear bumper', 'Front left headlights',
                'Front right headlights', 'Rear left tail lights', 'Rear right tail lights',
                'General body condition',
            ],
            'Interior' => [
                'Seat belts', 'Headliner', 'Rearview mirror', 'Steering wheel', 'Gear lever',
                'Sun visor', 'Pillar trim', 'Armrest console', 'Floor mats and carpets', 'Trunk liner',
                'Dashboard', 'Glove compartment', 'Seats', 'Door trims', 'A/C grills',
                'Sunroof shade / Sunroof liner',
            ],
            'Tyre' => [
                'Spare Tyre', 'Front Left Tyre', 'Back Right Tyre', 'Front Right Tyre', 'Back Left Tyre',
            ],
            'Engine' => [
                'Coolant level', 'Coolant leaks', 'Brake master and booster', 'Evidence of overheating',
                'Coolant Conditions', 'Radiator cap', 'Radiator fan', 'Fender liner', 'Hoses & pipes',
                'Cable, harnes & connectors', 'Power steering fluid level', 'Engine oil level',
                'External engine leaks', 'Engine mounts', 'Turbo/ Supercharger', 'Fuel pump & pipes',
                'Cold starting', 'Fast idle when engine cold', 'Noise lvl whn engine cold',
                'Excess Smoke(minor/major)', 'Inlet manifold', 'Outlet manifold', 'Exhaust Pipes',
                'Silencer(s)', 'Head shields & mountings', 'Joints & couplings', 'Engine underside leaks',
                'Catalytic converter', 'Engine shield',
            ],
            'Transmission' => [
                'Gear selector', 'Gear shifting', 'Gear noise', 'Fluid Level & Oil Leak',
                'Transmission Mount (Gear Mount)',
            ],
            'Electrical' => [
                'Door locks', 'Central Locking', 'Ignition lock/Starting sys', 'Instrument panel',
                'Headlights', 'Sidelights / Running lights', 'Rear lights', 'Indicator / Hazard lights',
                'Boot / Tailgate lock', 'Reverse lights', 'Fog lights', 'Multimedia',
                'A/C Control & Cooling', 'Side Mirror', 'Auxiliary lights', 'Panel lights', 'Horn',
                'Window operation', 'Sunroof operation', 'Wipers / Jet washers', 'Keys & remote controls',
                'Warning lights', 'Number plate lights',
            ],
            'Underbody' => [
                'Steering joints & ball joints', 'Brakes lines', 'Subframe', 'Wheels, hubs & bearings',
                'Dampers and bushes', 'Power steering/ rack', 'Evidence of floor/chassis corrosion',
            ],
            'Test Drive' => [
                'Engine - Performance', 'Gearbox operation', 'Clutch operation', 'Steering Operation',
                'Brake Operation', 'Hand brake/ Parking brake', 'DriveTrain(4WD,2WD,AWD)',
                'Instrument & cntrl functng', 'Suspension noise', 'Road holding stability', 'Noise',
                'Shock absorber',
            ],
        ],
    ];

    public function run(): void
    {
        $type = InspectionType::orderBy('id')->first();

        if (! $type) {
            $this->command?->error('No inspection_types row found — create the template first.');

            return;
        }

        // Start after any sections belonging to groups this seeder does not manage.
        $sequence = (int) InspectionSection::where('inspection_type_id', $type->id)
            ->whereNotIn('group_name', array_keys(self::GROUPS))
            ->max('sequence');

        foreach (self::GROUPS as $group => $sections) {
            $keptSectionIds = [];
            $questionCount = 0;

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

            // Sections removed from this group.
            $staleSections = InspectionSection::where('inspection_type_id', $type->id)
                ->where('group_name', $group)
                ->whereNotIn('id', $keptSectionIds)
                ->pluck('id')->all();

            if ($staleSections) {
                $this->pruneSteps(
                    InspectionStep::whereIn('inspection_section_id', $staleSections)->pluck('id')->all()
                );
                DB::table('inspection_section_summaries')->whereIn('inspection_section_id', $staleSections)->delete();
                InspectionSection::whereIn('id', $staleSections)->delete();
                $this->command?->warn("  {$group}: removed ".count($staleSections).' stale section(s).');
            }

            $this->command?->info("{$group}: ".count($sections)." sections, {$questionCount} questions.");
        }
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
