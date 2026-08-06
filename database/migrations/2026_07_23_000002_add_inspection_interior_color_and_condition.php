<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Two more fields the legacy /inspectionreport form captures via dropdowns and
 * `inspections` had nowhere to store:
 *
 *   interior_color    <- tbl_interior_color  (alongside the existing exterior_color)
 *   vehicle_condition <- Used / New
 *
 * vehicle_condition is deliberately separate from the existing `overall_condition`:
 * that one is the inspector's verdict (excellent/good/fair/poor), this one is
 * whether the vehicle is new or used.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'interior_color')) {
                $table->string('interior_color', 50)->nullable()->after('exterior_color');
            }
            if (! Schema::hasColumn('inspections', 'vehicle_condition')) {
                $table->string('vehicle_condition', 20)->nullable()->after('manufacturing_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropColumn(['interior_color', 'vehicle_condition']);
        });
    }
};
