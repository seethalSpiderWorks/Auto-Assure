<?php

namespace Database\Seeders;

use App\Models\InspectionSection;
use App\Models\InspectionStep;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;

/**
 * Turns off the text answer box on every Premium Inspection question, the
 * same as Comprehensive. Answers already typed are kept in the database; the
 * box just stops being offered. Safe to re-run.
 *
 *   php artisan db:seed --class=PremiumNoTextAnswerSeeder --force
 */
class PremiumNoTextAnswerSeeder extends Seeder
{
    private const TYPE_NAME = 'Premium Inspection';

    public function run(): void
    {
        $type = InspectionType::where('name', self::TYPE_NAME)->first();

        if (! $type) {
            $this->command?->error('No '.self::TYPE_NAME.' template found.');

            return;
        }

        $changed = InspectionStep::whereIn('inspection_section_id', InspectionSection::where('inspection_type_id', $type->id)->pluck('id'))
            ->where('show_text_answer', true)
            ->update(['show_text_answer' => false]);

        $this->command?->info("  Text answer turned off on {$changed} question(s).");
    }
}
