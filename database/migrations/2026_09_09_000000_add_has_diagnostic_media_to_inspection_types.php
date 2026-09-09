<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-template switch for the Diagnostic Media bucket.
 *
 * The bucket used to be unconditional on the inspection screen, so existing
 * templates default to true — turning this on for everyone keeps the current
 * behaviour and lets an admin opt a template out instead of opting every one in.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            $table->boolean('has_diagnostic_media')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            $table->dropColumn('has_diagnostic_media');
        });
    }
};
