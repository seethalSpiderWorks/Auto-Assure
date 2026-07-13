@php
    // $lead is the lead row passed from the popup ($data). Resolve everything by lead_id
    // so the Leads module controller needs no changes.
    $aaLeadId = $lead->lead_id;
    $aaLead = \App\Models\Lead::find($aaLeadId);
    $aaInspection = \App\Models\Inspection::with('technician')->where('lead_id', $aaLeadId)->first();
    $aaTechnicians = \App\Models\User::where('previlage', \App\Models\User::TECHNICIAN_PRIVILEGE)
        ->where('status', 0)->orderBy('name')->get();
    $aaAssignedId = $aaLead?->lead_assigned_users;
    $aaAssigned = ($aaAssignedId && is_numeric($aaAssignedId))
        ? \App\Models\User::find($aaAssignedId) : null;
    $aaStatuses = ['New', 'Assign', 'Reassign', 'Followup', 'Plan / Shedule', 'Reshedule', 'Inspection', 'Inspection Completed', 'Approved', 'Rejected', 'Closed'];
    $aaStatusColor = [
        'Inspection' => 'badge-soft-warning', 'Inspection Completed' => 'badge-soft-primary',
        'Assign' => 'badge-soft-success', 'Reassign' => 'badge-soft-success', 'Approved' => 'badge-soft-success',
        'Rejected' => 'badge-soft-danger', 'Closed' => 'badge-soft-dark', 'New' => 'badge-soft-danger',
    ];
@endphp

<div class="card border-top" style="border-top:3px solid #556ee6 !important;">
    <div class="card-body">
        <h4 class="card-title mb-3"><b>Inspection Assignment</b></h4>

        @include('partials._notify')

        <div class="row">
            {{-- Assignment --}}
            <div class="col-md-6 border-right">
                <h5 class="font-size-14 text-muted mb-3">Assign Technician</h5>

                @if ($aaAssigned)
                    <div class="d-flex align-items-center mb-3" style="gap:.75rem;">
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                {{ strtoupper(substr($aaAssigned->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <div class="font-weight-bold text-dark">{{ trim($aaAssigned->name . ' ' . $aaAssigned->lname) }}</div>
                            <div class="text-muted font-size-12">Currently assigned</div>
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-3">Not assigned to a technician yet.</p>
                @endif

                <form method="POST" action="{{ route('lead.inspection.assign', $aaLeadId) }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label class="form-label font-size-12">Technician <span class="text-danger">*</span></label>
                        <select name="assigned_to" class="form-control" required>
                            <option value="">Select technician…</option>
                            @foreach ($aaTechnicians as $tech)
                                <option value="{{ $tech->id }}" @selected($aaAssignedId == $tech->id)>{{ trim($tech->name . ' ' . $tech->lname) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label font-size-12">Schedule (optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control"
                               value="{{ optional($aaInspection?->scheduled_at)->format('Y-m-d\TH:i') }}">
                    </div>
                    <button class="btn btn-primary btn-sm">{{ $aaAssigned ? 'Reassign' : 'Assign' }} &amp; Create Inspection</button>
                    @if ($aaTechnicians->isEmpty())
                        <p class="text-warning font-size-12 mb-0 mt-2">No technicians (previlage 49) found.</p>
                    @endif
                </form>
            </div>

            {{-- Status + Inspection --}}
            <div class="col-md-6">
                <h5 class="font-size-14 text-muted mb-3">Update Status</h5>
                <form method="POST" action="{{ route('lead.inspection.status', $aaLeadId) }}" class="form-inline mb-4" style="gap:.5rem;">
                    @csrf
                    <select name="status" class="form-control">
                        @foreach ($aaStatuses as $s)
                            <option value="{{ $s }}" @selected(($aaLead?->lead_assigned_status) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-dark btn-sm">Set</button>
                </form>

                <h5 class="font-size-14 text-muted mb-2">Inspection</h5>
                @if ($aaInspection)
                    <p class="mb-1">
                        <span class="badge {{ $aaStatusColor[ucfirst(str_replace('_',' ',$aaInspection->status))] ?? 'badge-soft-secondary' }} font-size-12">
                            {{ ucfirst(str_replace('_', ' ', $aaInspection->status)) }}
                        </span>
                        @if ($aaInspection->technician)
                            <span class="text-muted font-size-12 ml-1">· {{ $aaInspection->technician->name }}</span>
                        @endif
                    </p>
                    @if ($aaInspection->scheduled_at)
                        <p class="text-muted font-size-12 mb-2">Scheduled: {{ $aaInspection->scheduled_at->format('d M Y, H:i') }}</p>
                    @endif
                    <a href="{{ route('inspections.edit', $aaInspection) }}" class="btn btn-soft-primary btn-sm">Open inspection report</a>
                @else
                    <p class="text-muted font-size-12 mb-0">No inspection yet — assign a technician to create one.</p>
                @endif
            </div>
        </div>
    </div>
</div>
