<?php

namespace App\Support;

/**
 * The "[ar]" convention the legacy report screen uses for bilingual text.
 *
 * Modules/InspectionReport's form tells the user outright: «English & Arabic
 * comment (example: Good quality[ar]نوعية جيدة)» — one box, the two languages
 * separated by the marker. Everything else there is a pair of fields instead
 * (report_client_name / report_client_name_ar, overview_english /
 * overview_arabic), and the template screens follow that pairing.
 *
 * This makes the inline form work in the template screens as well: type it
 * either way. Whatever follows [ar] is peeled off into the Arabic field, so the
 * two conventions end up in the same two columns.
 */
class BilingualText
{
    public const MARKER = '[ar]';

    /**
     * Split "English[ar]العربية" into its two halves.
     *
     * Without the marker the whole string is the English half and the Arabic one
     * is null. Case and surrounding spaces around the marker are ignored, so
     * "Good quality [AR] نوعية جيدة" splits the same way.
     *
     * @return array{0: string, 1: string|null}
     */
    public static function split(?string $value): array
    {
        $value = (string) $value;

        $parts = preg_split('/\s*\[ar\]\s*/i', $value, 2);

        $english = trim($parts[0] ?? '');
        $arabic = isset($parts[1]) ? trim($parts[1]) : null;

        return [$english, $arabic === '' ? null : $arabic];
    }

    /**
     * Apply the convention to one English/Arabic field pair in a validated array.
     *
     * The Arabic half is only taken from the marker when the Arabic field was
     * left empty — a value typed into the Arabic box always wins, so filling
     * both never silently overwrites one with the other.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function apply(array $data, string $field, ?string $arabicField = null): array
    {
        $arabicField ??= $field.'_ar';

        if (! array_key_exists($field, $data)) {
            return $data;
        }

        [$english, $arabic] = self::split(is_string($data[$field]) ? $data[$field] : null);

        $data[$field] = $english;

        if ($arabic !== null && blank($data[$arabicField] ?? null)) {
            $data[$arabicField] = $arabic;
        }

        return $data;
    }

    /**
     * Apply it to several pairs at once.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $fields
     * @return array<string, mixed>
     */
    public static function applyAll(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            $data = self::apply($data, $field);
        }

        return $data;
    }
}
