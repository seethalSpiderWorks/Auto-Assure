<?php

namespace Database\Seeders;

use App\Models\InspectionDetail;
use App\Models\InspectionSection;
use App\Models\InspectionStep;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;

/**
 * Makes Premium's Aftermarket section match Comprehensive's After Market
 * section: the same questions, Arabic, answer options, settings and order.
 *
 * Read from Comprehensive's own rows rather than a hard-coded list, so it
 * copies whatever that section holds on the server it runs on. Questions are
 * matched on their text, ignoring case — a matched Premium question is updated
 * in place, so answers already given to it are kept. A Premium question that
 * Comprehensive does not have is removed only when nobody has answered it;
 * an answered one is kept and reported. Safe to re-run.
 *
 *   php artisan db:seed --class=PremiumAftermarketSyncSeeder --force
 */
class PremiumAftermarketSyncSeeder extends Seeder
{
    private const SOURCE = ['Comprehensive Vehicle Inspection', 'After Market'];

    private const TARGET = ['Premium Inspection', 'Aftermarket Added Accessories'];

    /** Step columns copied from Comprehensive. */
    private const FIELDS = [
        'sequence', 'question', 'question_ar', 'description', 'description_ar',
        'show_rating', 'show_text_answer', 'show_multiple_choice', 'multiple_choice_options',
        'show_remedial_suggestions', 'photos', 'videos',
    ];

    public function run(): void
    {
        $source = $this->section(...self::SOURCE);
        $target = $this->section(...self::TARGET);

        if (! $source || ! $target) {
            return;
        }

        $existing = InspectionStep::where('inspection_section_id', $target->id)->get()
            ->keyBy(fn ($s) => mb_strtolower(trim($s->question)));
        $kept = [];

        foreach (InspectionStep::where('inspection_section_id', $source->id)->orderBy('sequence')->get() as $from) {
            $key = mb_strtolower(trim($from->question));
            $step = $existing->get($key) ?? new InspectionStep(['inspection_section_id' => $target->id]);

            foreach (self::FIELDS as $field) {
                $step->{$field} = $from->{$field};
            }

            $label = ! $step->exists ? 'Added' : ($step->isDirty() ? 'Updated' : 'Already the same');
            $step->save();
            $kept[] = $key;

            $this->command?->line("  {$label}: {$from->question}");
        }

        // toBase(): an Eloquent collection's except() goes by model id, not by
        // these question keys.
        foreach ($existing->toBase()->except($kept) as $extra) {
            if (InspectionDetail::where('inspection_step_id', $extra->id)->exists()) {
                $this->command?->warn("  Kept (has answers): {$extra->question}");

                continue;
            }

            $extra->delete();
            $this->command?->line("  Removed: {$extra->question}");
        }
    }

    private function section(string $typeName, string $sectionName): ?InspectionSection
    {
        $type = InspectionType::where('name', $typeName)->first();
        $section = $type
            ? InspectionSection::where('inspection_type_id', $type->id)->where('section_name', $sectionName)->first()
            : null;

        if (! $section) {
            $this->command?->error("No “{$sectionName}” section found on the {$typeName} template.");
        }

        return $section;
    }
}
