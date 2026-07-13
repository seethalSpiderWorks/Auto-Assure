<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionStep extends Model
{
    public const MEDIA_NOT_REQUIRED = 'not_required';
    public const MEDIA_OPTIONAL = 'optional';
    public const MEDIA_MANDATORY = 'mandatory';

    public const MEDIA_OPTIONS = [
        self::MEDIA_NOT_REQUIRED => 'Not required',
        self::MEDIA_OPTIONAL => 'Optional',
        self::MEDIA_MANDATORY => 'Mandatory',
    ];

    protected $fillable = [
        'inspection_section_id', 'sequence', 'question', 'question_ar', 'description', 'description_ar',
        'show_rating', 'show_text_answer', 'show_multiple_choice', 'multiple_choice_options',
        'show_remedial_suggestions', 'photos', 'videos',
    ];

    protected function casts(): array
    {
        return [
            'show_rating' => 'boolean',
            'show_text_answer' => 'boolean',
            'show_multiple_choice' => 'boolean',
            'show_remedial_suggestions' => 'boolean',
            'multiple_choice_options' => 'array',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(InspectionSection::class, 'inspection_section_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(InspectionDetail::class);
    }
}
