<?php

namespace Database\Seeders;

use App\Models\InspectionSummaryOption;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;

/**
 * Gives every template the same Summary options as Comprehensive Vehicle
 * Inspection — same titles, same Arabic, same order. Comprehensive itself is
 * read, never written, so whatever it holds on the server is what the others
 * get.
 *
 * Options are matched on title: one a template already has is updated in place
 * (its notes stay attached), a missing one is added, and one Comprehensive does
 * not have is removed. Notes written against a removed option stay in
 * inspection_summaries — they simply stop being shown, as when an option is
 * removed on the template screen. Safe to re-run.
 *
 *   php artisan db:seed --class=SummaryOptionsSeeder --force
 */
class SummaryOptionsSeeder extends Seeder
{
    private const SOURCE = 'Comprehensive Vehicle Inspection';

    public function run(): void
    {
        $source = InspectionType::where('name', self::SOURCE)->with('summaryOptions')->first();

        if (! $source || $source->summaryOptions->isEmpty()) {
            $this->command?->error('“'.self::SOURCE.'” has no Summary options to copy — nothing changed.');

            return;
        }

        $wanted = $source->summaryOptions->values();
        $key = fn (string $name) => mb_strtolower(trim($name));

        foreach (InspectionType::where('id', '!=', $source->id)->with('summaryOptions')->get() as $type) {
            $existing = $type->summaryOptions->keyBy(fn ($o) => $key($o->name));
            $kept = [];
            $added = [];

            foreach ($wanted as $i => $option) {
                $values = [
                    'name' => $option->name,
                    'name_ar' => $option->name_ar,
                    'summary_type_id' => $option->summary_type_id,
                    'sequence' => $i + 1,
                ];

                $mine = $existing->get($key($option->name));
                if ($mine) {
                    $mine->update($values);
                } else {
                    $mine = $type->summaryOptions()->create($values);
                    $added[] = $option->name;
                }
                $kept[] = $mine->id;
            }

            $removed = InspectionSummaryOption::where('inspection_type_id', $type->id)->whereNotIn('id', $kept)->pluck('name')->all();
            InspectionSummaryOption::where('inspection_type_id', $type->id)->whereNotIn('id', $kept)->delete();

            $this->command?->info("  {$type->name}: ".count($kept).' options'
                .($added ? '; added '.implode(', ', $added) : '')
                .($removed ? '; removed '.implode(', ', $removed) : '')
                .(! $added && ! $removed ? ' (already the same)' : '').'.');
        }
    }
}
