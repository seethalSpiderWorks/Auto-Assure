<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Arabic twins for the two notes the report prints.
 *
 * inspections.summary_ar        — the Inspector Comment on the cover
 * inspection_summaries.summary_ar — the note per area (Exterior, Engine, …)
 *
 * The legacy report screen stores exactly this pair: tbl_report_overview
 * .overview_arabic beside overview_english, and tbl_report_insp_summary
 * .insp_summary_desc_ar beside insp_summary_desc. Both nullable — an
 * English-only inspection is unaffected, and the Arabic report falls back to
 * the English note wherever the Arabic one is empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'summary_ar')) {
                $table->text('summary_ar')->nullable()->after('summary');
            }
        });

        Schema::table('inspection_summaries', function (Blueprint $table) {
            if (! Schema::hasColumn('inspection_summaries', 'summary_ar')) {
                $table->text('summary_ar')->nullable()->after('summary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (Schema::hasColumn('inspections', 'summary_ar')) {
                $table->dropColumn('summary_ar');
            }
        });

        Schema::table('inspection_summaries', function (Blueprint $table) {
            if (Schema::hasColumn('inspection_summaries', 'summary_ar')) {
                $table->dropColumn('summary_ar');
            }
        });
    }
};
