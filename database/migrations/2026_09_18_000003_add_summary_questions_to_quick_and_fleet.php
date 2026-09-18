<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Quick and Fleet get their checklist back as one question per Summary
 * option: a section named after each summary title (Exterior, Interior, …,
 * Road Test) holding a single Good/Bad/NA/Average judgement with an
 * observation box — the shape QuickInspectionTemplateSeeder defines.
 *
 * Sections follow the template's Summary options in their order, so a title
 * added later just needs a matching section added from the template screen.
 * Templates that already have sections are left alone.
 */
return new class extends Migration
{
    private const TEMPLATES = ['Quick Inspection', 'Fleet Inspection – Corporate Customers'];

    /** Summary title => [question, question in Arabic]. */
    private const QUESTIONS = [
        'Exterior' => ['Exterior condition', 'حالة الهيكل الخارجي'],
        'Interior' => ['Interior condition', 'حالة المقصورة الداخلية'],
        'Engine' => ['Engine condition', 'حالة المحرك'],
        'Brakes' => ['Brakes', 'الفرامل'],
        'Transmission' => ['Transmission', 'ناقل الحركة'],
        'Undercarriage' => ['Undercarriage / chassis condition', 'حالة الهيكل السفلي / الشاسيه'],
        'Road Test' => ['Road test', 'اختبار الطريق'],
    ];

    public function up(): void
    {
        $now = now();

        foreach (DB::table('inspection_types')->whereIn('name', self::TEMPLATES)->pluck('id') as $typeId) {
            if (DB::table('inspection_sections')->where('inspection_type_id', $typeId)->exists()) {
                continue;
            }

            $options = DB::table('inspection_summary_options')
                ->where('inspection_type_id', $typeId)
                ->orderBy('sequence')->orderBy('id')
                ->get(['name', 'name_ar']);

            foreach ($options->values() as $i => $option) {
                [$question, $questionAr] = self::QUESTIONS[$option->name] ?? [$option->name.' condition', null];

                $sectionId = DB::table('inspection_sections')->insertGetId([
                    'inspection_type_id' => $typeId,
                    'group_name' => 'Inspection Checklist',
                    'group_name_ar' => 'قائمة الفحص',
                    'section_name' => $option->name,
                    'section_name_ar' => $option->name_ar,
                    'sequence' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('inspection_steps')->insert([
                    'inspection_section_id' => $sectionId,
                    'sequence' => 1,
                    'question' => $question,
                    'question_ar' => $questionAr,
                    'show_rating' => false,
                    'show_text_answer' => true,
                    'show_multiple_choice' => true,
                    'multiple_choice_options' => json_encode(['Good', 'Bad', 'NA', 'Average']),
                    'show_remedial_suggestions' => false,
                    'photos' => 'not_required',
                    'videos' => 'not_required',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $typeIds = DB::table('inspection_types')->whereIn('name', self::TEMPLATES)->pluck('id');
        $sectionIds = DB::table('inspection_sections')->whereIn('inspection_type_id', $typeIds)->pluck('id');
        $stepIds = DB::table('inspection_steps')->whereIn('inspection_section_id', $sectionIds)->pluck('id');
        $detailIds = DB::table('inspection_details')
            ->where(fn ($q) => $q->whereIn('inspection_step_id', $stepIds)->orWhereIn('inspection_section_id', $sectionIds))
            ->pluck('id');

        DB::table('inspection_media')->whereIn('inspection_detail_id', $detailIds)->delete();
        DB::table('inspection_details')->whereIn('id', $detailIds)->delete();
        DB::table('inspection_section_summaries')->whereIn('inspection_section_id', $sectionIds)->delete();
        DB::table('inspection_steps')->whereIn('id', $stepIds)->delete();
        DB::table('inspection_sections')->whereIn('id', $sectionIds)->delete();
    }
};
