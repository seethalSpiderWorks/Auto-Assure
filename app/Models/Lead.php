<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lightweight Eloquent wrapper around the legacy `tbl_lead` table so the
 * inspection feature (built against a modern `leads` schema) can read/write
 * the CRM's real lead records without a separate table.
 */
class Lead extends Model
{
    // Inspection feature status vocabulary -> stored in `lead_assigned_status`.
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $table = 'tbl_lead';
    protected $primaryKey = 'lead_id';

    protected $guarded = [];

    /**
     * Reference shown in listings (e.g. "LD00001").
     */
    public function getReferenceAttribute(): ?string
    {
        return $this->lead_unq_id;
    }

    /**
     * Customer/seller name for this lead.
     */
    public function getCustomerNameAttribute(): ?string
    {
        return $this->lead_seller_name;
    }

    public function getMobileAttribute(): ?string
    {
        return $this->lead_seller_mobile ?: $this->lead_your_mobile;
    }

    /**
     * Best-effort vehicle description from the legacy columns.
     */
    public function car(): string
    {
        $car = trim((string) ($this->make_model_year ?: $this->lead_add_details));

        if ($car === '') {
            $car = trim((string) $this->lead_year);
        }

        return $car;
    }

    /**
     * Map the inspection feature's string status onto the legacy
     * `lead_assigned_status` column instead of a non-existent `status` column.
     */
    public function setStatusAttribute($value): void
    {
        $map = [
            self::STATUS_IN_PROGRESS => 'Inspection In Progress',
            self::STATUS_COMPLETED => 'Inspection Completed',
        ];

        $this->attributes['lead_assigned_status'] = $map[$value] ?? $value;
    }

    /**
     * The inspection assigned to this lead, linked via inspection_assigned_id.
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_assigned_id', 'id');
    }
}
