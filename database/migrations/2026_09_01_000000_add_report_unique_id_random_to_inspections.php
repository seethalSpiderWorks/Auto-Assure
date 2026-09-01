<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Random public id for the customer's report link, mirroring the legacy
 * tbl_report.report_unique_id_random convention (Str::random(10)) so both report
 * systems share one link format. The two systems cover different leads, so the
 * new inspections need their own column rather than reusing tbl_report's.
 * Minted on first use — see Inspection::reportToken().
 *
 * NOTE: this must be applied on every environment, or building a report link
 * fails with "Unknown column 'report_unique_id_random'". This project's
 * `migrations` table does not hold the full history, so run it explicitly:
 *   php artisan migrate --force --path=database/migrations/2026_09_01_000000_add_report_unique_id_random_to_inspections.php
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
