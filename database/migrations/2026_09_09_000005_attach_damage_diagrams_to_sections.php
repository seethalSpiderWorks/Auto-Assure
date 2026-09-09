<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A damage diagram belongs to one checklist section, so its canvas appears
 * inside that step (Performance, Safety, Underbody, …) rather than once at the
 * bottom of the inspection.
 *
 * Many diagrams per section, one section per diagram — deliberately not a pivot.
 * Keeping a diagram key unique across the inspection means damage_images and
 * damage_marks stay keyed by that key alone, so mark-ups saved before sections
 * existed keep resolving untouched.
 *
 * Null means the diagram is not attached to any section and is shown nowhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('damage_diagrams', function (Blueprint $table) {
            if (! Schema::hasColumn('damage_diagrams', 'inspection_section_id')) {
                $table->foreignId('inspection_section_id')->nullable()->after('key')
                    ->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('damage_diagrams', function (Blueprint $table) {
            if (Schema::hasColumn('damage_diagrams', 'inspection_section_id')) {
                $table->dropConstrainedForeignId('inspection_section_id');
            }
        });
    }
};
