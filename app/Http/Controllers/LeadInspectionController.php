<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionType;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Additive lead → inspection bridge. Assigns a lead to a technician,
 * updates the lead status, and opens a matching row in the `inspections`
 * table. Works against the legacy `tbl_lead` (via App\Models\Lead) and does
 * NOT touch the existing Leads module logic. Assignment is stored on the
 * `lead_assigned_users` column, as the CRM already does.
 *
 * Logic mirrors App\Http\Controllers\LeadController::assign() (which targets a
 * modern schema) adapted to the real CRM tables.
 */
class LeadInspectionController extends Controller
{
    /**
     * Assign (or reassign) a lead to a technician and open/refresh its inspection.
     */
    public function assign(Request $request, int $lead): RedirectResponse
    {
        $leadRow = Lead::findOrFail($lead);

        $validated = $request->validate([
            'assigned_to' => ['required', 'integer', 'exists:users,id'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $technician = User::findOrFail($validated['assigned_to']);

        if (! $technician->isTechnician()) {
            return back()->withErrors(['assigned_to' => 'Selected user is not a technician.']);
        }

        // --- Update the lead assignment (legacy columns only) ---
        $leadRow->lead_assigned_users = (string) $technician->id;
        $leadRow->lead_assigned_status = 'Inspection';
        $leadRow->save();

        // --- Ensure a matching inspection record exists for the technician ---
        $inspection = Inspection::firstOrNew(['lead_id' => $leadRow->lead_id]);

        $make = $leadRow->lead_make ? DB::table('tbl_make')->where('make_id', $leadRow->lead_make)->value('make_name') : null;
        $model = $leadRow->lead_model ? DB::table('tbl_model')->where('model_id', $leadRow->lead_model)->value('model_name') : null;

        $inspection->fill([
            'branch_id' => $inspection->branch_id ?: ($leadRow->lead_branch_id ?: (Inspection::query()->value('branch_id') ?? 1)),
            'technician_id' => $technician->id,
            'inspection_type_id' => $inspection->inspection_type_id ?: $this->defaultInspectionTypeId(),
            'scheduled_at' => $validated['scheduled_at'] ?? $inspection->scheduled_at,
            // Snapshot customer/vehicle from the lead (technician confirms/edits in the app).
            'customer_name' => $inspection->customer_name ?: ($leadRow->lead_seller_name ?: 'Customer'),
            'customer_phone' => $inspection->customer_phone ?: $leadRow->lead_seller_mobile,
            'car_make' => $inspection->car_make ?: $make,
            'car_model' => $inspection->car_model ?: $model,
            'car_year' => $inspection->car_year ?: $leadRow->lead_year,
        ]);
        $inspection->status ??= Inspection::STATUS_PENDING;
        $inspection->save();

        return back()->with('success', "Assigned to {$technician->name}. Inspection ready.");
    }

    /**
     * Update the lead's status (stored on lead_assigned_status).
     */
    public function updateStatus(Request $request, int $lead): RedirectResponse
    {
        $leadRow = Lead::findOrFail($lead);

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:100'],
        ]);

        $leadRow->lead_assigned_status = $validated['status'];
        $leadRow->save();

        return back()->with('success', 'Status updated.');
    }

    private function defaultInspectionTypeId(): int
    {
        return InspectionType::where('is_active', true)->orderBy('sequence')->value('id')
            ?? InspectionType::orderBy('id')->value('id')
            ?? abort(422, 'No inspection type configured. Create one under Inspection Templates first.');
    }
}
