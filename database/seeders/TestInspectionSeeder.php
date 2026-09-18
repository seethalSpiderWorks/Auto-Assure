<?php

namespace Database\Seeders;

use App\Models\DamageDiagram;
use App\Models\Inspection;
use App\Models\InspectionDetail;
use App\Models\InspectionMedia;
use App\Models\InspectionType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * One finished inspection per newly added template — Premium, Quick and Fleet —
 * so each can be opened, printed and shared without waiting for a real job.
 *
 * Each is filled the way a technician would leave it: every question answered
 * (with an observation wherever the answer is not Good), a note and rating per
 * section, a note for all ten summary areas, the overall verdict, marked-up
 * damage diagrams, a few photos and a report link.
 *
 * Test rows are recognised by their plate (TEST-<id>), so re-running refreshes
 * the same three inspections instead of piling up new ones. Nothing else in the
 * database is touched.
 */
class TestInspectionSeeder extends Seeder
{
    /** template name => [plate, vehicle, customer] */
    private const CASES = [
        'Premium Inspection' => [
            'plate' => 'TEST-PRM',
            'car' => ['Lexus', 'RX 350', 2021, 2021, 'JTJBZMCA2N2041881', 'Pearl White', 68420],
            'customer' => ['Omar Al Farsi', 'عمر الفارسي', 'omar.alfarsi@example.test', '+974 33 111 222'],
        ],
        'Quick Inspection' => [
            'plate' => 'TEST-QCK',
            'car' => ['Nissan', 'Patrol', 2019, 2018, 'JN8AY2NY9K9350112', 'Desert Silver', 132780],
            'customer' => ['Yousef Rahman', 'يوسف رحمن', 'yousef.rahman@example.test', '+974 33 333 444'],
        ],
        'Fleet Inspection – Corporate Customers' => [
            'plate' => 'TEST-FLT',
            'car' => ['Toyota', 'Hilux', 2022, 2022, 'MR0FZ29G1N1234567', 'Fleet White', 84115],
            'customer' => ['Gulf Logistics W.L.L.', 'الخليج للخدمات اللوجستية', 'fleet@gulflogistics.test', '+974 44 555 666'],
        ],
    ];

    /**
     * Answers that are not "Good", keyed by a fragment of the question, so the
     * test data reads like a real inspection instead of a wall of Good.
     */
    private const FINDINGS = [
        'general body condition' => ['Average', 'Front bumper and right fender repainted; minor scratches on both doors.'],
        'front bumper' => ['Bad', 'Repainted and misaligned after a previous repair.'],
        'engine oil level' => ['Average', 'Oil level low and due for a change.'],
        'engine condition' => ['Average', 'Oil level low, minor seepage at the valve cover.'],
        'external engine leaks' => ['Bad', 'Seepage at the valve cover gasket.'],
        'gear shifting' => ['Average', 'Slight delay shifting into third gear when cold.'],
        'transmission' => ['Average', 'Slight delay shifting into third gear when cold.'],
        'front left tyre' => ['Bad', 'Tread below the legal limit — replace.'],
        'back left tyre' => ['Average', 'Worn on the inner edge; alignment check advised.'],
        'evidence of floor/chassis corrosion' => ['Average', 'Surface rust underneath; undercoating recommended.'],
        'undercarriage / chassis condition' => ['Average', 'Surface rust underneath; undercoating recommended.'],
        'suspension noise' => ['Bad', 'Knocking from the front left over speed bumps.'],
        'dampers and bushes' => ['Bad', 'Front bushes worn.'],
        'warning lights' => ['Average', 'TPMS warning light on.'],
        'road test' => ['Average', 'Suspension noise over bumps and a slight delay into third gear.'],
        'brakes' => ['Good', ''],
    ];

