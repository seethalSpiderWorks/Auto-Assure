<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A technician's free-text summary and optional rating for one section of one
 * inspection — the section-level note/score shown on the report's "Inspection Summary".
 */
class InspectionSectionSummary extends Model
{
    protected $table = 'inspection_section_summaries';

    protected $fillable = ['inspection_id', 'inspection_section_id', 'summary', 'rating'];

    protected function casts(): array
    {
        // Float, not decimal:1 — decimal casts to a string, which would print
        // "4.6" fine but break the numeric comparisons the star loops rely on.
        return ['rating' => 'float'];
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(InspectionSection::class, 'inspection_section_id');
    }
}
