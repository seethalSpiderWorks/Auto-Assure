<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionDetail extends Model
{
    protected $fillable = [
        'inspection_id', 'inspection_step_id',
        'descriptive_answer', 'rating', 'choice', 'remedial_suggestion',
    ];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(InspectionStep::class, 'inspection_step_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(InspectionMedia::class);
    }

    public function photos(): HasMany
    {
        return $this->media()->where('type', 'photo');
    }

    public function videos(): HasMany
    {
        return $this->media()->where('type', 'video');
    }
}
