<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a damage diagram sit in a section of every template, not just one.
 *
 * damage_diagrams.inspection_section_id allowed a single section across all
 * templates, so ticking "Full Body" on the Fleet template silently took it off
 * Comprehensive. The pivot allows one section per template instead — still one
 * per template, so damage_images / damage_marks stay keyed by the diagram key
 * alone within an inspection.
 *
 * Existing assignments are copied over; the old column is left in place (and
 * no longer read) so rolling back loses nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('damage_diagram_section')) {
            Schema::create('damage_diagram_section', function (Blueprint $table) {
                $table->id();
                $table->foreignId('damage_diagram_id')->constrained()->cascadeOnDelete();
                $table->foreignId('inspection_section_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['damage_diagram_id', 'inspection_section_id'], 'dds_diagram_section_unique');
            });
        }

        $now = now();
        DB::table('damage_diagrams')->whereNotNull('inspection_section_id')->get(['id', 'inspection_section_id'])
            ->each(fn ($d) => DB::table('damage_diagram_section')->insertOrIgnore([
                'damage_diagram_id' => $d->id,
                'inspection_section_id' => $d->inspection_section_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_diagram_section');
    }
};
