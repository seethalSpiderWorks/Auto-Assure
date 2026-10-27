<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Chassis Type picked in the Underbody section (unibody / body_on_frame).
 * It decides which under-body diagram the technician marks. Null until chosen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'chassis_type')) {
                $table->string('chassis_type', 20)->nullable()->after('damage_marks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (Schema::hasColumn('inspections', 'chassis_type')) {
                $table->dropColumn('chassis_type');
            }
        });
    }
};
