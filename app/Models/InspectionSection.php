<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionSection extends Model
{
    protected $fillable = ['inspection_type_id', 'group_name', 'group_name_ar', 'section_name', 'section_name_ar', 'description', 'sequence'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class, 'inspection_type_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(InspectionStep::class)->orderBy('sequence');
    }
}
