<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Inspection extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const CONDITIONS = [
        'excellent' => 'Excellent',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
    ];

    public const RECOMMENDATIONS = [
        'buy' => 'Recommend Buying',
        'buy_with_repairs' => 'Buy (with repairs)',
        'avoid' => 'Advise Against',
    ];

    protected $fillable = [
        'lead_id', 'branch_id', 'technician_id', 'inspection_type_id',
        'customer_name', 'customer_email', 'customer_phone', 'car_make', 'car_model', 'car_year',
        'status', 'scheduled_at', 'started_at', 'completed_at',
        'odometer', 'overall_condition', 'summary', 'recommendation', 'estimated_repair_cost',
        // Extended vehicle details (inspection edit page)
        'vin', 'registration_number', 'variant', 'color', 'fuel_type', 'transmission',
        'body_type', 'number_of_keys', 'vehicle_type', 'manufacturer_name',
        'country_of_origin', 'country_of_export', 'motor_power_kw', 'cylinders_cc',
        'passengers', 'fuel_economy',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'estimated_repair_cost' => 'decimal:2',
        ];
    }

    public function lead(): BelongsTo
    {
        // Lead's primary key is lead_id, so name the keys explicitly — otherwise
        // Laravel guesses the foreign key as "lead_lead_id" and the relation is null.
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Branch\Models\BranchModel::class, 'branch_id', 'branch_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class, 'inspection_type_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(InspectionDetail::class);
    }

    /**
     * Number of template steps that have been answered so far.
     */
    public function progress(): array
    {
        $total = $this->type
            ? $this->type->steps()->count()
            : 0;
        // Only checklist-step answers count toward progress — the step-less
        // "additional media" bucket must not inflate the answered total.
        $answered = $this->details()->whereNotNull('inspection_step_id')->count();

        return [
            'answered' => $answered,
            'total' => $total,
            'percent' => $total > 0 ? (int) round($answered / $total * 100) : 0,
        ];
    }

    public function car(): string
    {
        return trim("{$this->car_year} {$this->car_make} {$this->car_model}");
    }

    /**
     * Is this saved answer meaningful (a choice, a rating, or written text)?
     */
    public static function detailIsAnswered($detail): bool
    {
        if (! $detail) {
            return false;
        }

        return ($detail->choice !== null && $detail->choice !== '')
            || ((int) ($detail->rating ?? 0) > 0)
            || ($detail->descriptive_answer !== null && $detail->descriptive_answer !== '');
    }

    /**
     * Per-section completion breakdown for the edit screen and the
     * "all answered before complete" gate.
     *
     * @return array<int, array{id:int,name:string,total:int,answered:int,done:bool}>
     */
    public function sectionProgress(): array
    {
        $this->loadMissing(['type.sections.steps', 'details']);
        $byStep = $this->details->keyBy('inspection_step_id');
        $out = [];

        foreach ($this->type?->sections ?? [] as $section) {
            $total = $section->steps->count();
            $answered = 0;

            foreach ($section->steps as $step) {
                if (self::detailIsAnswered($byStep->get($step->id))) {
                    $answered++;
                }
            }

            $out[] = [
                'id' => $section->id,
                'name' => $section->section_name,
                'total' => $total,
                'answered' => $answered,
                'done' => $total > 0 && $answered >= $total,
            ];
        }

        return $out;
    }

    /**
     * Every templated question has a meaningful answer.
     */
    public function isFullyAnswered(): bool
    {
        foreach ($this->sectionProgress() as $s) {
            if (! $s['done']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Questions whose mandatory photo/video requirement is not yet satisfied.
     *
     * @return array<int, string>
     */
    public function missingMandatoryMedia(): array
    {
        $this->loadMissing(['type.sections.steps', 'details.media']);
        $byStep = $this->details->keyBy('inspection_step_id');
        $missing = [];

        foreach ($this->type->sections as $section) {
            foreach ($section->steps as $step) {
                $detail = $byStep->get($step->id);
                $photos = $detail ? $detail->media->where('type', 'photo')->count() : 0;
                $videos = $detail ? $detail->media->where('type', 'video')->count() : 0;

                if ($step->photos === InspectionStep::MEDIA_MANDATORY && $photos === 0) {
                    $missing[] = $step->question.' (photo)';
                }
                if ($step->videos === InspectionStep::MEDIA_MANDATORY && $videos === 0) {
                    $missing[] = $step->question.' (video)';
                }
            }
        }

        return $missing;
    }

    /**
     * Create (or re-point) the inspection for a legacy lead when a technician
     * is assigned for inspection. One inspection per lead: if it already exists
     * the technician is updated instead. Always records the inspection id on
     * tbl_lead.inspection_assigned_id so the lead links to its inspection.
     *
     * @param  int  $leadId        tbl_lead.lead_id
     * @param  int  $technicianId  users.id of the assigned technician
     * @param  mixed $scheduledAt  optional schedule date/time
     */
    public static function createForLead(int $leadId, int $technicianId, $scheduledAt = null, ?int $inspectionTypeId = null): ?self
    {
        if ($technicianId <= 0) {
            return null;
        }

        $lead = DB::table('tbl_lead')->where('lead_id', $leadId)->first();
        if (! $lead) {
            return null;
        }

        // Resolve the chosen inspection template; fall back to the first active one.
        $typeId = static::resolveInspectionTypeId($inspectionTypeId);

        $inspection = static::where('lead_id', $leadId)->latest('id')->first();

        if ($inspection) {
            // Re-assign: point the existing inspection at the new technician/template.
            $inspection->technician_id = $technicianId;
            $inspection->inspection_type_id = $typeId;
            if ($scheduledAt) {
                $inspection->scheduled_at = $scheduledAt;
            }
            $inspection->save();
        } else {
            $basicReg = DB::table('tbl_basic_registration')->where('breg_id', $lead->lead_reg_id)->first();
            $branchId = $lead->lead_branch_id ?: (session('application_branch') ?: 1);
            $year = is_numeric($lead->lead_year) ? (int) $lead->lead_year : null;

            // lead_make / lead_model hold lookup IDs — resolve them to names so the
            // inspection stores the make/model name, not the raw id.
            $carMake = static::resolveName('tbl_make', 'make_id', 'make_name', $lead->lead_make ?? null);
            $carModel = static::resolveName('tbl_model', 'model_id', 'model_name', $lead->lead_model ?? null);

            $inspection = static::create([
                'lead_id'            => $leadId,
                'branch_id'          => $branchId,
                'technician_id'      => $technicianId,
                'inspection_type_id' => $typeId,
                'customer_name'      => ($basicReg?->breg_fname) ?: ($lead->lead_seller_name ?: 'N/A'),
                'customer_email'     => $basicReg?->breg_email,
                'customer_phone'     => ($basicReg?->breg_mob) ?: ($lead->lead_seller_mobile ?? null),
                'car_make'           => $carMake,
                'car_model'          => $carModel,
                'car_year'           => $year,
                'status'             => static::STATUS_PENDING,
                'scheduled_at'       => $scheduledAt ?: null,
            ]);
        }

        // The inspection links back to the lead via inspections.lead_id; no separate
        // tbl_lead.inspection_assigned_id pointer is maintained.
        return $inspection;
    }

    /**
     * Resolve the inspection template (type) to use. Honors the explicitly chosen
     * template when it is an active type; otherwise falls back to the first active
     * template (or 1 when none are configured).
     */
    protected static function resolveInspectionTypeId(?int $inspectionTypeId): int
    {
        if ($inspectionTypeId) {
            $valid = InspectionType::where('is_active', 1)
                ->where('id', $inspectionTypeId)
                ->value('id');
            if ($valid) {
                return (int) $valid;
            }
        }

        return (int) (InspectionType::where('is_active', 1)->orderBy('id')->value('id') ?? 1);
    }

    /**
     * Resolve a lookup id (e.g. a make/model id stored on the lead) to its name.
     * Returns null when the value is empty or no matching row exists, so we never
     * store the raw id in place of a missing name. A non-numeric value is assumed
     * to already be a name and is passed through unchanged.
     */
    protected static function resolveName(string $table, string $idColumn, string $nameColumn, $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return (string) $value;
        }

        $name = DB::table($table)->where($idColumn, $value)->value($nameColumn);

        return $name !== null && $name !== '' ? (string) $name : null;
    }

    public function statusColor(): string
    {
        return [
            self::STATUS_PENDING => 'bg-gray-100 text-gray-700',
            self::STATUS_IN_PROGRESS => 'bg-amber-100 text-amber-700',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-700',
        ][$this->status] ?? 'bg-gray-100 text-gray-700';
    }
}
