<?php

namespace Database\Seeders;

use App\Models\InspectionSection;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;

/**
 * Arabic names for every template, group and section.
 *
 * The wording is Auto Assure's own wherever the company already publishes it:
 * tbl_summary_type.summary_type_name_ar supplies Exterior, Interior, Engine,
 * Brakes, Transmission, Suspension & Steering, Tires & Wheels, Undercarriage,
 * Safety Systems and Road Test, and the bilingual report preview supplies the
 * rest (بيانات المركبة, الفحص الخارجي والهيكل …).
 *
 * Only names are translated here. The individual questions — 370 of them across
 * the four templates — are left in English: they are technical wording that
 * should be translated by someone who knows the trade, not guessed at. The
 * report falls back to the English question wherever question_ar is empty, so
 * an Arabic report is complete either way.
 *
 * Existing Arabic is never overwritten; fill a field by hand and this seeder
 * leaves it alone. Safe to re-run.
 */
class TemplateArabicSeeder extends Seeder
{
    /** Template name => Arabic name. */
    private const TYPES = [
        'Comprehensive Vehicle Inspection' => 'الفحص الشامل للمركبة',
        'Premium Inspection' => 'الفحص المتميز',
        'Quick Inspection' => 'الفحص السريع',
        'Fleet Inspection – Corporate Customers' => 'فحص الأساطيل — عملاء الشركات',
    ];

    /** Group name => Arabic name. */
    private const GROUPS = [
        'Vehicle Specifications' => 'مواصفات المركبة',
        'Inspection Checklist' => 'قائمة الفحص',
    ];

    /** Section name => Arabic name. */
    private const SECTIONS = [
        // Vehicle Specifications
        'Performance' => 'الأداء',
        'Safety' => 'السلامة',
        'Interior - Entertainment' => 'الداخلية والترفيه',
        'After Market' => 'إضافات ما بعد البيع',
        'Aftermarket Added Accessories' => 'إضافات ما بعد البيع',

        // Inspection Checklist — the ten that tbl_summary_type already names in Arabic
        'Exterior' => 'الجزء الخارجي',
        'Interior' => 'الجزء الداخلي',
        'Engine' => 'المحرك',
        'Brakes' => 'الفرامل',
        'Transmission' => 'تحويل/ تبديل السرعات',
        'Suspension & Steering' => 'نظام التعليق والمحرك',
        'Tires & Wheels' => 'الإطارات والعجلات',
        'Undercarriage' => 'الهيكل السفلي',
        'Safety Systems' => 'نظم الأمن والسلامة',
        'Road Test' => 'اختبار الطريق',

        // The remaining checklist sections
        'Tyre' => 'الإطارات',
        'Electrical' => 'الكهرباء والإلكترونيات',
        'Underbody' => 'الهيكل السفلي',
        'Test Drive' => 'اختبار القيادة',
    ];

    public function run(): void
    {
        $types = 0;

        foreach (InspectionType::all() as $type) {
            $arabic = self::TYPES[$type->name] ?? null;

            if ($arabic && blank($type->name_ar)) {
                $type->forceFill(['name_ar' => $arabic])->save();
                $types++;
            }
        }

        $sections = 0;
        $groups = 0;
        $missing = [];

        foreach (InspectionSection::all() as $section) {
            $changes = [];

            if (blank($section->section_name_ar)) {
                if ($arabic = self::SECTIONS[$section->section_name] ?? null) {
                    $changes['section_name_ar'] = $arabic;
                    $sections++;
                } else {
                    $missing[] = $section->section_name;
                }
            }

            if ($section->group_name && blank($section->group_name_ar)) {
                if ($arabic = self::GROUPS[$section->group_name] ?? null) {
                    $changes['group_name_ar'] = $arabic;
                    $groups++;
                }
            }

            if ($changes) {
                $section->forceFill($changes)->save();
            }
        }

        $this->command?->info("Arabic filled — {$types} templates, {$groups} group names, {$sections} section names.");

        if ($missing) {
            $this->command?->warn('  No Arabic for: '.implode(', ', array_unique($missing)));
        }
    }
}
