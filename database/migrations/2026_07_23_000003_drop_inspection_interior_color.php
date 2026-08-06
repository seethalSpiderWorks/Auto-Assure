<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Interior Color is not wanted on the inspection — drop the column added by
 * 2026_07_23_000002. Exterior Color stays.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inspections', 'interior_color')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->dropColumn('interior_color');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('inspections', 'interior_color')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->string('interior_color', 50)->nullable()->after('exterior_color');
            });
        }
    }
};