    /** The ten summary areas, so every one carries a note. */
    private const AREA_NOTES = [
        'Exterior' => 'Front bumper and right front fender repainted. Minor scratches and dents on the body panels.',
        'Interior' => 'Cabin clean and in good order; no damage to trim or seats.',
        'Engine' => 'Oil level low with minor seepage at the valve cover. No overheating signs.',
        'Brakes' => 'Brakes effective, pads and discs within limits.',
        'Transmission' => 'No leakage. Slight delay shifting into third gear when cold.',
        'Suspension & Steering' => 'Front bushes worn; knocking noise over bumps. Steering rack dry.',
        'Tires & Wheels' => 'Front left tyre below the legal tread limit. Rims free of damage.',
        'Undercarriage' => 'Surface rust underneath; undercoating recommended. No accident damage found.',
        'Safety Systems' => 'Airbags, ABS and seat belts all functional. TPMS warning light on.',
        'Road Test' => 'Suspension noise over bumps and a slight delay into third gear; otherwise stable.',
    ];

    public function run(): void
    {
        $technician = User::whereIn('id', DB::table('inspections')->distinct()->pluck('technician_id'))->first()
            ?? User::first();
        $branchId = DB::table('inspections')->value('branch_id');

        if (! $technician) {
            $this->command?->error('No user to assign as technician — skipped.');

            return;
        }

        foreach (self::CASES as $typeName => $case) {
            $type = InspectionType::with('sections.steps')->where('name', $typeName)->first();

            if (! $type) {
                $this->command?->warn("Template “{$typeName}” not found — skipped.");

                continue;
            }

            $this->seedOne($type, $case, $technician->id, $branchId);
        }
    }

    private function seedOne(InspectionType $type, array $case, int $technicianId, ?int $branchId): void
    {
        [$make, $model, $year, $mfgYear, $vin, $colour, $odometer] = $case['car'];
        [$name, $nameAr, $email, $phone] = $case['customer'];

        $inspection = Inspection::firstOrNew(['plate_no' => $case['plate']]);

        $inspection->fill([
            'branch_id' => $branchId,
            'technician_id' => $technicianId,
            'inspection_type_id' => $type->id,
            'customer_name' => $name,
            'customer_name_ar' => $nameAr,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'whatsapp_number' => $phone,
            'date_of_inspection' => now()->toDateString(),
            'car_make' => $make,
            'car_model' => $model,
            'car_year' => $year,
            'manufacturing_year' => $mfgYear,
            'vehicle_condition' => 'Used',
            'vin' => $vin,
            'exterior_color' => $colour,
            'odometer' => $odometer,
            'region' => 'GCC',
            'fuel_type' => 'Petrol',
            'gearbox' => 'Automatic',
            'body_type' => 'SUV',
            'number_of_keys' => 2,
            'with_service_history' => true,
            'last_service_date' => now()->subMonths(4)->toDateString(),
            'status' => Inspection::STATUS_COMPLETED,
            'scheduled_at' => now()->subDay()->setTime(9, 30),
            'started_at' => now()->subDay()->setTime(9, 45),
            'completed_at' => now()->subDay()->setTime(11, 20),
            'overall_condition' => 'good',
            'overall_rating' => 3.8,
            'recommendation' => 'buy_with_repairs',
            'summary' => 'Mechanically sound overall. Attend to the worn front suspension bushes and the front left tyre, '
                .'top up and change the engine oil, and treat the surface rust underneath. The delayed third-gear shift '
                .'should be re-checked after a transmission service.',
            'currency' => 'QAR',
        ])->save();

        $this->answerEveryQuestion($inspection, $type);
        $this->writeSectionSummaries($inspection, $type);
        $this->writeAreaNotes($inspection);
        $this->attachDamageDiagrams($inspection);
        $this->attachPhotos($inspection, $type);

        $token = $inspection->reportToken();

        $this->command?->info(sprintf(
            '  %-40s inspection #%-4d %s  →  %s',
            $type->name,
            $inspection->id,
            $inspection->progress()['answered'].'/'.$inspection->progress()['total'].' answered',
            route('inspections.report', ['token' => $token])
        ));
    }

    /**
     * Every templated question gets an answer, so the inspection counts as fully
     * answered and can legitimately sit at "completed".
     */
    private function answerEveryQuestion(Inspection $inspection, InspectionType $type): void
    {
        foreach ($type->sections as $section) {
            foreach ($section->steps as $step) {
                [$choice, $note] = $this->answerFor($step->question);

                InspectionDetail::updateOrCreate(
                    ['inspection_id' => $inspection->id, 'inspection_step_id' => $step->id],
                    [
                        'inspection_section_id' => $section->id,
                        'choice' => $choice,
                        // The observation box only carries text when something was found.
                        'descriptive_answer' => $note ?: null,
                    ]
                );
            }
        }
    }

