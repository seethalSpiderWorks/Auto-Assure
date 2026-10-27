<?php

namespace Database\Seeders;

use App\Models\DamageColour;
use App\Models\DamageDiagram;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Adds two Under Body diagrams to Damage Setup — Body-on-Frame and Unibody —
 * so the technician can mark the one that matches the vehicle.
 *
 * Each one gets its image (copied from database/seeders/images into the public
 * disk), the Under Body palette and the same template sections Under Body is
 * drawn in. The old Under Body diagram is then removed, options first: they
 * are nullOnDelete, and a null diagram means "every diagram". Inspections
 * already drawn on it keep their saved picture; reports just no longer list
 * its colour key. Diagrams are matched on key and colours on label, so
 * re-running changes nothing. Safe to re-run.
 *
 *   php artisan db:seed --class=UnderBodyDiagramsSeeder --force
 */
class UnderBodyDiagramsSeeder extends Seeder
{
    private const SOURCE = 'under_body';

    /** key => [name, image file in database/seeders/images, sequence] */
    private const DIAGRAMS = [
        'under_body_frame' => ['Under Body – Body-on-Frame', 'under-body-on-frame.jpeg', 3],
        'under_body_unibody' => ['Under Body – Unibody', 'under-body-unibody.jpeg', 4],
    ];

    public function run(): void
    {
        $source = DamageDiagram::where('key', self::SOURCE)->with(['colours', 'sections'])->first();

        foreach (self::DIAGRAMS as $key => [$name, $file, $sequence]) {
            $image = 'damage-diagrams/'.$file;
            if (! Storage::disk('public')->exists($image)) {
                Storage::disk('public')->put($image, file_get_contents(database_path('seeders/images/'.$file)));
            }

            $diagram = DamageDiagram::firstOrCreate(
                ['key' => $key],
                ['name' => $name, 'image' => $image, 'sequence' => $sequence, 'is_active' => true]
            );

            if (! $source) {
                $this->command?->info("  {$name}: ".($diagram->wasRecentlyCreated ? 'added' : 'already there').'.');

                continue;
            }

            $added = 0;
            foreach ($source->colours as $colour) {
                $option = DamageColour::firstOrCreate(
                    ['damage_diagram_id' => $diagram->id, 'label' => $colour->label],
                    $colour->only(['colour', 'description', 'sequence', 'is_active'])
                );
                $added += (int) $option->wasRecentlyCreated;
            }

            $attached = $diagram->sections()->syncWithoutDetaching($source->sections->pluck('id'))['attached'];

            $this->command?->info("  {$name}: ".($diagram->wasRecentlyCreated ? 'added' : 'already there')
                ."; {$added} option(s) and ".count($attached).' section(s) added.');
        }

        if ($source) {
            $source->colours()->delete();
            $source->delete();
            if ($source->image) {
                Storage::disk('public')->delete($source->image);
            }
            $this->command?->info('  '.$source->name.': removed.');
        }
    }
}
