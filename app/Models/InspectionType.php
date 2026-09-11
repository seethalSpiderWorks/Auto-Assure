<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class InspectionType extends Model
{
    protected $fillable = ['name', 'name_ar', 'description', 'description_ar', 'is_active', 'sequence', 'has_diagnostic_media'];

    /**
     * Templates whose report prints the basic format the client asked for —
     * cover / Vehicle Summary, Inspection Summary, Vehicle Photos and Paint
     * Inspection Images only. Everything else (checklist pages, diagnostic
     * media, EV & PHEV, technical measurements, signatures) is left out.
     *
     * Kept as a list of names rather than a column so no schema change is
     * needed; add a template here to give it the basic report.
     */
    public const BASIC_REPORT_TYPES = [
        'Quick Inspection',
        'Fleet Inspection – Corporate Customers',
    ];

    /**
     * Does this template print the basic report?
     */
    public function isBasicReport(): bool
    {
        $name = trim(mb_strtolower((string) $this->name));

        foreach (self::BASIC_REPORT_TYPES as $basic) {
            // Compare loosely on dashes and spacing — the corporate template's
            // name carries an en dash that is easy to retype as a hyphen.
            if ($this->normalise($name) === $this->normalise(mb_strtolower($basic))) {
                return true;
            }
        }

        return false;
    }

    private function normalise(string $name): string
    {
        return preg_replace('/\s+/', ' ', str_replace(['–', '—', '-'], '-', $name));
    }

    /**
     * The kind of report this template issues — "Comprehensive", "Premium",
     * "Quick", … — used for the cover heading and the page header, so a Quick
     * report is not titled "Comprehensive".
     */
    public function reportKind(): string
    {
        $kind = trim(preg_replace('/\b(vehicle\s+)?inspection\b/i', ' ', (string) $this->name));
        $kind = trim(preg_replace('/\s+/', ' ', $kind), " \t\n\r\0\x0B-–—");

        return $kind !== '' ? $kind : 'Vehicle';
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'has_diagnostic_media' => 'boolean'];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(InspectionSection::class)->orderBy('sequence');
    }

    public function steps(): HasManyThrough
    {
        return $this->hasManyThrough(InspectionStep::class, InspectionSection::class);
    }

    public function stepCount(): int
    {
        return $this->steps()->count();
    }
}
