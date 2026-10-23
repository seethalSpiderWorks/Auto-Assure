<?php

namespace Database\Seeders;

use App\Models\DamageColour;
use App\Models\DamageDiagram;
use Illuminate\Database\Seeder;

/**
 * Damage diagram option changes requested by the client:
 *
 *   Full Body  — add Rust
 *   Under Body — add Dent, remove Good
 *
 * Good is deleted outright, so it leaves the Damage Setup list too. Dots already
 * drawn in it keep their colour and still re-save (DamageDiagramWriter keeps a
 * view's existing colours), but reports no longer list it in the key. Options
 * are matched on label, so re-running changes nothing. Safe to re-run.
 *
 *   php artisan db:seed --class=DamageOptionsSeeder --force
 */
class DamageOptionsSeeder extends Seeder
{
    /** diagram key => [label, colour, description] to add */
    private const ADD = [
        'full_body' => ['Rust', '#8b4513', null],
        'under_body' => ['Dent', '#2196f3', null],
    ];

    /** diagram key => label to remove */
    private const REMOVE = [
        'under_body' => 'Good',
    ];

    public function run(): void
    {
        foreach (self::ADD as $key => [$label, $colour, $description]) {
            $diagram = DamageDiagram::where('key', $key)->first();
            if (! $diagram) {
                $this->command?->warn("  No “{$key}” diagram — skipped {$label}.");

                continue;
            }

            $option = DamageColour::firstOrCreate(
                ['damage_diagram_id' => $diagram->id, 'label' => $label],
                [
                    'colour' => $colour,
                    'description' => $description,
                    'sequence' => (int) DamageColour::where('damage_diagram_id', $diagram->id)->max('sequence') + 1,
                    'is_active' => true,
                ]
            );

            $this->command?->info("  {$diagram->name}: ".($option->wasRecentlyCreated ? 'added' : 'already has')." {$label}.");
        }

        foreach (self::REMOVE as $key => $label) {
            $diagram = DamageDiagram::where('key', $key)->first();
            $count = $diagram
                ? DamageColour::where('damage_diagram_id', $diagram->id)->where('label', $label)->delete()
                : 0;

            $this->command?->info('  '.($diagram->name ?? $key).": {$label} ".($count ? 'removed' : 'not found').'.');
        }
    }
}
