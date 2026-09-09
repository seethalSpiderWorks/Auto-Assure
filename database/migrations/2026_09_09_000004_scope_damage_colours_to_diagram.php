<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Each diagram gets its own palette.
 *
 * The body views and the underbody are assessed against different scales — the
 * body is marked Dent / Re-Painted / Scratch, while the chassis is graded
 * Good / Repaired / Corroded / Damaged / Modified / N-V — so a single shared
 * palette cannot serve both.
 *
 * A null damage_diagram_id means "applies to every diagram", which keeps a
 * colour usable everywhere if an admin wants that.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('damage_colours', function (Blueprint $table) {
            if (! Schema::hasColumn('damage_colours', 'damage_diagram_id')) {
                $table->foreignId('damage_diagram_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('damage_colours', 'description')) {
                $table->string('description')->nullable()->after('colour');
            }
        });

        $fullBody = DB::table('damage_diagrams')->where('key', 'full_body')->value('id');
        $underBody = DB::table('damage_diagrams')->where('key', 'under_body')->value('id');

        // Everything that existed was the body palette.
        if ($fullBody) {
            DB::table('damage_colours')->whereNull('damage_diagram_id')->update(['damage_diagram_id' => $fullBody]);
        }

        // The chassis / underbody scale.
        if ($underBody && ! DB::table('damage_colours')->where('damage_diagram_id', $underBody)->exists()) {
            $now = now();
            DB::table('damage_colours')->insert(collect([
                ['Good',     '#22a04a', 'No visible damage'],
                ['Repaired', '#f5d915', 'Evidence of repair / welding'],
                ['Corroded', '#f39019', 'Corrosion present'],
                ['Damaged',  '#e02b28', 'Deformation / cracks / bends'],
                ['Modified', '#8e44c9', 'Structural modification / cutting'],
                ['N/V',      '#ffffff', 'Not visible / Unable to inspect'],
            ])->map(fn ($c, $i) => [
                'damage_diagram_id' => $underBody,
                'label' => $c[0], 'colour' => $c[1], 'description' => $c[2],
                'sequence' => $i + 1, 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ])->all());
        }
    }

    public function down(): void
    {
        $underBody = DB::table('damage_diagrams')->where('key', 'under_body')->value('id');

        if ($underBody) {
            DB::table('damage_colours')->where('damage_diagram_id', $underBody)->delete();
        }

        Schema::table('damage_colours', function (Blueprint $table) {
            if (Schema::hasColumn('damage_colours', 'damage_diagram_id')) {
                $table->dropConstrainedForeignId('damage_diagram_id');
            }
            if (Schema::hasColumn('damage_colours', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
