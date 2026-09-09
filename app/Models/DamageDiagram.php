<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * One body view a technician can mark damage on (Full Body, Under Body, …).
 *
 * The key is the stable identifier: it is what inspections.damage_images and
 * inspections.damage_marks are keyed by, so renaming a diagram is safe but
 * changing its key orphans everything already drawn on it.
 */
class DamageDiagram extends Model
{
    protected $fillable = ['name', 'key', 'inspection_section_id', 'image', 'sequence', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sequence' => 'integer'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence')->orderBy('id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(InspectionSection::class, 'inspection_section_id');
    }

    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('inspection_section_id', $sectionId);
    }

    public function colours(): HasMany
    {
        return $this->hasMany(DamageColour::class, 'damage_diagram_id');
    }

    /** Active palette for this diagram, in display order. */
    public function palette()
    {
        return DamageColour::active()->forDiagram($this->id)->ordered()->get();
    }

    public function imageUrl(): ?string
    {
        return $this->image ? url('storage/'.ltrim($this->image, '/')) : null;
    }

    public function imageExists(): bool
    {
        try {
            return (bool) $this->image && Storage::disk('public')->exists($this->image);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
