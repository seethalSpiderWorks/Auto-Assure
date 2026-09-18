<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Summary options for the Quick and Fleet templates: a shorter list than the
 * ten standard areas. Names (and their Arabic) are taken from tbl_summary_type,
 * and existing notes on those areas are moved onto the new options.
 *
 * Templates that already have options are left alone.
 */
return new class extends Migration
{
    private const TEMPLATES = ['Quick Inspection', 'Fleet Inspection – Corporate Customers'];

    private const AREAS = ['Exterior', 'Interior', 'Engine', 'Brakes', 'Transmission', 'Undercarriage', 'Road Test'];

    public function up(): void
    {
        $legacy = DB::table('tbl_summary_type')
            ->where('summary_type_status', 0)
            ->get(['summary_type_id', 'summary_type_name', 'summary_type_name_ar'])
            ->keyBy(fn ($t) => mb_strtolower(trim($t->summary_type_name)));

        foreach (DB::table('inspection_types')->whereIn('name', self::TEMPLATES)->pluck('id') as $typeId) {
            if (DB::table('inspection_summary_options')->where('inspection_type_id', $typeId)->exists()) {
                continue;
            }

            $now = now();
            foreach (self::AREAS as $i => $name) {
                $area = $legacy->get(mb_strtolower($name));

                $optionId = DB::table('inspection_summary_options')->insertGetId([
                    'inspection_type_id' => $typeId,
                    'summary_type_id' => $area?->summary_type_id,
                    'name' => $name,
                    'name_ar' => filled($area?->summary_type_name_ar) ? $area->summary_type_name_ar : null,
                    'sequence' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                if ($area) {
                    DB::table('inspection_summaries')
                        ->where('summary_type_id', $area->summary_type_id)
                        ->whereNull('summary_option_id')
                        ->whereIn('inspection_id', DB::table('inspections')->where('inspection_type_id', $typeId)->select('id'))
                        ->update(['summary_option_id' => $optionId]);
                }
            }
        }
    }

    public function down(): void
    {
        $typeIds = DB::table('inspection_types')->whereIn('name', self::TEMPLATES)->pluck('id');
        $optionIds = DB::table('inspection_summary_options')->whereIn('inspection_type_id', $typeIds)->pluck('id');

        DB::table('inspection_summaries')->whereIn('summary_option_id', $optionIds)->update(['summary_option_id' => null]);
        DB::table('inspection_summary_options')->whereIn('id', $optionIds)->delete();
    }
};
