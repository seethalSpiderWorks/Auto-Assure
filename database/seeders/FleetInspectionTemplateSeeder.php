<?php

namespace Database\Seeders;

/**
 * "Fleet Inspection – Corporate Customers" — the fleet check run for corporate
 * accounts, which prints the same basic report as the Quick Inspection: cover,
 * Inspection Summary, Vehicle Photos, Vehicle Summary and Paint Inspection
 * Images, with no per-question checklist pages.
 *
 * It therefore carries the same sections and questions as
 * QuickInspectionTemplateSeeder — one verdict plus a note per area — and only
 * the name and description differ. Which templates print the basic report is
 * listed in InspectionType::BASIC_REPORT_TYPES.
 *
 * Safe to re-run: see the parent for the desired-state behaviour.
 */
class FleetInspectionTemplateSeeder extends QuickInspectionTemplateSeeder
{
    protected const TYPE_NAME = 'Fleet Inspection – Corporate Customers';

    protected const TYPE_DESCRIPTION = 'Fleet inspection for corporate customers — a visual assessment of the '
        .'exterior, interior, engine, brakes, transmission and undercarriage with photos of any faults, a damage '
        .'mark-up and a short note per area. Prints the same basic report as the Quick Inspection.';
}
