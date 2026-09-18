<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-template switch for the Calculated Overall Verdict.
 *
 * On: the verdict (score, rating, condition and Recommendations) is worked out
 * from the section weights, and the technician no longer picks a
 * Recommendation. Off: the manual Overall Rating and Recommendation, as before.
 *
 * Defaults to off so every existing template keeps its current behaviour;
 * SectionWeightSeeder turns it on for Comprehensive and Premium.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            if (! Schema::hasColumn('inspection_types', 'has_calculated_verdict')) {
                $table->boolean('has_calculated_verdict')->default(false)->after('has_diagnostic_media');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            if (Schema::hasColumn('inspection_types', 'has_calculated_verdict')) {
                $table->dropColumn('has_calculated_verdict');
            }
        });
    }
};
