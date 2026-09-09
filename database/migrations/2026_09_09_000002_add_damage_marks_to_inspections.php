<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The damage dots as data: {view: [{x, y, c}, ...]} in the diagram's own pixel
 * space.
 *
 * The rendered PNG stays in damage_full_body / damage_under_body for anything
 * that just wants a picture, but a flattened bitmap cannot give back a single
 * dot — erasing one needs the marks themselves, so they are kept alongside and
 * the canvas is redrawn from base image + marks.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'damage_marks')) {
                $table->json('damage_marks')->nullable()->after('damage_under_body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (Schema::hasColumn('inspections', 'damage_marks')) {
                $table->dropColumn('damage_marks');
            }
        });
    }
};
