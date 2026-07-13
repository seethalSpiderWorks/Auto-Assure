@extends('layouts.myfudapp')
@section('content')

@php
    $name = $data->breg_fname ?: ($data->lead_seller_name ?: 'Lead');
    $mobile = trim(($data->breg_mob_code ? $data->breg_mob_code.' ' : '').($data->breg_mob ?: $data->lead_seller_mobile));
    $initials = collect(explode(' ', trim($name)))->filter()->take(2)->map(fn($p)=>mb_strtoupper(mb_substr($p,0,1)))->implode('') ?: '?';
    $status = $data->lead_assigned_status ?: '—';
    $vehicle = trim(($data->lead_year ? $data->lead_year.' ' : '').($data->lead_make ?? '').' '.($data->lead_model ?? ''));

    $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : null;
    $clean = fn($arr) => array_filter($arr, fn($v)=>$v!==null && trim((string)$v)!=='' && $v!=='0000-00-00');

    $contact = $clean([
        'Full Name'      => $data->breg_fname,
        'Name (Arabic)'  => $data->breg_fname_ar,
        'Mobile'         => $mobile,
        'WhatsApp'       => $data->breg_whatsapp,
        'Alt. Mobile'    => $data->lead_your_mobile,
        'Email'          => $data->breg_email,
        'Place'          => $data->breg_place,
        'District'       => $data->district_name,
        'State'          => $data->state_name,
        'Qualification'  => $data->breg_qualification,
    ]);

    $enquiry = $clean([
        'Make'           => $data->lead_make,
        'Model'          => $data->lead_model,
        'Year'           => $data->lead_year,
        'Make/Model Year'=> $data->make_model_year,
        'Year From'      => $data->lead_year_from,
        'Year To'        => $data->lead_year_to,
        'Colour'         => $data->lead_color,
        'Colour (Arabic)'=> $data->lead_color_ar,
        'Plate No'       => $data->lead_vehicle_plate_no,
        'Budget'         => $data->lead_budget,
        'Location'       => $data->lead_location,
        'Source'         => $data->source_name ?: $data->lead_source_name,
        'Form Type'      => $data->lead_form_type,
    ]);

    $seller = $clean([
        'Seller Name'         => $data->lead_seller_name,
        'Seller Name (Arabic)'=> $data->lead_seller_name_ar,
        'Seller Mobile'       => $data->lead_seller_mobile,
    ]);

    // Resolve the numeric enquiry-status code to its readable label.
    $enqStatusLabel = ($data->lead_enq_status !== null && $data->lead_enq_status !== '')
        ? optional(\DB::table('tbl_followup_type')->where('followup_type_id', $data->lead_enq_status)->first())->followup_type_name
        : null;

    $meta = $clean([
        'Reference'       => $data->lead_unq_id,
        'Lead Date'       => $fmtDate($data->lead_date),
        'Created On'      => $data->lead_datetime ?: trim(($fmtDate($data->lead_date_on) ?? '').' '.($data->lead_time_on ?? '')),
        'Registered On'   => $fmtDate($data->breg_date),
        'Current Status'  => $data->lead_assigned_status,
        'Enquiry Status'  => $enqStatusLabel,
        'Assigned Staff'  => optional($assigned_staff)->name,
        'Added By'        => $data->added_by_name,
        'Branch'          => $data->branch_name,
    ]);
@endphp

