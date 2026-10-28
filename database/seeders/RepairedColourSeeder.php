<?php

namespace Database\Seeders;

use App\Models\DamageColour;
use App\Models\Inspection;
use Illuminate\Database\Seeder;

/**
 * Changes the Under Body "Repaired" option from yellow to pink, so it no
 * longer looks like Scratch.
 *
 * Dots are stored by colour, so the stored dots on under-body diagrams are
 * moved to the new colour too: the edit page redraws them pink and the next
 * save updates the picture. Pictures already saved keep their yellow dots
 * until then. Safe to re-run.
 *
 *   php artisan db:seed --class=RepairedColourSeeder --force
 */
class RepairedColourSeeder extends Seeder
{
    private const LABEL = 'Repaired';

    private const FROM = '#f5d915';

    private const TO = '#e91e63';

    public function run(): void
    {
        $options = DamageColour::where('label', self::LABEL)->whereRaw('LOWER(colour) = ?', [self::FROM])->update(['colour' => self::TO]);
        $this->command?->info("  {$options} Repaired option(s) changed to ".self::TO.'.');

        $views = array_merge(array_values(Inspection::CHASSIS_DIAGRAMS), ['under_body']);
        $changed = 0;

        Inspection::whereNotNull('damage_marks')->chunkById(200, function ($inspections) use ($views, &$changed) {
            foreach ($inspections as $inspection) {
                $marks = $inspection->damage_marks;
                if (! is_array($marks)) {
                    continue;
                }

                $touched = false;
                foreach ($views as $view) {
                    foreach ((array) ($marks[$view] ?? []) as $i => $m) {
                        if (strtolower((string) ($m['c'] ?? '')) === self::FROM) {
                            $marks[$view][$i]['c'] = self::TO;
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
