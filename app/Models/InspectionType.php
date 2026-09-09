<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class InspectionType extends Model
{
    protected $fillable = ['name', 'description', 'is_active', 'sequence', 'has_diagnostic_media'];

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
