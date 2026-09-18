<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One Summary title configured on a template (Exterior, Engine, …). Inspections
 * of that template ask for one note per option instead of the legacy
 * tbl_summary_type areas.
 */
class InspectionSummaryOption extends Model
{
    protected $fillable = ['inspection_type_id', 'summary_type_id', 'name', 'name_ar', 'sequence'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class, 'inspection_type_id');
    }
}
