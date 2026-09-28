<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Per-template switch for the report title.
 *
 * On: the report is titled "<Kind> Inspection Report" (e.g. "Premium
 * Inspection Report"). Off: just "Inspection Report".
 *
 * Defaults to on so every existing template keeps its current title; the Fleet
 * templates are switched off here, as the client asked.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            if (! Schema::hasColumn('inspection_types', 'show_name_in_report')) {
                $table->boolean('show_name_in_report')->default(true)->after('has_calculated_verdict');
            }
        });

        DB::table('inspection_types')->where('name', 'like', '%fleet%')->update(['show_name_in_report' => false]);
    }

    public function down(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            if (Schema::hasColumn('inspection_types', 'show_name_in_report')) {
                $table->dropColumn('show_name_in_report');
            }
        });
    }
};
