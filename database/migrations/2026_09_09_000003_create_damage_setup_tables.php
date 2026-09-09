<?php

use App\Models\Inspection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Makes the damage screen configurable instead of hard-coded.
 *
 * - damage_diagrams : the body views (Full Body, Under Body, …) — name, key, image
 * - damage_colours  : the palette (Dent, Re-Painted, …) — label + hex
 * - inspections.damage_images : saved mark-ups keyed by diagram key, replacing the
 *   two fixed damage_full_body / damage_under_body columns.
 *
 * The two diagrams and six colours that were hard-coded are seeded here, so the
 * screen looks and behaves exactly as before until an admin edits them.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_diagrams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Stable identifier used in damage_images / damage_marks JSON keys.
            $table->string('key')->unique();
            $table->string('image');
            $table->unsignedInteger('sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('damage_colours', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('colour', 7);
            $table->unsignedInteger('sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'damage_images')) {
                $table->json('damage_images')->nullable()->after('damage_under_body');
            }
        });

        // Seed the palette exactly as it was in the Blade view.
        $now = now();
        DB::table('damage_colours')->insert(collect([
            ['Dent', '#ff9800'], ['Re-Painted', '#f44336'], ['Faded', '#2196f3'],
            ['Broken', '#000000'], ['Scratch', '#dfcd2b'], ['Sticker Work', '#008000'],
        ])->map(fn ($c, $i) => [
            'label' => $c[0], 'colour' => $c[1], 'sequence' => $i + 1,
            'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ])->all());

        // Seed the two diagrams. Their PNGs are copied onto the storage disk so
        // every row — seeded or uploaded — lives in one place and deleting a row
        // never touches the originals the team put in public/assets.
        foreach ([['Full Body', 'full_body'], ['Under Body', 'under_body']] as $i => [$name, $key]) {
            $source = public_path("assets/images/damage/{$key}.png");
            $stored = "damage-diagrams/{$key}.png";

            if (is_file($source) && ! Storage::disk('public')->exists($stored)) {
                Storage::disk('public')->put($stored, file_get_contents($source));
            }

            DB::table('damage_diagrams')->insert([
                'name' => $name, 'key' => $key, 'image' => $stored,
                'sequence' => $i + 1, 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Carry any already-saved mark-ups into the keyed column.
        foreach (DB::table('inspections')
            ->where(fn ($q) => $q->whereNotNull('damage_full_body')->orWhereNotNull('damage_under_body'))
            ->get(['id', 'damage_full_body', 'damage_under_body']) as $row) {
            $images = array_filter([
                'full_body' => $row->damage_full_body,
                'under_body' => $row->damage_under_body,
            ]);

            DB::table('inspections')->where('id', $row->id)->update(['damage_images' => json_encode($images)]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_diagrams');
        Schema::dropIfExists('damage_colours');

        Schema::table('inspections', function (Blueprint $table) {
            if (Schema::hasColumn('inspections', 'damage_images')) {
                $table->dropColumn('damage_images');
            }
        });
    }
};
