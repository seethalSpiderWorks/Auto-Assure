<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Weight (%) of each section in the Overall Verdict score.
 *
 * A section's share of the /100 score — Engine counts for 20, After Market
 * for 2 — so a template's weights should add up to 100. Nullable: a template
 * nobody has weighted yet stays exactly as it is.
 *
 * The client's weights for the Comprehensive and Premium templates are filled
 * in by SectionWeightSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspection_sections', 'weight')) {
                $table->decimal('weight', 5, 2)->nullable()->after('sequence');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspection_sections', function (Blueprint $table) {
            if (Schema::hasColumn('inspection_sections', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};
