<?php

namespace Database\Seeders;

use App\Models\InspectionSummaryOption;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;

/**
 * Adds an "Electrical" Summary option to every template that has Summary
 * options, placed right after Transmission (or last when a template has no
 * Transmission option). The options after it move down one place.
 *
 * There is no Electrical area in tbl_summary_type, so the option has no legacy
 * summary_type_id. Templates that already have an Electrical option are left
 * alone. Safe to re-run.
 *
 *   php artisan db:seed --class=ElectricalSummaryOptionSeeder --force
 */
class ElectricalSummaryOptionSeeder extends Seeder
{
    private const NAME = 'Electrical';

    private const NAME_AR = 'الكهرباء والإلكترونيات';

    private const AFTER = 'Transmission';

    public function run(): void
    {
        $types = InspectionType::whereHas('summaryOptions')->with('summaryOptions')->get();

        foreach ($types as $type) {
            $options = $type->summaryOptions->values();

            if ($options->contains(fn ($o) => strcasecmp(trim($o->name), self::NAME) === 0)) {
                $this->command?->line("  {$type->name}: already has ".self::NAME.'.');

                continue;
            }

            $after = $options->search(fn ($o) => strcasecmp(trim($o->name), self::AFTER) === 0);
            $position = $after === false ? $options->count() : $after + 1;

            $electrical = new InspectionSummaryOption([
                'name' => self::NAME,
                'name_ar' => self::NAME_AR,
            ]);
            $options->splice($position, 0, [$electrical]);

            foreach ($options as $i => $option) {
                $option->inspection_type_id = $type->id;
                $option->sequence = $i + 1;
                $option->save();
            }

            $this->command?->info("  {$type->name}: added ".self::NAME.' at position '.($position + 1).'.');
        }
    }
}
