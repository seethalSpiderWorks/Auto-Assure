<?php

namespace Database\Seeders;

use App\Models\DamageColour;
use App\Models\DamageDiagram;
use App\Models\Inspection;
use Illuminate\Database\Seeder;

/**
 * Adds the Full Body damage options (Dent, Re-Painted, Faded, Broken, Scratch,
 * Sticker Work, Rust) to both Under Body diagrams — Unibody and Body-on-Frame —
 * after the options they already have.
 *
 * Read from Full Body's own rows, so it copies whatever that palette holds on
 * the server it runs on. An option Under Body already has (matched on label,
 * ignoring case) is left alone. A colour Under Body already uses for another
 * option gets a stand-in, since a dot is identified by its colour. Safe to
 * re-run.
 *
 *   php artisan db:seed --class=UnderBodyFullBodyOptionsSeeder --force
 */
class UnderBodyFullBodyOptionsSeeder extends Seeder
{
    private const SOURCE = 'full_body';

    /** Used in place of a Full Body colour Under Body already has. */
    private const STAND_INS = ['#00bcd4', '#e91e63', '#607d8b', '#795548', '#4caf50', '#3f51b5'];

    public function run(): void
    {
        $source = DamageDiagram::where('key', self::SOURCE)->first();

        if (! $source) {
            $this->command?->error('No Full Body diagram found.');

            return;
        }

        $options = DamageColour::where('damage_diagram_id', $source->id)->orderBy('sequence')->get();

        foreach (DamageDiagram::whereIn('key', Inspection::CHASSIS_DIAGRAMS)->get() as $diagram) {
            $existing = DamageColour::where('damage_diagram_id', $diagram->id)->get();
            $labels = $existing->map(fn ($c) => mb_strtolower(trim($c->label)))->all();
            $colours = $existing->map(fn ($c) => strtolower($c->colour))->all();
            $sequence = (int) $existing->max('sequence');
            $standIns = self::STAND_INS;

            foreach ($options as $option) {
                if (in_array(mb_strtolower(trim($option->label)), $labels, true)) {
                    $this->command?->line("  {$diagram->name}: already has {$option->label}");

                    continue;
                }

                $colour = strtolower($option->colour);
                while (in_array($colour, $colours, true) && $standIns) {
                    $colour = array_shift($standIns);
                }

                DamageColour::create([
                    'damage_diagram_id' => $diagram->id,
                    'label' => $option->label,
                    'colour' => $colour,
                    'description' => $option->description,
                    'sequence' => ++$sequence,
                    'is_active' => $option->is_active,
                ]);
                $colours[] = $colour;

                $this->command?->info("  {$diagram->name}: added {$option->label} ({$colour})");
            }
        }
    }
}
