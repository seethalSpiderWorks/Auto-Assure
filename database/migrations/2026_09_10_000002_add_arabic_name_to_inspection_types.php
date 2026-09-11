<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Arabic name and description for an inspection template.
 *
 * The rest of the module already follows the pattern the legacy report screen
 * uses — every text field paired with an "_ar" twin filled on the same form
 * (tbl_report.report_client_name_ar, tbl_report_overview.overview_arabic,
 * tbl_report_insp_summary.insp_summary_desc_ar, and here
 * inspection_sections.section_name_ar / inspection_steps.question_ar). The
 * template itself was the one text the screen could not capture in Arabic.
 *
 * Both are nullable: an English-only template stays exactly as it is.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            if (! Schema::hasColumn('inspection_types', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (! Schema::hasColumn('inspection_types', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            foreach (['name_ar', 'description_ar'] as $column) {
                if (Schema::hasColumn('inspection_types', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
