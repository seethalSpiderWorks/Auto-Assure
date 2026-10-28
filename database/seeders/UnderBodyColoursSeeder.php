<?php

namespace Database\Seeders;

use App\Models\DamageColour;
use App\Models\Inspection;
use Illuminate\Database\Seeder;

/**
 * Recolours Under Body options that looked alike: Repaired (yellow, like
 * Scratch) becomes pink and Damaged (red, like Re-Painted) becomes grey.
 *
 * Dots are stored by colour, so the stored dots on under-body diagrams are
 * moved to the new colour too: the edit page redraws them and the next save
 * updates the picture. Pictures already saved keep their old dots until
 * then. Safe to re-run.
 *
 *   php artisan db:seed --class=UnderBodyColoursSeeder --force
 */
class UnderBodyColoursSeeder extends Seeder
{
    /** label => [old colour, new colour] */
    private const CHANGES = [
        'Repaired' => ['#f5d915', '#e91e63'],
        'Damaged' => ['#e02b28', '#607d8b'],
    ];

    public function run(): void
    {
        $map = [];
        foreach (self::CHANGES as $label => [$from, $to]) {
            $options = DamageColour::where('label', $label)->whereRaw('LOWER(colour) = ?', [$from])->update(['colour' => $to]);
            $this->command?->info("  {$label}: {$options} option(s) changed to {$to}.");
            $map[$from] = $to;
        }

        $views = array_merge(array_values(Inspection::CHASSIS_DIAGRAMS), ['under_body']);
        $changed = 0;

        Inspection::whereNotNull('damage_marks')->chunkById(200, function ($inspections) use ($views, $map, &$changed) {
            foreach ($inspections as $inspection) {
                $marks = $inspection->damage_marks;
                if (! is_array($marks)) {
                    continue;
                }

                $touched = false;
                foreach ($views as $view) {
                    foreach ((array) ($marks[$view] ?? []) as $i => $m) {
                        $to = $map[strtolower((string) ($m['c'] ?? ''))] ?? null;
                        if ($to) {
                            $marks[$view][$i]['c'] = $to;
                            $touched = true;
                        }
                    }
                }

                if ($touched) {
                    $inspection->damage_marks = $marks;
                    $inspection->timestamps = false;   // a data fix, not an edit
                    $inspection->saveQuietly();
                    $changed++;
                }
            }
        });

        $this->command?->info("  Dots moved to the new colour on {$changed} inspection(s).");
    }
}