    /** @return array{0: string, 1: string} */
    private function answerFor(string $question): array
    {
        $q = mb_strtolower($question);

        foreach (self::FINDINGS as $needle => $finding) {
            if (str_contains($q, $needle)) {
                return $finding;
            }
        }

        return ['Good', ''];
    }

    private function writeSectionSummaries(Inspection $inspection, InspectionType $type): void
    {
        foreach ($type->sections as $section) {
            DB::table('inspection_section_summaries')->updateOrInsert(
                ['inspection_id' => $inspection->id, 'inspection_section_id' => $section->id],
                [
                    'summary' => self::AREA_NOTES[$section->section_name]
                        ?? "{$section->section_name} checked — no significant faults recorded.",
                    'rating' => 4,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function writeAreaNotes(Inspection $inspection): void
    {
        $inspection->load('type.summaryOptions');

        foreach ($inspection->summaryAreas() as $areaId => $areaName) {
            $inspection->saveSummaryNote($areaId, self::AREA_NOTES[$areaName] ?? "{$areaName} inspected — nothing to report.");
        }
    }

    /**
     * Stand-in mark-ups for the Damage Points / Paint Inspection Images block:
     * the diagram's own base image saved under the inspection, with a handful of
     * dots so the marks travel with it.
     */
    private function attachDamageDiagrams(Inspection $inspection): void
    {
        $images = [];
        $marks = [];

        foreach (DamageDiagram::active()->ordered()->get() as $diagram) {
            if (! $diagram->image || ! Storage::disk('public')->exists($diagram->image)) {
                continue;
            }

            $path = "inspections/{$inspection->id}/damage/{$diagram->key}-test.png";

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, Storage::disk('public')->get($diagram->image));
            }

            $palette = $diagram->palette();
            $images[$diagram->key] = $path;
            $marks[$diagram->key] = collect([[320, 280], [520, 350], [740, 470], [940, 300]])
                ->map(fn ($xy, $i) => [
                    'x' => $xy[0],
                    'y' => $xy[1],
                    'c' => optional($palette->get($i % max($palette->count(), 1)))->colour ?? '#f44336',
                ])->all();
        }

        if ($images) {
            $inspection->forceFill(['damage_images' => $images, 'damage_marks' => $marks])->save();
        }
    }

    /**
     * A few section photos so the Vehicle Photos block has something to show.
     * Real image files are copied from an existing inspection — the report skips
     * any media whose file is missing from the disk.
     */
    private function attachPhotos(Inspection $inspection, InspectionType $type): void
    {
        $sources = collect(Storage::disk('public')->files('inspections/25/photos'))
            ->filter(fn ($f) => Str::endsWith(mb_strtolower($f), ['.jpg', '.jpeg', '.png']))
            ->take(3)
            ->values();

        if ($sources->isEmpty()) {
            $this->command?->warn('    No source photos on disk — Vehicle Photos left empty.');

            return;
        }

        // One section bucket (step-less, section set) — the same shape the app's
        // section upload endpoint writes.
        $section = $type->sections->firstWhere('section_name', 'Exterior') ?? $type->sections->first();

        $detail = InspectionDetail::updateOrCreate(
            [
                'inspection_id' => $inspection->id,
                'inspection_step_id' => null,
                'inspection_section_id' => $section->id,
            ],
            []
        );

        foreach ($sources as $i => $source) {
            $path = "inspections/{$inspection->id}/photos/test-".($i + 1).'.'.pathinfo($source, PATHINFO_EXTENSION);

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, Storage::disk('public')->get($source));
            }

            InspectionMedia::updateOrCreate(
                ['inspection_detail_id' => $detail->id, 'path' => $path],
                [
                    'type' => 'photo',
                    'disk' => 'public',
                    'original_name' => 'test-photo-'.($i + 1).'.'.pathinfo($source, PATHINFO_EXTENSION),
                    'label' => $section->section_name,
                    'mime_type' => 'image/'.(Str::endsWith($path, '.png') ? 'png' : 'jpeg'),
                    'size' => Storage::disk('public')->size($path),
                ]
            );
        }
    }
}
