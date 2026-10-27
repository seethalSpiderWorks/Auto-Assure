<?php

namespace Database\Seeders;

use App\Models\InspectionSection;
use App\Models\InspectionStep;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;

/**
 * Adds Comprehensive's After Market questions to Premium's "Aftermarket Added
 * Accessories" section, with their Arabic.
 *
 * Only creates the questions that are missing — existing questions (including
 * Fire Extinguisher and its answers) are never touched, and nothing else in the
 * Premium template is changed. New questions go after the existing ones. Safe
 * to re-run.
 *
 *   php artisan db:seed --class=PremiumAftermarketSeeder --force
 */
class PremiumAftermarketSeeder extends Seeder
{
    private const TYPE_NAME = 'Premium Inspection';

    private const SECTION_NAME = 'Aftermarket Added Accessories';

    private const CHOICES = ['Good', 'Bad', 'NA', 'Average'];

    /** Question => Arabic, in display order. */
    private const QUESTIONS = [
        'Winch' => 'ونش سحب',
        'Body Kit' => 'طقم هيكل خارجي',
        'Lift Kit' => 'طقم رفع',
        'Leather Seats' => 'مقاعد جلد',
        'Rear Seat Entertain. Sys' => 'نظام ترفيه المقاعد الخلفية',
        'Parking Sensors' => 'حساسات الركن',
        'Rear View Camera' => 'كاميرا الرؤية الخلفية',
        'Navigation' => 'نظام الملاحة',
    ];

    public function run(): void
    {
        $type = InspectionType::where('name', self::TYPE_NAME)->first();
        $section = $type
            ? InspectionSection::where('inspection_type_id', $type->id)->where('section_name', self::SECTION_NAME)->first()
            : null;

        if (! $section) {
            $this->command?->error('No “'.self::SECTION_NAME.'” section found on the '.self::TYPE_NAME.' template.');

            return;
        }

        $sequence = (int) InspectionStep::where('inspection_section_id', $section->id)->max('sequence');

        foreach (self::QUESTIONS as $question => $arabic) {
            $step = InspectionStep::firstOrCreate(
                ['inspection_section_id' => $section->id, 'question' => $question],
                [
                    'question_ar' => $arabic,
                    'sequence' => $sequence + 1,
                    'show_multiple_choice' => true,
                    'multiple_choice_options' => self::CHOICES,
                    'show_rating' => false,
                    'show_text_answer' => false,
                    'show_remedial_suggestions' => false,
                    'photos' => InspectionStep::MEDIA_NOT_REQUIRED,
                    'videos' => InspectionStep::MEDIA_NOT_REQUIRED,
                ]
            );

            if ($step->wasRecentlyCreated) {
                $sequence++;
                $this->command?->info("  Added: {$question}");
            } else {
                $this->command?->line("  Already there: {$question}");
            }
        }
    }
}
