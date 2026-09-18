<?php

namespace Database\Seeders;

use App\Models\InspectionSection;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Weight (%) of each section in the Overall Verdict score, for the
 * Comprehensive and Premium templates — the client's category table, which
 * adds up to 100 — and switches their Calculated Overall Verdict on.
 *
 * Matched on section name; Premium calls After Market "Aftermarket Added
 * Accessories". Existing weights are never overwritten: set one by hand on
 * the template screen and this seeder leaves it alone. Safe to re-run.
 *
 * Needs both columns first:
 *   php artisan migrate --path=database/migrations/2026_09_16_000000_add_weight_to_inspection_sections.php
 *   php artisan migrate --path=database/migrations/2026_09_16_000001_add_has_calculated_verdict_to_inspection_types.php
 *   php artisan db:seed --class=SectionWeightSeeder
 */
class SectionWeightSeeder extends Seeder
{
    private const TEMPLATES = ['Comprehensive Vehicle Inspection', 'Premium Inspection'];

    /** Section name => weight (%). */
    private const WEIGHTS = [
        'Performance' => 5,
        'Safety' => 15,
        'Interior - Entertainment' => 4,
        'After Market' => 2,
        'Aftermarket Added Accessories' => 2,
        'Exterior' => 8,
        'Interior' => 5,
        'Tyre' => 8,
        'Engine' => 20,
        'Transmission' => 12,
        'Electrical' => 6,
        'Underbody' => 10,
        'Test Drive' => 5,
    ];

    public function run(): void
    {
        if (! Schema::hasColumn('inspection_sections', 'weight')) {
            $this->command?->error('inspection_sections.weight is missing — run the add_weight_to_inspection_sections migration first.');

            return;
        }

        if (! Schema::hasColumn('inspection_types', 'has_calculated_verdict')) {
            $this->command?->error('inspection_types.has_calculated_verdict is missing — run the add_has_calculated_verdict_to_inspection_types migration first.');

            return;
        }

        foreach (InspectionType::whereIn('name', self::TEMPLATES)->with('sections')->get() as $type) {
            // These two templates use the Calculated Overall Verdict.
            $type->forceFill(['has_calculated_verdict' => true])->save();

            $set = 0;

            foreach ($type->sections as $section) {
                $weight = self::WEIGHTS[$section->section_name] ?? null;

                if ($weight !== null && $section->weight === null) {
                    $section->forceFill(['weight' => $weight])->save();
                    $set++;
                }
            }

            $total = round((float) InspectionSection::where('inspection_type_id', $type->id)->sum('weight'), 2);
            $this->command?->info("{$type->name}: calculated verdict on, {$set} section weight(s) set, total {$total}%.");
        }
    }
}
