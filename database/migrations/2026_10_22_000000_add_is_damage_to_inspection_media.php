<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Flags a photo uploaded on the Additional Damage Photos step. Those photos
 * live in the inspection's step-less, section-less media bucket alongside the
 * Diagnostic Media; the flag keeps the two apart. Every existing row is false.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_media', function (Blueprint $table) {
            if (! Schema::hasColumn('inspection_media', 'is_damage')) {
                $table->boolean('is_damage')->default(false)->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspection_media', function (Blueprint $table) {
            if (Schema::hasColumn('inspection_media', 'is_damage')) {
                $table->dropColumn('is_damage');
            }
        });
    }
};
