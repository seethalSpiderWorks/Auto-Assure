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
        return ['rating' => 'integer'];
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