<div class="page-content ldv-page">
    <div class="container-fluid">

        {{-- Hero --}}
        <div class="ldv-hero">
            <div class="ldv-hero__left">
                <span class="ldv-avatar">{{ $initials }}</span>
                <div>
                    <div class="ldv-hero__eyebrow">Lead {{ $data->lead_unq_id ? '· '.$data->lead_unq_id : '' }}</div>
                    <h4 class="ldv-hero__title">{{ $name }}</h4>
                    <div class="ldv-hero__meta">
                        <span class="ldv-status">{{ $status }}</span>
                        @if($mobile)<span><i class="bx bx-phone"></i> {{ $mobile }}</span>@endif
                        @if($data->lead_date)<span><i class="bx bx-calendar"></i> {{ \Carbon\Carbon::parse($data->lead_date)->format('d M Y') }}</span>@endif
                    </div>
                </div>
            </div>
            <div class="ldv-hero__actions">
                <a href="{{ url('leads?id='.$data->lead_id) }}" target="_blank" class="btn btn-light btn-sm"><i class="bx bx-edit"></i> Edit</a>
                @if($inspection)<a href="{{ url('inspections/'.$inspection->id.'/details') }}" class="btn btn-light btn-sm"><i class="bx bx-clipboard"></i> Inspection</a>@endif
                <a href="{{ url()->previous() }}" class="btn btn-outline-light btn-sm">Back</a>
            </div>
        </div>

        {{-- Assignment option — assigns the lead & opens an inspection --}}
        <div class="ldv-card ldv-assign mb-3">
            <div class="ldv-assign__label"><i class="bx bx-user-plus"></i> Assign &amp; create inspection</div>
            @php
                $curTypeId = $inspection->inspection_type_id ?? null;
                $curSched  = ($inspection && $inspection->scheduled_at) ? \Carbon\Carbon::parse($inspection->scheduled_at)->format('Y-m-d') : '';
            @endphp
            <div class="ldv-assign__field">
                <label>Inspection Template</label>
                <select id="ldv_type" class="form-select">
                    <option value="" @selected(!$curTypeId) disabled>Select template</option>
                    @foreach($inspectionTypes as $tid => $tname)
                        <option value="{{ $tid }}" @selected($curTypeId == $tid)>{{ $tname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ldv-assign__field">
                <label>Scheduled Date</label>
                <input type="date" id="ldv_date" class="form-control" value="{{ $curSched }}">
            </div>
            <div class="ldv-assign__field">
                <label>Technician</label>
                <select id="ldv_staff" class="form-select">
                    <option value="">Select technician</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" @selected($data->lead_assigned_users == $tech->id)>{{ $tech->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" id="ldv_assign_btn" class="btn btn-brand"><i class="bx bx-check"></i> Assign</button>
            <span id="ldv_assign_msg" class="ldv-assign__msg"></span>
        </div>

        <div class="row g-3">
            {{-- Contact --}}
            <div class="col-lg-6">
                <div class="ldv-card h-100">
                    <div class="ldv-card__title"><i class="bx bx-user"></i> Contact Details</div>
                    <div class="ldv-facts">
                        @forelse($contact as $k => $v)
                            <div class="ldv-fact"><span class="ldv-fact__k">{{ $k }}</span><span class="ldv-fact__v">{{ $v }}</span></div>
                        @empty
                            <div class="text-muted">No contact details.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Enquiry / Vehicle --}}
            <div class="col-lg-6">
                <div class="ldv-card h-100">
                    <div class="ldv-card__title"><i class="bx bxs-car"></i> Enquiry &amp; Vehicle</div>
                    <div class="ldv-facts">
                        @forelse($enquiry as $k => $v)
                            <div class="ldv-fact"><span class="ldv-fact__k">{{ $k }}</span><span class="ldv-fact__v">{{ $v }}</span></div>
                        @empty
                            <div class="text-muted">No enquiry details.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Seller + Lead information --}}
        <div class="row g-3 mt-0">
            @if(count($seller))
                <div class="col-lg-6">
                    <div class="ldv-card h-100">
                        <div class="ldv-card__title"><i class="bx bx-store"></i> Seller</div>
                        <div class="ldv-facts">
                            @foreach($seller as $k => $v)
                                <div class="ldv-fact"><span class="ldv-fact__k">{{ $k }}</span><span class="ldv-fact__v">{{ $v }}</span></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-lg-{{ count($seller) ? 6 : 12 }}">
                <div class="ldv-card h-100">
                    <div class="ldv-card__title"><i class="bx bx-detail"></i> Lead Information</div>
                    <div class="ldv-facts">
                        @forelse($meta as $k => $v)
                            <div class="ldv-fact"><span class="ldv-fact__k">{{ $k }}</span><span class="ldv-fact__v">{{ $v }}</span></div>
                        @empty
                            <div class="text-muted">—</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($data->breg_message || $data->lead_add_details || $data->lead_remarks)
            <div class="ldv-card mt-3">
                <div class="ldv-card__title"><i class="bx bx-note"></i> Notes</div>
                <div class="ldv-notes">
                    @if($data->breg_message)<p><b>Message:</b> {{ $data->breg_message }}</p>@endif
                    @if($data->lead_add_details)<p><b>Additional Details:</b> {{ $data->lead_add_details }}</p>@endif
                    @if($data->lead_remarks)<p><b>Remarks:</b> {{ $data->lead_remarks }}</p>@endif
                </div>
            </div>
        @endif

        {{-- Followup history --}}
        <div class="ldv-card mt-3">
            <div class="ldv-card__title"><i class="bx bx-history"></i> Follow-up History <span class="ldv-chip">{{ $followups->count() }}</span></div>
            @if($followups->count())
                <div class="ldv-timeline">
                    @foreach($followups as $f)
                        <div class="ldv-tl">
                            <span class="ldv-tl__dot"></span>
                            <div class="ldv-tl__body">
                                <div class="ldv-tl__head">
                                    <span class="ldv-tl__status">{{ $f->followup_current_status ?: 'Update' }}</span>
                                    <span class="ldv-tl__date">{{ $f->followup_date ? \Carbon\Carbon::parse($f->followup_date)->format('d M Y') : (optional($f->created_at) ? \Carbon\Carbon::parse($f->created_at)->format('d M Y') : '') }}</span>
                                </div>
                                <div class="ldv-tl__meta">
                                    @if($f->staff_name)<span><i class="bx bx-user"></i> {{ $f->staff_name }}</span>@endif
                                    @if($f->next_followup_date)<span><i class="bx bx-calendar-check"></i> Next: {{ \Carbon\Carbon::parse($f->next_followup_date)->format('d M Y') }}</span>@endif
                                </div>
                                @if($f->followup_remarks)<div class="ldv-tl__note">{{ $f->followup_remarks }}</div>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted">No follow-ups recorded yet.</div>
            @endif
        </div>

        {{-- Notes --}}
        <div class="ldv-card mt-3">
            <div class="ldv-card__title"><i class="bx bx-note"></i> Notes <span class="ldv-chip">{{ $notes->count() }}</span></div>
            @if($notes->count())
                <div class="ldv-notelist">
                    @foreach($notes as $n)
                        <div class="ldv-note">
                            <div class="ldv-note__avatar">{{ strtoupper(substr($n->author ?: 'U',0,1)) }}</div>
                            <div class="ldv-note__body">
                                <div class="ldv-note__head">
                                    <span class="ldv-note__author">{{ $n->author ?: 'User' }}</span>
                                    <span class="ldv-note__date">{{ \Carbon\Carbon::parse($n->created_at)->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="ldv-note__text">{{ $n->note_text }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted">No notes added yet.</div>
            @endif
        </div>

    </div>
</div>

@endsection

@section('css')
<style>
    :root { --ld-dark:#00263D; --ld-brand:#04B084; }
    .ldv-page { background:
        radial-gradient(1200px 240px at 100% -60px, rgba(4,176,132,.10), transparent 60%),
        radial-gradient(900px 220px at -10% -40px, rgba(0,38,61,.08), transparent 55%); padding-bottom:40px; }

    .ldv-hero { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;
        background:linear-gradient(120deg,#00263D 0%,#06655a 60%,#04B084 130%); color:#fff; border-radius:20px;
        padding:22px 28px; margin-bottom:20px; box-shadow:0 14px 34px rgba(0,38,61,.22); }
    .ldv-hero__left { display:flex; align-items:center; gap:16px; }
    .ldv-avatar { width:58px; height:58px; border-radius:16px; background:rgba(255,255,255,.16); display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; }
    .ldv-hero__eyebrow { font-size:12px; opacity:.8; letter-spacing:.4px; text-transform:uppercase; }
    .ldv-hero__title { font-size:24px; font-weight:800; margin:2px 0 6px; color:#fff; }
    .ldv-hero__meta { display:flex; flex-wrap:wrap; gap:14px; font-size:13px; opacity:.92; align-items:center; }
    .ldv-status { background:rgba(255,255,255,.16); border-radius:20px; padding:3px 12px; font-weight:600; font-size:12px; }
    .ldv-hero__actions { display:flex; gap:8px; flex-wrap:wrap; }

    .ldv-card { background:#fff; border-radius:16px; box-shadow:0 6px 22px rgba(16,40,70,.07); padding:22px 24px; }
    .ldv-card__title { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--ld-dark); margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    .ldv-card__title i { font-size:17px; color:var(--ld-brand); }
    .ldv-chip { font-size:11.5px; font-weight:700; background:#e7f8ef; color:var(--ld-brand); border-radius:20px; padding:2px 10px; margin-left:4px; }

    .ldv-facts { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:14px 22px; }
    .ldv-fact { display:flex; flex-direction:column; }
    .ldv-fact__k { font-size:11.5px; text-transform:uppercase; letter-spacing:.3px; color:#98a2b3; margin-bottom:3px; }
    .ldv-fact__v { font-size:14.5px; font-weight:600; color:#344054; word-break:break-word; }
    .ldv-notes { margin-top:16px; padding-top:14px; border-top:1px solid #eef1f5; }
    .ldv-notes p { margin:0 0 8px; font-size:13.5px; color:#475467; line-height:1.5; }

    .ldv-timeline { position:relative; margin-left:6px; }
    .ldv-tl { position:relative; padding:0 0 18px 24px; border-left:2px solid #eef1f5; }
    .ldv-tl:last-child { border-left-color:transparent; padding-bottom:0; }
    .ldv-tl__dot { position:absolute; left:-7px; top:2px; width:12px; height:12px; border-radius:50%; background:var(--ld-brand); box-shadow:0 0 0 3px #e7f8ef; }
    .ldv-tl__head { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .ldv-tl__status { font-weight:700; color:var(--ld-dark); font-size:14px; }
    .ldv-tl__date { font-size:12px; color:#98a2b3; }
    .ldv-tl__meta { display:flex; gap:14px; flex-wrap:wrap; font-size:12.5px; color:#667085; margin-top:4px; }
    .ldv-tl__note { margin-top:6px; background:#f7f9fc; border-radius:10px; padding:8px 12px; font-size:13px; color:#475467; }

    /* Notes */
    .ldv-notelist { display:flex; flex-direction:column; gap:12px; }
    .ldv-note { display:flex; gap:12px; background:#f9fbfc; border:1px solid #eef1f5; border-radius:12px; padding:12px 14px; }
    .ldv-note__avatar { flex:0 0 auto; width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,var(--ld-dark),var(--ld-brand)); color:#fff; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .ldv-note__body { flex:1; min-width:0; }
    .ldv-note__head { display:flex; align-items:center; gap:10px; margin-bottom:4px; }
    .ldv-note__author { font-weight:700; color:#1f2a37; font-size:13.5px; }
    .ldv-note__date { font-size:12px; color:#98a2b3; }
    .ldv-note__text { font-size:13.5px; color:#475467; line-height:1.5; white-space:pre-wrap; word-break:break-word; }

    /* Assign option */
    .ldv-assign { display:flex; align-items:flex-end; gap:14px; flex-wrap:wrap; padding:18px 22px; }
    .ldv-assign__label { font-weight:700; color:var(--ld-dark); display:flex; align-items:center; gap:8px; flex:1 1 100%; }
    .ldv-assign__label i { color:var(--ld-brand); font-size:18px; }
    .ldv-assign__field { display:flex; flex-direction:column; gap:5px; flex:1 1 190px; min-width:160px; }
    .ldv-assign__field > label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#98a2b3; margin:0; }
    .ldv-assign .form-select, .ldv-assign .form-control { border:1px solid #e4e8ee; border-radius:10px; height:40px; }
    .ldv-assign .form-select:focus, .ldv-assign .form-control:focus { border-color:var(--ld-brand); box-shadow:0 0 0 .18rem rgba(4,176,132,.15); }
    .ldv-assign #ldv_assign_btn { height:40px; }
    .btn-brand { background:var(--ld-dark); border-color:var(--ld-dark); color:#fff; border-radius:10px; font-weight:600; }
    .btn-brand:hover { background:var(--ld-brand); border-color:var(--ld-brand); color:#fff; }
    .ldv-assign__msg { font-size:13px; font-weight:600; }
    .ldv-assign__msg.ok { color:var(--ld-brand); }
    .ldv-assign__msg.err { color:#e5484d; }

    @media (max-width:575px){ .ldv-facts { grid-template-columns:1fr; } }
</style>
@endsection

@section('js')
<script>
    document.getElementById('ldv_assign_btn').addEventListener('click', function () {
        var type  = document.getElementById('ldv_type').value;
        var date  = document.getElementById('ldv_date').value;
        var staff = document.getElementById('ldv_staff').value;
        var msg   = document.getElementById('ldv_assign_msg');

        if (!staff) { msg.className = 'ldv-assign__msg err'; msg.textContent = 'Please select a technician.'; return; }
        if (!type)  { msg.className = 'ldv-assign__msg err'; msg.textContent = 'Please select an inspection template.'; return; }

        var btn = this; btn.disabled = true;
        msg.className = 'ldv-assign__msg'; msg.textContent = 'Assigning…';

        var body = new URLSearchParams();
        body.append('leads[]', '{{ $data->lead_id }}');   // assignLeadAction iterates request.leads
        body.append('user_id', staff);
        body.append('inspection_type_id', type);
        body.append('scheduled_at', date);
        body.append('date', date);

        fetch('{{ url('leads/assign_leads') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'X-Requested-With': 'XMLHttpRequest' },
            body: body
        })
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
        .then(function () {
            msg.className = 'ldv-assign__msg ok';
            msg.textContent = '✓ Lead assigned & inspection created. Refreshing…';
            setTimeout(function () { window.location.reload(); }, 1100);
        })
        .catch(function () { msg.className = 'ldv-assign__msg err'; msg.textContent = '⚠ Assignment failed.'; btn.disabled = false; });
    });
</script>
@endsection
