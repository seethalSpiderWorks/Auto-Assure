<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The two marked-up damage diagrams (full body / under body) drawn on the
 * inspection screen, mirroring the legacy /inspectionreport DAMAGES canvas.
 *
 * Stored as paths on the "public" disk, the same shape as vehicle_image. Kept
 * off inspection_media on purpose: these are generated diagrams, not uploads,
 * and must not leak into the media galleries or the report's General Photos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'damage_full_body')) {
                $table->string('damage_full_body')->nullable()->after('vehicle_image');
            }
            if (! Schema::hasColumn('inspections', 'damage_under_body')) {
                $table->string('damage_under_body')->nullable()->after('damage_full_body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            foreach (['damage_full_body', 'damage_under_body'] as $col) {
                if (Schema::hasColumn('inspections', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
