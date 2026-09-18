<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Per-template Summary titles (the "A note per area" block on the inspection).
 *
 * A template with options shows exactly those titles; a template without any
 * keeps the legacy tbl_summary_type areas. Notes written against an option are
 * stored in inspection_summaries.summary_option_id; legacy notes keep using
 * summary_type_id, which therefore becomes nullable.
 *
 * Comprehensive and Premium are given the ten legacy areas as their starting
 * options, and their existing notes are moved onto those options so nothing
 * already written disappears from the edit screen or the report.
 */
return new class extends Migration
{
    private const TEMPLATES = ['Comprehensive Vehicle Inspection', 'Premium Inspection'];

    public function up(): void
    {
        if (! Schema::hasTable('inspection_summary_options')) {
            Schema::create('inspection_summary_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inspection_type_id')->constrained('inspection_types')->cascadeOnDelete();
                // The legacy area this option was copied from, if any.
                $table->unsignedBigInteger('summary_type_id')->nullable();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->unsignedInteger('sequence')->default(0);
                $table->timestamps();

                $table->index(['inspection_type_id', 'sequence']);
            });
        }

        Schema::table('inspection_summaries', function (Blueprint $table) {
            $table->unsignedBigInteger('summary_type_id')->nullable()->change();

            if (! Schema::hasColumn('inspection_summaries', 'summary_option_id')) {
                // No foreign key: removing an option from a template must not
                // delete notes already printed on issued reports.
                $table->unsignedBigInteger('summary_option_id')->nullable()->after('summary_type_id');
                $table->unique(['inspection_id', 'summary_option_id'], 'insp_summary_option_unique');
            }
        });

        $legacy = DB::table('tbl_summary_type')
            ->where('summary_type_status', 0)
            ->orderBy('summary_type_id')
            ->get(['summary_type_id', 'summary_type_name', 'summary_type_name_ar']);

        foreach (DB::table('inspection_types')->whereIn('name', self::TEMPLATES)->pluck('id') as $typeId) {
            if (DB::table('inspection_summary_options')->where('inspection_type_id', $typeId)->exists()) {
                continue;
            }

            $now = now();
            foreach ($legacy->values() as $i => $area) {
                $optionId = DB::table('inspection_summary_options')->insertGetId([
                    'inspection_type_id' => $typeId,
                    'summary_type_id' => $area->summary_type_id,
                    'name' => $area->summary_type_name,
                    'name_ar' => filled($area->summary_type_name_ar) ? $area->summary_type_name_ar : null,
                    'sequence' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('inspection_summaries')
                    ->where('summary_type_id', $area->summary_type_id)
                    ->whereNull('summary_option_id')
                    ->whereIn('inspection_id', DB::table('inspections')->where('inspection_type_id', $typeId)->select('id'))
                    ->update(['summary_option_id' => $optionId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inspection_summaries', 'summary_option_id')) {
            // Option-only notes have no legacy area to fall back to.
            DB::table('inspection_summaries')->whereNull('summary_type_id')->delete();

            Schema::table('inspection_summaries', function (Blueprint $table) {
                $table->dropUnique('insp_summary_option_unique');
                $table->dropColumn('summary_option_id');
            });
        }

        Schema::dropIfExists('inspection_summary_options');
    }
};
