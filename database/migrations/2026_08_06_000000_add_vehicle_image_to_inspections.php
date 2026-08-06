<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Primary vehicle photo captured on the "Customer & Vehicle" step — the single
 * shot of the car itself, distinct from the checklist/section/additional media.
 * Stores the path on the "public" disk.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'vehicle_image')) {
                $table->string('vehicle_image')->nullable()->after('exterior_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (Schema::hasColumn('inspections', 'vehicle_image')) {
                $table->dropColumn('vehicle_image');
            }
        });
    }
};
