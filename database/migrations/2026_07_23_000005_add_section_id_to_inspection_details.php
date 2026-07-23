<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-category (section) media.
 *
 * Media hangs off inspection_details, which until now was either a step answer
 * (inspection_step_id set) or the single step-less "additional media" bucket
 * (both ids null). This adds a third shape: a per-section bucket with
 * inspection_section_id set and inspection_step_id null, so photos can be
 * attached to a category rather than to one question.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_details', function (Blueprint $table) {
            if (! Schema::hasColumn('inspection_details', 'inspection_section_id')) {
                $table->unsignedBigInteger('inspection_section_id')->nullable()->after('inspection_step_id');
                $table->index(['inspection_id', 'inspection_section_id'], 'insp_details_section_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspection_details', function (Blueprint $table) {
            $table->dropIndex('insp_details_section_idx');
            $table->dropColumn('inspection_section_id');
        });
    }
};
