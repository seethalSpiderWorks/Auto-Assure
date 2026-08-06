<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Section ratings were whole 1–5 stars (tinyint), so a technician could not
 * record "4.6". decimal(2,1) holds 0.0–9.9, which covers the 0–5 scale with
 * one decimal place.
 *
 * Widening only — every existing whole rating survives as e.g. 4 -> 4.0.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_section_summaries', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Narrowing back to whole stars — round rather than truncate so 4.6
        // becomes 5, not 4.
        \Illuminate\Support\Facades\DB::statement(
            'UPDATE inspection_section_summaries SET rating = ROUND(rating) WHERE rating IS NOT NULL'
        );

        Schema::table('inspection_section_summaries', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->change();
        });
    }
};
