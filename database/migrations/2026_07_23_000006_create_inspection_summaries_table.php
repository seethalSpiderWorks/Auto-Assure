<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-summary-type notes shown under the inspection's Overall Verdict
 * (Exterior, Interior, Engine, Brakes, …). The types themselves come from the
 * legacy tbl_summary_type lookup, so this stores one free-text note per
 * (inspection, summary type).
 *
 * Distinct from inspection_section_summaries, which is the note attached to a
 * checklist section on the wizard.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inspection_summaries')) {
            return;
        }

        Schema::create('inspection_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_id');
            $table->unsignedBigInteger('summary_type_id');
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->unique(['inspection_id', 'summary_type_id'], 'insp_summary_unique');
            $table->index('inspection_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_summaries');
    }
};
