<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One entry in the damage palette — a label ("Dent") and the colour its dots
 * are drawn in. The hex is what gets stored on each mark, so the save endpoint
 * validates against the active set here.
 */
class DamageColour extends Model
{
    protected $fillable = ['damage_diagram_id', 'label', 'colour', 'description', 'sequence', 'is_active'];

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

    /**
     * Colours for one diagram: its own, plus any left unassigned — a null
     * diagram means the colour applies everywhere.
     */
    public function scopeForDiagram($query, int $diagramId)
    {
        return $query->where(fn ($q) => $q->where('damage_diagram_id', $diagramId)->orWhereNull('damage_diagram_id'));
    }

    public function diagram(): BelongsTo
    {
        return $this->belongsTo(DamageDiagram::class, 'damage_diagram_id');
    }
}
