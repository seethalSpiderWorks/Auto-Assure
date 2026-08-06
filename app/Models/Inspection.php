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
    /** Cancelled by an admin. Kept out of the technician's active list; can be re-opened from the lead. */
    public const STATUS_CANCELLED = 'cancelled';

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
        'customer_name', 'customer_name_ar', 'customer_email', 'customer_phone', 'whatsapp_number',
        'date_of_inspection', 'car_make', 'car_model', 'car_year',
        'status', 'scheduled_at', 'started_at', 'completed_at',
        'cancelled_at', 'cancel_reason', 'cancelled_by',
        'odometer', 'overall_condition', 'overall_rating', 'summary', 'recommendation', 'estimated_repair_cost', 'currency',
        // Extended vehicle details (inspection edit page)
        'manufacturing_year', 'vehicle_condition', 'vin', 'plate_no',
        'exterior_color', 'vehicle_image', 'region',
        'fuel_type', 'gearbox', 'steering_side', 'body_type',
        'number_of_keys', 'with_service_history', 'last_service_date',
    ];

    protected function casts(): array
    {
        return [
            'date_of_inspection' => 'date',
            'last_service_date' => 'date',
            'with_service_history' => 'boolean',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'overall_rating' => 'decimal:1',
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
     * Per-section free-text summaries + optional ratings (the section-level notes
     * shown at the top of the report's "Inspection Summary"), keyed by section id.
     */
    public function sectionSummaries(): HasMany
    {
        return $this->hasMany(InspectionSectionSummary::class);
    }

    /**
     * Free-text notes per summary type (Exterior, Engine, Brakes, …) shown
     * under the Overall Verdict.
     */
    public function summaries(): HasMany
    {
        return $this->hasMany(InspectionSummary::class);
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
     * Reference shown for this inspection: the linked lead's unique id
     * (tbl_lead.lead_unq_id, e.g. "LD01272"), falling back to a generated id
     * for inspections with no lead.
     */
    public function getReferenceAttribute(): string
    {
        return optional($this->lead)->reference
            ?: 'AAQ-'.str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
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

    // Choice vocabulary used to score answers as pass / fail across the report,
    // summary and API. Kept here so every surface reads the same rule.
    public const POSITIVE_CHOICES = ['Pass', 'Yes', 'Working', 'Good', 'Available', 'OK'];

    public const NEGATIVE_CHOICES = ['Fail', 'No', 'Not Working', 'Poor', 'Not Available', 'Bad'];

    /**
     * Choices that mean "doesn't apply to this vehicle". These line items are
     * omitted from the printable report entirely — an unanswered step counts the
     * same way.
     */
    public const NA_CHOICES = ['NA', 'N/A', 'Not Applicable'];

    /**
     * Choices that need a written note — anything less than a clean pass: the
     * outright negatives plus "Average". Picking one of these reveals the
     * Observations box (and the Remedial box, on templates that use it).
     */
    public const ATTENTION_CHOICES = ['Fail', 'No', 'Not Working', 'Poor', 'Not Available', 'Bad', 'Average'];

    /**
     * Should this answer appear as a line item in the report?
     * False when the step was never answered or was explicitly marked N/A.
     */
    public static function isReportable($detail): bool
    {
        if (! self::detailIsAnswered($detail)) {
            return false;
        }

        return ! in_array($detail->choice, self::NA_CHOICES, true);
    }

    /**
     * pass | fail | na for a saved answer, recognising the checklist's choice vocab
     * (Working/Not Working, Pass/Fail, Yes/No, …) or a 1–5 rating.
     */
    public static function choiceState($detail): string
    {
        if (! $detail) {
            return 'na';
        }

        $choice = $detail->choice;
        $rating = $detail->rating;

        if (in_array($choice, self::POSITIVE_CHOICES, true) || ($rating !== null && $rating >= 3)) {
            return 'pass';
        }
        if (in_array($choice, self::NEGATIVE_CHOICES, true) || ($rating !== null && $rating < 3)) {
            return 'fail';
        }

        return 'na';
    }

    /**
     * Dynamic 1–5 rating for a section, derived from the share of "pass" answers
     * among its answered questions. A manual per-section rating overrides it.
     *
     * @param  \Illuminate\Support\Collection  $byStep  answers keyed by inspection_step_id
     */
    public static function sectionRating($section, $byStep, float|int|string|null $manual = null): ?float
    {
        // A rating the technician actually recorded wins, and keeps its decimal
        // place (4.6 stays 4.6). Only the derived fallback below is whole.
        if (filled($manual) && (float) $manual > 0) {
            return round(max(0.5, min(5, (float) $manual)), 1);
        }

        $answered = 0;
        $pass = 0;
        foreach ($section->steps as $step) {
            $detail = $byStep->get($step->id);
            if (! self::detailIsAnswered($detail)) {
                continue;
            }
            $answered++;
            if (self::choiceState($detail) === 'pass') {
                $pass++;
            }
        }

        if ($answered === 0) {
            return null;
        }

        return max(1, (int) round($pass / $answered * 5));
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
     * Create (or re-point) the lead's active inspection when a technician is
     * assigned. One ACTIVE inspection per lead: if one already exists the
     * technician is updated instead of adding a second row.
     *
     * Cancelled inspections are never touched — they are kept as history so the
     * cancellation reason and date stay on record. Assigning a lead whose last
     * inspection was cancelled therefore creates a brand-new inspection.
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

        // The lead's active inspection. Cancelled rows are skipped so they survive
        // untouched as history — when the last one was cancelled this comes back
        // null and a fresh inspection is created below.
        $inspection = static::where('lead_id', $leadId)
            ->where('status', '!=', static::STATUS_CANCELLED)
            ->latest('id')
            ->first();

        // A completed inspection is locked — never (re)assign or notify. This is the
        // single source of truth guarding every assign entry point. "Completed" =
        // the inspection status OR the lead's "Inspection Completed" label.
        $leadCompleted = ($lead->lead_assigned_status ?? null) === 'Inspection Completed';
        if (($inspection && $inspection->status === static::STATUS_COMPLETED) || $leadCompleted) {
            return $inspection;
        }

        // Fetch the basic registration record — needed in both the new-inspection
        // path (to snapshot customer details) and the reassign path (to fill any
        // fields that were empty on the first assignment).
        $basicReg = DB::table('tbl_basic_registration')->where('breg_id', $lead->lead_reg_id)->first();

        $isNew = ! $inspection;
        $technicianChanged = false;

        if ($inspection) {
            // Re-assign: point the existing inspection at the new technician/template.
            $technicianChanged = (int) $inspection->technician_id !== (int) $technicianId;

            $inspection->technician_id = $technicianId;
            $inspection->inspection_type_id = $typeId;

            $previousSchedule = $inspection->scheduled_at;
            if ($scheduledAt) {
                $inspection->scheduled_at = $scheduledAt;
            }
            // A moved slot for the SAME technician gets its own notification — the
            // reassignment message below only fires when the technician changes.
            $scheduleMoved = ! $technicianChanged
                && optional($inspection->scheduled_at)->format('Y-m-d H:i') !== optional($previousSchedule)->format('Y-m-d H:i');
            // Fill in customer/vehicle fields that may still be empty from
            // an earlier assignment when the lead didn't have them yet.
            if (! $inspection->customer_name_ar && ($basicReg?->breg_fname_ar ?? null)) {
                $inspection->customer_name_ar = $basicReg?->breg_fname_ar;
            }
            if (! $inspection->whatsapp_number && ($basicReg?->breg_whatsapp ?? null)) {
                $inspection->whatsapp_number = $basicReg?->breg_whatsapp;
            }
            if (! $inspection->car_make && ($lead->lead_make ?? null)) {
                $inspection->car_make = static::resolveName('tbl_make', 'make_id', 'make_name', $lead->lead_make);
            }
            if (! $inspection->car_model && ($lead->lead_model ?? null)) {
                $inspection->car_model = static::resolveName('tbl_model', 'model_id', 'model_name', $lead->lead_model);
            }
            // Model year from make_model_year (the free-text combined field).
            if (! $inspection->car_year) {
                $inspection->car_year = static::extractYearFromMakeModel($lead->make_model_year ?? null);
            }
            // Manufacturing year from the dedicated lead_year field.
            if (! $inspection->manufacturing_year) {
                $inspection->manufacturing_year = static::resolveLeadYear($lead->lead_year ?? null);
            }
            if (! $inspection->plate_no && ($lead->lead_vehicle_plate_no ?? null)) {
                $inspection->plate_no = $lead->lead_vehicle_plate_no;
            }
            if (! $inspection->exterior_color && ($lead->lead_color ?? null)) {
                $inspection->exterior_color = $lead->lead_color;
            }
            $inspection->save();
        } else {
            $branchId = $lead->lead_branch_id ?: (session('application_branch') ?: 1);

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
                'customer_name_ar'   => $basicReg?->breg_fname_ar,
                'customer_email'     => $basicReg?->breg_email,
                'customer_phone'     => ($basicReg?->breg_mob) ?: ($lead->lead_seller_mobile ?? null),
                'whatsapp_number'    => $basicReg?->breg_whatsapp,
                'car_make'           => $carMake,
                'car_model'          => $carModel,
                // Model year from make_model_year (the free-text combined field).
                'car_year'           => static::extractYearFromMakeModel($lead->make_model_year ?? null),
                // Manufacturing year from the dedicated lead_year field.
                'manufacturing_year' => static::resolveLeadYear($lead->lead_year ?? null),
                'plate_no'           => $lead->lead_vehicle_plate_no,
                'exterior_color'     => $lead->lead_color,
                'status'             => static::STATUS_PENDING,
                'scheduled_at'       => $scheduledAt ?: null,
            ]);
        }

        if (! empty($scheduleMoved)) {
            $inspection->notifyRescheduled($previousSchedule, auth()->id());
        }

        // Push-notify the assigned technician on a new assignment, or on a
        // reassignment that actually changed the technician. A no-op re-save
        // (same technician) does not notify. Failures are logged to the fcm
        // channel so assignment never breaks.
        if ($isNew || $technicianChanged) {
            $vehicle = trim(($inspection->car_make ?? '').' '.($inspection->car_model ?? '')) ?: 'Vehicle inspection';
            $title = $isNew ? 'New Inspection Assigned' : 'Inspection Reassigned to You';
            $body  = ($isNew ? 'You have a new inspection: ' : 'An inspection was reassigned to you: ') . $vehicle;
            $type  = $isNew ? 'inspection_assigned' : 'inspection_reassigned';

            // FCM push — reaches the technician's device even when the app is closed.
            \App\Services\PushNotificationService::sendToUser($technicianId, $title, $body, [
                'type' => $type,
                'inspection_id' => (string) $inspection->id,
                'lead_id' => (string) $leadId,
            ]);

            // Pusher broadcast — live in-app update while the app is open.
            \App\Events\InspectionAssigned::dispatch(
                $technicianId, (int) $inspection->id, (int) $leadId, $title, $body, $type
            );
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
     * Resolve a lookup id (or comma-separated ids — the model field stores
     * multiple selections) to its name(s).
     *
     * - A single numeric id (e.g. "5") is looked up and the name returned.
     * - A comma-separated list (e.g. "3,5,8") resolves each id individually
     *   and returns the names joined by ", ".
     * - A non-numeric value is assumed to already be a name and is passed
     *   through unchanged.
     * - Returns null when the value is empty or none of the ids resolve.
     */
    protected static function resolveName(string $table, string $idColumn, string $nameColumn, $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Already a name (non-numeric without commas) — pass through.
        if (! is_numeric($value) && ! str_contains($value, ',')) {
            return (string) $value;
        }

        // Comma-separated ids: resolve each one and join the names.
        if (str_contains($value, ',')) {
            $ids = array_filter(array_map('trim', explode(',', $value)));
            $names = [];
            foreach ($ids as $id) {
                if (is_numeric($id)) {
                    $name = DB::table($table)->where($idColumn, $id)->value($nameColumn);
                    if ($name !== null && $name !== '') {
                        $names[] = (string) $name;
                    }
                }
            }
            return ! empty($names) ? implode(', ', $names) : null;
        }

        // Single numeric id.
        $name = DB::table($table)->where($idColumn, $value)->value($nameColumn);

        return $name !== null && $name !== '' ? (string) $name : null;
    }

    /**
     * Resolve the manufacturing year from the lead's dedicated year field.
     *
     * @param  mixed  $leadYear  tbl_lead.lead_year
     * @return int|null
     */
    protected static function resolveLeadYear(mixed $leadYear): ?int
    {
        if (filled($leadYear) && is_numeric($leadYear)) {
            $y = (int) $leadYear;
            if ($y >= 1950 && $y <= 2099) {
                return $y;
            }
        }

        return null;
    }

    /**
     * Extract a 4-digit model year from the free-text make_model_year field
     * (e.g. "2020" from "Toyota Camry 2020").
     *
     * @param  string|null  $makeModelYear  tbl_lead.make_model_year
     * @return int|null
     */
    protected static function extractYearFromMakeModel(?string $makeModelYear): ?int
    {
        if (empty($makeModelYear)) {
            return null;
        }

        if (preg_match('/\b(\d{4})\b/', $makeModelYear, $m)) {
            $y = (int) $m[1];
            if ($y >= 1950 && $y <= 2099) {
                return $y;
            }
        }

        return null;
    }

    public function statusColor(): string
    {
        return [
            self::STATUS_PENDING => 'bg-gray-100 text-gray-700',
            self::STATUS_IN_PROGRESS => 'bg-amber-100 text-amber-700',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-700',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-700',
        ][$this->status] ?? 'bg-gray-100 text-gray-700';
    }

    /**
     * Public URL of the primary vehicle photo, or null when none is set.
     * Built against the current request host, same as InspectionMedia::url.
     */
    public function vehicleImageUrl(): ?string
    {
        return $this->vehicle_image
            ? url('storage/'.ltrim($this->vehicle_image, '/'))
            : null;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * An inspection can be cancelled while the job is still open — pending or in
     * progress. A completed one is locked, and an already-cancelled one is a
     * permanent record.
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS], true);
    }

    /**
     * Notify the assigned technician that this inspection was cancelled — stores
     * an app_notifications row and sends the FCM push.
     *
     * No self-notification: a technician who cancels their own job already knows.
     *
     * @param  int|null  $actorId  the user who cancelled
     */
    public function notifyCancelled(?int $actorId = null): void
    {
        $this->notifyTechnician(
            $actorId,
            'Inspection Cancelled',
            'Your inspection for '.$this->notificationVehicle().' was cancelled.'
                .($this->cancel_reason ? ' Reason: '.$this->cancel_reason : ''),
            'inspection_cancelled',
            array_filter([
                'cancel_reason' => $this->cancel_reason,
                'cancelled_at' => optional($this->cancelled_at)->toIso8601String(),
            ])
        );
    }

    /**
     * Notify the assigned technician that the schedule moved. Call AFTER saving,
     * passing the previous value so the message can show both.
     *
     * @param  mixed  $previous  the scheduled_at value before the change
     * @param  int|null  $actorId  the user who rescheduled
     */
    public function notifyRescheduled($previous = null, ?int $actorId = null): void
    {
        $fmt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('d M Y, h:i A') : null;

        $from = $fmt($previous);
        $to = $fmt($this->scheduled_at);

        // Nothing useful to say without a new slot.
        if (! $to) {
            return;
        }

        $this->notifyTechnician(
            $actorId,
            'Inspection Rescheduled',
            'Your inspection for '.$this->notificationVehicle().' is now on '.$to.'.'
                .($from ? ' (was '.$from.')' : ''),
            'inspection_rescheduled',
            array_filter([
                'scheduled_at' => optional($this->scheduled_at)->toIso8601String(),
                'previous_scheduled_at' => $previous ? \Illuminate\Support\Carbon::parse($previous)->toIso8601String() : null,
            ])
        );
    }

    /**
     * Notify a technician that this inspection is now theirs — stored
     * notification + FCM push + Pusher broadcast, the same trio the lead-assign
     * flow sends. Call AFTER saving the new technician_id.
     *
     * @param  int|null  $actorId  the user who reassigned it
     */
    public function notifyReassigned(?int $actorId = null): void
    {
        $technicianId = (int) $this->technician_id;

        if ($technicianId <= 0 || $technicianId === (int) $actorId) {
            return;
        }

        $title = 'Inspection Reassigned to You';
        $body = 'An inspection was reassigned to you: '.($this->customer_name ?: $this->notificationVehicle());

        \App\Services\PushNotificationService::sendToUser($technicianId, $title, $body, [
            'type' => 'inspection_reassigned',
            'inspection_id' => (string) $this->id,
            'lead_id' => (string) $this->lead_id,
        ]);

        \App\Events\InspectionAssigned::dispatch(
            $technicianId, (int) $this->id, (int) $this->lead_id, $title, $body, 'inspection_reassigned'
        );
    }

    /**
     * Shared delivery for the notifications above: stored in app_notifications and
     * pushed via FCM. Silently skipped when there is no technician, or when the
     * technician is the one who made the change.
     */
    private function notifyTechnician(?int $actorId, string $title, string $body, string $type, array $data = []): void
    {
        $technicianId = (int) $this->technician_id;

        if ($technicianId <= 0 || $technicianId === (int) $actorId) {
            return;
        }

        \App\Services\PushNotificationService::sendToUser($technicianId, $title, $body, array_merge([
            'type' => $type,
            'inspection_id' => (string) $this->id,
            'lead_id' => (string) $this->lead_id,
        ], $data));
    }

    /**
     * Vehicle description used in notification text.
     */
    private function notificationVehicle(): string
    {
        return trim(($this->car_make ?? '').' '.($this->car_model ?? '')) ?: 'a vehicle';
    }

    /**
     * Who may cancel: admins only. The assigned technician is told about it
     * (see notifyCancelled) but cannot cancel their own job. Single source of
     * truth for both the web and API cancel endpoints.
     */
    public function canBeCancelledBy(?User $user): bool
    {
        if (! $user || ! $this->isCancellable()) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * The admin who cancelled this inspection.
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
