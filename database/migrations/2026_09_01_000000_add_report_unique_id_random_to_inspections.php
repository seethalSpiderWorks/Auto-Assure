<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Random public id for the customer's report link, mirroring the legacy
 * tbl_report.report_unique_id_random convention (Str::random(10)) so both report
 * systems share one link format. Minted on first use — see
 * Inspection::reportToken().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'report_unique_id_random')) {
                $table->string('report_unique_id_random', 10)->nullable()->unique()->after('summary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (Schema::hasColumn('inspections', 'report_unique_id_random')) {
                $table->dropUnique(['report_unique_id_random']);
                $table->dropColumn('report_unique_id_random');
            }
        });
    }
};
