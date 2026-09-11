<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * A free-text note against one summary type (Exterior, Engine, Brakes, …) for
 * an inspection. Types come from the legacy tbl_summary_type lookup, which the
 * /inspectionreport summary tab also reads.
 */
class InspectionSummary extends Model
{
    protected $fillable = ['inspection_id', 'summary_type_id', 'summary', 'summary_ar'];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    /**
     * Active summary types as id => name, in lookup order.
     *
     * @return array<int, string>
     */
    public static function types(): array
    {
        return DB::table('tbl_summary_type')
            ->where('summary_type_status', 0)
            ->orderBy('summary_type_id')
            ->pluck('summary_type_name', 'summary_type_id')
            ->all();
    }

    /**
     * The same list in Arabic, from the lookup's own summary_type_name_ar
     * column — the names the legacy report prints. Falls back to the English
     * name for any type left untranslated.
     *
     * @return array<int, string>
     */
    public static function typesAr(): array
    {
        return DB::table('tbl_summary_type')
            ->where('summary_type_status', 0)
            ->orderBy('summary_type_id')
            ->get(['summary_type_id', 'summary_type_name', 'summary_type_name_ar'])
            ->mapWithKeys(fn ($t) => [
                (int) $t->summary_type_id => filled($t->summary_type_name_ar)
                    ? $t->summary_type_name_ar
                    : $t->summary_type_name,
            ])
            ->all();
    }
}
