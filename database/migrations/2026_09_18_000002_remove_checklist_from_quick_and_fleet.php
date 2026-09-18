<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Quick and Fleet inspections are summary-only: vehicle details, the verdict
 * and one note per Summary option. Their checklist sections and questions are
 * removed, together with every answer, section photo and section note already
 * recorded against them (and the files behind those photos).
 *
 * Diagnostic Media is switched on for both, so the technician still has a
 * place to attach photos — those print as the report's Vehicle Photos.
 *
 * Not reversible: the deleted answers and photos cannot be restored.
 */
return new class extends Migration
{
    private const TEMPLATES = ['Quick Inspection', 'Fleet Inspection – Corporate Customers'];

    public function up(): void
    {
        $typeIds = DB::table('inspection_types')->whereIn('name', self::TEMPLATES)->pluck('id');
        $sectionIds = DB::table('inspection_sections')->whereIn('inspection_type_id', $typeIds)->pluck('id');
        $stepIds = DB::table('inspection_steps')->whereIn('inspection_section_id', $sectionIds)->pluck('id');

        DB::transaction(function () use ($typeIds, $sectionIds, $stepIds) {
            $detailIds = DB::table('inspection_details')
                ->where(fn ($q) => $q->whereIn('inspection_step_id', $stepIds)->orWhereIn('inspection_section_id', $sectionIds))
                ->pluck('id');

            foreach (DB::table('inspection_media')->whereIn('inspection_detail_id', $detailIds)->get(['disk', 'path']) as $media) {
                if ($media->path) {
                    try {
                        Storage::disk($media->disk ?: 'public')->delete($media->path);
                    } catch (\Throwable $e) {
                        // A missing or unreadable file must not block the cleanup.
                    }
                }
            }

            DB::table('inspection_media')->whereIn('inspection_detail_id', $detailIds)->delete();
            DB::table('inspection_details')->whereIn('id', $detailIds)->delete();
            DB::table('inspection_section_summaries')->whereIn('inspection_section_id', $sectionIds)->delete();
            DB::table('damage_diagrams')->whereIn('inspection_section_id', $sectionIds)->update(['inspection_section_id' => null]);
            DB::table('inspection_steps')->whereIn('id', $stepIds)->delete();
            DB::table('inspection_sections')->whereIn('id', $sectionIds)->delete();

            DB::table('inspection_types')->whereIn('id', $typeIds)->update(['has_diagnostic_media' => true]);
        });
    }

    public function down(): void
    {
        // Deleted sections, questions, answers and photos cannot be recreated.
    }
};
