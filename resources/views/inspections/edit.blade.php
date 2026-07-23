@extends('layouts.myfudapp')
@section('content')

<style>
    /* ---- Brand palette (from logo.svg) ----------------------------------- */
    :root {
        --brand-dark: #00263D;   /* logo navy   */
        --brand:      #04B084;   /* logo green  */
        --brand-2:    #17BC8D;
    }

    /* ---- Wizard shell ---------------------------------------------------- */
    #inspection-root { padding-bottom: 104px; }        /* clear the fixed nav */

    .wiz-head { position: sticky; top: 0; z-index: 900; background: #fff; padding: 1rem 1.25rem .55rem; margin-bottom: 1rem; border-radius: 0 0 16px 16px; box-shadow: 0 4px 16px rgba(16,40,70,.06); }
    .wiz-head h4 { color: var(--brand-dark); font-weight: 700; }

    /* Horizontal step indicator — scrolls sideways when there are many steps */
    .wiz-steps {
        display: flex; flex-wrap: nowrap; align-items: center; gap: 0;
        overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch;
        padding: .5rem .25rem .55rem; scroll-behavior: smooth;
    }
    .wiz-steps::-webkit-scrollbar { height: 6px; }
    .wiz-steps::-webkit-scrollbar-thumb { background: #c7ccd6; border-radius: 3px; }
    .wiz-dot { display: flex; flex-direction: column; align-items: center; cursor: pointer; flex: 0 0 auto; }
    .wiz-dot .bead {
        width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        background: #e9edf1; color: #7a8593; font-weight: 700; font-size: .82rem; border: 2px solid #e9edf1; transition: all .18s;
    }
    .wiz-dot .lbl { font-size: .68rem; color: #98a2b3; margin-top: .25rem; max-width: 84px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .wiz-dot.active .bead { background: var(--brand-dark); border-color: var(--brand-dark); color: #fff; box-shadow: 0 0 0 4px rgba(4,176,132,.22); }
    .wiz-dot.active .lbl { color: var(--brand-dark); font-weight: 600; }
    .wiz-dot.done .bead { background: var(--brand); border-color: var(--brand); color: #fff; }
    .wiz-line { flex: 0 0 20px; width: 20px; height: 2px; background: #e5e7eb; }
    .wiz-line.done { background: var(--brand); }

    /* ---- Question rows (compact list, not individual cards) --------------- */
    .q-card {
        border: 0; border-bottom: 1px solid #eef1f5; border-radius: 0; background: transparent;
        padding: .7rem 0 .8rem; margin-bottom: 0; box-shadow: none; transition: background .12s;
    }
    .q-card:last-child { border-bottom: 0; padding-bottom: .2rem; }
    .q-card:hover { background: #fafcff; }
    .q-title { font-weight: 600; font-size: .95rem; color: #1f2a37; margin-bottom: .5rem; }

    /* Inline question row: question text on the left, answer control on the right */
    .q-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .q-titlewrap { flex: 1 1 240px; min-width: 0; }
    .q-head .q-title { margin-bottom: 0; }
    /* Rating and choice radios sit side by side here. Without a gap they collide
       when a question enables both; wrap keeps them readable on narrow screens. */
    .q-answer { flex: 0 0 auto; display: flex; align-items: center; flex-wrap: wrap; gap: .35rem 1.25rem; justify-content: flex-end; }
    /* The rating block carries mb-2 for the stacked layout — cancel it inline. */
    .q-answer > [data-rating] { margin-bottom: 0 !important; }
    /* Thin divider between the stars and the options when both are shown. */
    .q-answer > [data-rating] + .choice-radios { border-left: 1px solid #e4e8ee; padding-left: 1.25rem; }
    .observation-wrap, .remedial-wrap { margin-top: .5rem; }

    /* Choice options as radio buttons */
    .choice-radios { display: flex; flex-wrap: wrap; gap: .3rem 1.1rem; margin-bottom: 0; }
    .choice-radio { display: inline-flex; align-items: center; margin: 0; padding: .15rem 0; }
    .choice-radio .form-check-input { width: 1.1em; height: 1.1em; margin: 0 .4rem 0 0; cursor: pointer; }
    .choice-radio .form-check-label { font-weight: 600; cursor: pointer; font-size: .88rem; margin: 0; }
    .form-check-input:checked { background-color: var(--brand); border-color: var(--brand); }
    .form-check-input:focus { border-color: var(--brand); box-shadow: 0 0 0 .2rem rgba(4,176,132,.18); }
    .form-control:focus, .form-select:focus { border-color: var(--brand); box-shadow: 0 0 0 .18rem rgba(4,176,132,.15); }

    /* Star rating */
    .js-star, .js-secstar { cursor: pointer; font-size: 1.35rem; line-height: 1; }

    /* Dashed upload area */
    .upload-drop {
        display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%;
        min-height: 46px; border: 1.5px dashed #c7ccd6; border-radius: 10px; color: #6b7280; cursor: pointer;
        background: #fbfcfe; font-weight: 500; font-size: .85rem; transition: all .12s; margin: 0;
    }
    .upload-drop:hover { border-color: var(--brand); color: var(--brand); background: #f2fbf8; }
    .upload-drop i { font-size: 1.15rem; }
    .q-card .observation-wrap textarea, .q-card .remedial-wrap textarea { font-size: .88rem; }

    /* Details cards */
    .detail-card { border: 0; border-radius: 14px; box-shadow: 0 4px 18px rgba(16,40,70,.06); overflow: hidden; margin-bottom: .9rem; }
    .detail-card .card-header { display: flex; align-items: center; gap: .7rem; padding: .6rem 1.1rem; background: linear-gradient(90deg, #eef9f5 0%, #f7f9fc 60%); border-bottom: 1px solid #eef1f5; }
    .detail-card .card-header h5 { color: var(--brand-dark); font-weight: 700; font-size: 1rem; }
    .detail-ico { width: 34px; height: 34px; border-radius: 9px; flex: 0 0 auto; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, var(--brand-dark), var(--brand)); color: #fff; font-size: 18px; box-shadow: 0 4px 12px rgba(4,176,132,.28); }
    .detail-card .card-body { padding: .8rem 1.1rem .05rem; }
    .detail-group-title { display: flex; align-items: center; gap: .5rem; font-size: .72rem; letter-spacing: .05em; text-transform: uppercase; color: var(--brand-dark); font-weight: 700; margin: .15rem 0 .5rem; }
    .detail-group-title::before { content: ''; width: 16px; height: 3px; border-radius: 3px; background: var(--brand); }
    .detail-card .form-label { font-size: .8rem; font-weight: 600; color: #5b6472; margin-bottom: .3rem; }
    .detail-card .form-control, .detail-card .form-select, .detail-card select.form-control {
        /* background-color, NOT the `background` shorthand — the shorthand would reset
           background-image and wipe out the .form-select dropdown chevron. */
        border: 1px solid #e4e8ee; border-radius: 10px; padding: .58rem .85rem; font-size: .92rem; background-color: #fff; transition: border-color .12s, box-shadow .12s; }
    .detail-card .form-control:hover, .detail-card select.form-control:hover { border-color: #cfd6df; }
    /* Leave room on the right for the chevron and sit it inside the rounded border. */
    .detail-card select.form-select { padding-right: 2.1rem; background-position: right .8rem center; background-size: 13px 10px; cursor: pointer; }
    .detail-card .row + .detail-group-title { margin-top: .5rem; padding-top: .55rem; border-top: 1px dashed #eef1f5; }

    /* Compact section / verdict cards + field spacing */
    #inspection-root .mb-3 { margin-bottom: .55rem !important; }
    .wizard-step .card:not(.detail-card) { border-radius: 14px; margin-bottom: .9rem; }
    .wizard-step .card:not(.detail-card) > .card-header { padding: .55rem 1.1rem; }
    .wizard-step .card:not(.detail-card) > .card-body { padding: .85rem 1.1rem .3rem; }
    .wizard-step .card-title { margin-bottom: .6rem !important; font-size: 1rem; color: var(--brand-dark); font-weight: 700; }
    .detail-sep { margin: .35rem 0 .55rem; border: 0; border-top: 1px dashed #e2e6ec; }

    /* ---- Fixed bottom navigation ---------------------------------------- */
    .wiz-nav {
        position: fixed; left: 250px; right: 0; bottom: 0; z-index: 1000; background: #fff;
        border-top: 1px solid #e5e7eb; box-shadow: 0 -3px 12px rgba(0,0,0,.08);
        padding: .7rem 1.5rem; display: flex; align-items: center; gap: .75rem;
    }
    body[data-sidebar-size="sm"] .wiz-nav { left: 70px; }
    body[data-sidebar-size="small"] .wiz-nav { left: 160px; }
    @media (max-width: 991.98px) { .wiz-nav { left: 0; } }
    .wiz-nav .btn { min-height: 46px; border-radius: 10px; }
    .wiz-nav .btn-next, .wiz-nav .btn-complete { min-width: 150px; font-weight: 600; }
    .wiz-spacer { flex: 1 1 auto; }

    /* Themed buttons (scoped to the wizard) */
    #inspection-root .btn-primary, .wiz-nav .btn-primary { background: var(--brand-dark); border-color: var(--brand-dark); }
    #inspection-root .btn-primary:hover, .wiz-nav .btn-primary:hover { background: var(--brand); border-color: var(--brand); }
    .wiz-nav .btn-success, #inspection-root .btn-success { background: var(--brand); border-color: var(--brand); }
    .wiz-nav .btn-success:hover, #inspection-root .btn-success:hover { background: var(--brand-2); border-color: var(--brand-2); }
    #inspection-root a.text-success, #inspection-root .text-success { color: var(--brand) !important; }

    /* Additional-media items with per-file labels */
    .extra-item { width: 116px; display: flex; flex-direction: column; gap: 5px; }
    .extra-item__thumb { position: relative; width: 100%; height: 78px; }
    .extra-item__thumb img, .extra-item__thumb video { width: 100%; height: 100%; object-fit: cover; border-radius: .5rem; border: 1px solid #e6e9ef; background: #000; }
    .extra-item__label { font-size: 11.5px; padding: 4px 8px; border-radius: 8px; border: 1px solid #e4e8ee; }
    .extra-item__label:focus { border-color: var(--brand); box-shadow: 0 0 0 .15rem rgba(4,176,132,.15); }

    /* Header accents */
    .wiz-head .progress-bar.bg-success { background-color: var(--brand) !important; }
    .wiz-head .text-primary { color: var(--brand-dark) !important; }
    .wiz-head .btn-info { background: var(--brand-2); border-color: var(--brand-2); color: #fff; }
    .wiz-head .btn-info:hover { background: var(--brand); border-color: var(--brand); }
    .wiz-head .btn-primary { background: var(--brand-dark); border-color: var(--brand-dark); }
    .wiz-head .btn-primary:hover { background: var(--brand); border-color: var(--brand); }
</style>

@php
    $isCompleted = $inspection->status === 'completed';
    $prog = $inspection->progress();
    $statusBadge = [
        'pending'     => 'badge-soft-secondary',
        'in_progress' => 'badge-soft-warning',
        'completed'   => 'badge-soft-success',
    ];

    // Colour for a choice option so Pass/Fail/N-A read at a glance.
    $choiceColor = function ($opt) {
        $o = strtolower(trim($opt));
        if (in_array($o, ['pass', 'yes', 'ok', 'good', 'passed', 'available', 'working'])) return '#0f9d69';
        if (in_array($o, ['fail', 'no', 'bad', 'not ok', 'failed', 'faulty', 'damaged', 'not working', 'not available'])) return '#e43f3f';
        return '#6b7280';
    };

    // Build the wizard step list: Details → each checklist section → Verdict.
    $sections = $inspection->type->sections;
    $wsteps = [['type' => 'details', 'name' => 'Customer & Vehicle']];
    foreach ($sections as $s) {
        $wsteps[] = ['type' => 'section', 'name' => $s->sequence . '. ' . $s->section_name, 'section' => $s];
    }
    $wsteps[] = ['type' => 'verdict', 'name' => 'Verdict & Complete'];
    $totalW = count($wsteps);
    $sectionProgress = $inspection->sectionProgress();
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div id="inspection-root"
             data-step-url="{{ route('inspections.autosave.step', $inspection) }}"
             data-section-summary-url="{{ route('inspections.autosave.section-summary', $inspection) }}"
             data-customer-url="{{ route('inspections.autosave.customer', $inspection) }}"
             data-media-url="{{ route('inspections.media.upload', $inspection) }}"
             data-extra-media-url="{{ route('inspections.extra-media.upload', $inspection) }}"
             data-section-media-base="{{ url("inspections/".$inspection->id."/sections") }}"

             data-media-delete-base="{{ url('inspection-media') }}">

            @include('partials._notify')

            {{-- ===== Sticky header + step indicator ===== --}}
            <div class="wiz-head">
                <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:.5rem;">
                    <div>
                        <h4 class="mb-1">Inspection Report</h4>
                        <p class="text-muted mb-0 font-size-13">
                            <i class="bx bx-car"></i> {{ $inspection->car() }} ·
                            {{ $inspection->customer_name ?: '—' }} ·
                            <span class="text-monospace">{{ $inspection->reference }}</span>
                        </p>
                    </div>
                    <div class="d-flex align-items-center" style="gap:.5rem;">
                        <span id="save-status" class="text-muted font-size-12"></span>
                        <span class="badge {{ $statusBadge[$inspection->status] ?? 'badge-soft-secondary' }} font-size-12">
                            {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                        </span>
                        <a href="{{ route('inspections.summary', $inspection) }}" target="_blank" class="btn btn-sm btn-info"><i class="bx bx-list-check"></i> Summary</a>
                        <a href="{{ route('inspections.report', ['inspection' => $inspection, 'download' => 1]) }}" target="_blank" class="btn btn-sm btn-primary"><i class="bx bx-download"></i> Download Report</a>
                        <a href="{{ url('inspections') }}" class="btn btn-sm btn-light">Back</a>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-2">
                    <div class="font-size-13">
                        <span class="font-weight-bold text-primary">Step <span id="wiz-step-pos">1</span> of {{ $totalW }}</span>
                        <span class="text-muted">— <span id="wiz-step-name">{{ $wsteps[0]['name'] }}</span></span>
                    </div>
                    <span class="text-muted font-size-12"><span id="answered-count">{{ $prog['answered'] }}</span>/{{ $prog['total'] }} answered</span>
                </div>

                {{-- thin overall answered progress --}}
                <div class="progress mt-1 mb-2" style="height:6px;">
                    <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: {{ $prog['percent'] }}%"></div>
                </div>

                {{-- horizontal step dots --}}
                <div class="wiz-steps mb-2">
                    @foreach ($wsteps as $i => $ws)
                        @if ($i > 0)<div class="wiz-line" data-wsline="{{ $i }}"></div>@endif
                        <div class="wiz-dot @if($i===0) active @endif" data-wsdot="{{ $i }}" title="{{ $ws['name'] }}">
                            <span class="bead">{{ $i + 1 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('inspections.update', $inspection) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @foreach ($wsteps as $i => $ws)
                    <div class="wizard-step" data-wstep="{{ $i }}" data-wtype="{{ $ws['type'] }}" data-wname="{{ $ws['name'] }}" @if($i>0) style="display:none;" @endif>

                        {{-- ============================ DETAILS STEP ============================ --}}
                        @if ($ws['type'] === 'details')
                            <div class="card detail-card" id="customer-block">
                                <div class="card-header">
                                    <span class="detail-ico"><i class="bx bx-user-circle"></i></span>
                                    <div>
                                        <h5 class="mb-0">Customer &amp; Vehicle</h5>
                                        <small class="text-muted"><i class="bx bx-save"></i> Auto-saved as you type</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="detail-group-title">Report</p>
                                    <div class="row">
                                        {{-- Reference is the linked lead's unique id — derived, so no name attribute (nothing to post). --}}
                                        <div class="col-md-6 mb-3"><label class="form-label">Reference</label><input class="form-control text-monospace" value="{{ $inspection->reference }}" readonly></div>
                                        <div class="col-md-6 mb-3"><label class="form-label">Date of Inspection</label><input name="date_of_inspection" type="date" class="form-control js-customer" value="{{ old('date_of_inspection', optional($inspection->date_of_inspection)->format('Y-m-d')) }}"></div>
                                    </div>

                                    <p class="detail-group-title mt-2">Owner</p>
                                    <div class="row">
                                        <div class="col-md-3 mb-3"><label class="form-label">Customer name <span class="text-danger">*</span></label><input name="customer_name" data-wreq class="form-control js-customer" value="{{ old('customer_name', $inspection->customer_name) }}" required></div>
                                        <div class="col-md-3 mb-3"><label class="form-label">Name in Arabic</label><input name="customer_name_ar" dir="rtl" class="form-control js-customer" maxlength="255" value="{{ old('customer_name_ar', $inspection->customer_name_ar) }}"></div>
                                        <div class="col-md-3 mb-3"><label class="form-label">Phone</label><input name="customer_phone" class="form-control js-customer" value="{{ old('customer_phone', $inspection->customer_phone) }}"></div>
                                        <div class="col-md-3 mb-3"><label class="form-label">Email</label><input name="customer_email" type="email" class="form-control js-customer" value="{{ old('customer_email', $inspection->customer_email) }}"></div>
                                    </div>

                                    <p class="detail-group-title mt-2">Vehicle</p>
                                    <div class="row">
                                        <div class="col-md-3 mb-3"><label class="form-label">Make</label><input name="car_make" class="form-control js-customer" value="{{ old('car_make', $inspection->car_make) }}"></div>
                                        <div class="col-md-3 mb-3"><label class="form-label">Model</label><input name="car_model" class="form-control js-customer" value="{{ old('car_model', $inspection->car_model) }}"></div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Model Year</label>
                                            <select name="car_year" class="form-control form-select js-customer">
                                                <option value="">Select</option>
                                                @foreach ($lookups['years'] as $yr)
                                                    <option value="{{ $yr }}" @selected((int) old('car_year', $inspection->car_year) === $yr)>{{ $yr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Manufacturing Year</label>
                                            <select name="manufacturing_year" class="form-control form-select js-customer">
                                                <option value="">Select</option>
                                                @foreach ($lookups['years'] as $yr)
                                                    <option value="{{ $yr }}" @selected((int) old('manufacturing_year', $inspection->manufacturing_year) === $yr)>{{ $yr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Condition</label>
                                            <select name="vehicle_condition" class="form-control form-select js-customer">
                                                <option value="">Select</option>
                                                @foreach ($lookups['vehicle_condition'] as $opt)
                                                    <option value="{{ $opt }}" @selected(old('vehicle_condition', $inspection->vehicle_condition) === $opt)>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <p class="detail-group-title mt-2">Assignment</p>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Assigned Technician</label>
                                            <select name="technician_id" class="form-control form-select">
                                                <option value="">Select Technician</option>
                                                @foreach ($technicians as $technician)
                                                    <option value="{{ $technician->id }}" @selected(old('technician_id', $inspection->technician_id) == $technician->id)>{{ $technician->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Inspection Template</label>
                                            @if(auth()->user()->isTechnician())
                                                <input type="text" class="form-control" value="{{ optional($inspection->type)->name ?? '—' }}" readonly>
                                            @else
                                                <select name="inspection_type_id" class="form-control form-select" data-original="{{ old('inspection_type_id', $inspection->inspection_type_id) }}">
                                                    @foreach ($inspectionTypes as $tid => $tname)
                                                        <option value="{{ $tid }}" @selected(old('inspection_type_id', $inspection->inspection_type_id) == $tid)>{{ $tname }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Changing the template reloads the checklist. Save first.</small>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="detail-group-title mt-2"><i class="bx bxs-car"></i> Vehicle Details</p>
                                    <div class="row">
                                        <div class="col-md-6 mb-3"><label class="form-label">VIN / Chassis No.</label><input name="vin" class="form-control" maxlength="50" value="{{ old('vin', $inspection->vin) }}"></div>
                                        <div class="col-md-6 mb-3"><label class="form-label">Plate Number</label><input name="plate_no" class="form-control" maxlength="50" value="{{ old('plate_no', $inspection->plate_no) }}"></div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Exterior Color</label>
                                            <select name="exterior_color" class="form-control form-select">
                                                <option value="">Select</option>
                                                @foreach ($lookups['exterior_color'] as $opt)
                                                    <option value="{{ $opt }}" @selected(old('exterior_color', $inspection->exterior_color) === $opt)>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3"><label class="form-label">Region</label><input name="region" class="form-control" maxlength="100" value="{{ old('region', $inspection->region) }}"></div>
                                        <div class="col-md-3 mb-3"><label class="form-label">Body Type</label><input name="body_type" class="form-control" maxlength="50" value="{{ old('body_type', $inspection->body_type) }}"></div>
                                        <div class="col-md-3 mb-3"><label class="form-label">No. of Keys</label><input name="number_of_keys" type="number" min="0" max="20" class="form-control" value="{{ old('number_of_keys', $inspection->number_of_keys) }}"></div>
                                    </div>

                                    <p class="detail-group-title mt-2">Powertrain</p>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Fuel Type</label>
                                            <select name="fuel_type" class="form-control form-select">
                                                <option value="">Select</option>
                                                @foreach ($lookups['fuel_type'] as $opt)
                                                    <option value="{{ $opt }}" @selected(old('fuel_type', $inspection->fuel_type) === $opt)>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Gearbox</label>
                                            <select name="gearbox" class="form-control form-select">
                                                <option value="">Select</option>
                                                @foreach ($lookups['gearbox'] as $opt)
                                                    <option value="{{ $opt }}" @selected(old('gearbox', $inspection->gearbox) === $opt)>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3"><label class="form-label">Cylinders</label><input name="cylinders" class="form-control" maxlength="50" value="{{ old('cylinders', $inspection->cylinders) }}"></div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Steering Side</label>
                                            <select name="steering_side" class="form-control form-select">
                                                <option value="">Select</option>
                                                @foreach ($lookups['steering_side'] as $opt)
                                                    <option value="{{ $opt }}" @selected(old('steering_side', $inspection->steering_side) === $opt)>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <p class="detail-group-title mt-2">Warranty / Services</p>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">With Service History</label>
                                            <select name="with_service_history" class="form-control form-select">
                                                <option value="0" @selected(! old('with_service_history', $inspection->with_service_history))>No</option>
                                                <option value="1" @selected(old('with_service_history', $inspection->with_service_history))>Yes</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3"><label class="form-label">Last Service Date</label><input name="last_service_date" type="date" class="form-control" value="{{ old('last_service_date', optional($inspection->last_service_date)->format('Y-m-d')) }}"></div>
                                    </div>
                                </div>
                            </div>

                        {{-- ============================ SECTION STEP ============================ --}}
                        @elseif ($ws['type'] === 'section')
                            @php($section = $ws['section'])
                            <div class="card" data-section="{{ $section->id }}" id="section-card-{{ $section->id }}">
                                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($section->group_name)
                                            <div class="text-uppercase text-muted font-size-11 font-weight-bold" style="letter-spacing:.04em;">{{ $section->group_name }}@if($section->group_name_ar)<span dir="rtl"> — {{ $section->group_name_ar }}</span>@endif</div>
                                        @endif
                                        <h5 class="mb-0">{{ $section->section_name }}@if($section->section_name_ar)<small class="text-muted" dir="rtl"> — {{ $section->section_name_ar }}</small>@endif</h5>
                                    </div>
                                    <span class="badge badge-soft-secondary font-size-12" data-section-badge="{{ $section->id }}">0/{{ $section->steps->count() }}</span>
                                </div>
                                <div class="card-body">
                                    @forelse ($section->steps as $step)
                                        @php($detail = $answers->get($step->id))
                                        <div class="q-card" data-step="{{ $step->id }}">
                                                                                        <div class="q-head">
                                                <div class="q-titlewrap">
                                                    <div class="q-title">{{ $step->question }}</div>
                                                    @if ($step->description)<p class="text-muted font-size-12 mb-0 mt-1">{{ $step->description }}</p>@endif
                                                </div>
                                                <div class="q-answer">

                                            @if ($step->show_rating)
                                                <div class="d-inline-flex align-items-center mb-2" data-rating="{{ $step->id }}">
                                                    <input type="hidden" name="answers[{{ $step->id }}][rating]" value="{{ (int) ($detail->rating ?? 0) ?: '' }}">
                                                    @for ($n = 1; $n <= 5; $n++)
                                                        <span class="js-star" data-step="{{ $step->id }}" data-val="{{ $n }}"
                                                              style="color:{{ $n <= (int) ($detail->rating ?? 0) ? '#f1b44c' : '#ccc' }};">★</span>
                                                    @endfor
                                                    <small class="text-muted ml-2 js-rating-label" data-step="{{ $step->id }}">{{ ($detail->rating ?? 0) ? ($detail->rating . '/5') : '' }}</small>
                                                </div>
                                            @endif

                                            @if ($step->show_multiple_choice)
                                                <div class="choice-radios">
                                                    @foreach (($step->multiple_choice_options ?? []) as $oi => $opt)
                                                        <span class="choice-radio">
                                                            <input class="form-check-input" type="radio" id="c-{{ $step->id }}-{{ $oi }}" name="answers[{{ $step->id }}][choice]" value="{{ $opt }}"
                                                                   style="accent-color: {{ $choiceColor($opt) }};"
                                                                   @checked(($detail->choice ?? '') === $opt) onchange="AA.saveStep({{ $step->id }})">
                                                            <label class="form-check-label" for="c-{{ $step->id }}-{{ $oi }}" style="color: {{ $choiceColor($opt) }};">{{ $opt }}</label>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                                </div>
                                            </div>

                                            @php($choiceGated = $step->show_multiple_choice && (in_array('Pass', $step->multiple_choice_options ?? [], true) || in_array('Fail', $step->multiple_choice_options ?? [], true)))
                                            {{-- Options that reveal the remedial box (Bad / Average / Fail / No / …). --}}
                                            @php($remedialTriggers = collect($step->multiple_choice_options ?? [])
                                                    ->filter(fn ($o) => in_array($o, \App\Models\Inspection::REMEDIAL_CHOICES, true))
                                                    ->values()->all())
                                            @php($remedialGated = $step->show_multiple_choice && $remedialTriggers !== [])
                                            @if ($step->show_text_answer || $choiceGated)
                                                <div class="observation-wrap mt-2" data-observation="{{ $choiceGated ? $step->id : '' }}"
                                                    style="{{ $choiceGated && ($detail->choice ?? '') !== 'Fail' ? 'display:none;' : '' }}">
                                                    <textarea name="answers[{{ $step->id }}][text]" rows="2" placeholder="Observations…"
                                                        oninput="AA.debounceStep({{ $step->id }})" class="form-control mb-2">{{ $detail->descriptive_answer ?? '' }}</textarea>
                                                </div>
                                            @endif

                                            @if ($step->show_remedial_suggestions)
                                                {{-- Remedial is gated to the question's less-than-pass options (Bad, Average,
                                                     Fail, No, …). Questions with none of them always show it. --}}
                                                <div class="remedial-wrap" data-remedial="{{ $remedialGated ? $step->id : '' }}" data-remedial-triggers="{{ implode('|', $remedialTriggers) }}"
                                                    style="{{ $remedialGated && ! in_array($detail->choice ?? '', $remedialTriggers, true) ? 'display:none;' : '' }}">
                                                    <label class="form-label font-size-12 text-danger mb-1">Remedial suggestion{{ $remedialGated ? ' (for '.implode(' / ', $remedialTriggers).')' : '' }}</label>
                                                    <textarea name="answers[{{ $step->id }}][remedial]" rows="2" placeholder="What needs to be repaired / replaced…"
                                                        oninput="AA.debounceStep({{ $step->id }})" class="form-control mb-2">{{ $detail->remedial_suggestion ?? '' }}</textarea>
                                                </div>
                                            @endif

                                            @if ($step->photos !== 'not_required' || $step->videos !== 'not_required')
                                                <div class="row mt-2">
                                                    @if ($step->photos !== 'not_required')
                                                        <div class="col-sm-6 mb-2">
                                                            <label class="upload-drop">
                                                                <input type="file" accept="image/*" multiple class="d-none" onchange="AA.uploadFiles(this, {{ $step->id }}, 'photo')">
                                                                <i class="bx bx-image-add"></i>
                                                                <span>Add photo <small class="{{ $step->photos === 'mandatory' ? 'text-danger' : 'text-muted' }}">({{ $step->photos === 'mandatory' ? 'required' : 'optional' }})</small></span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                    @if ($step->videos !== 'not_required')
                                                        <div class="col-sm-6 mb-2">
                                                            <label class="upload-drop">
                                                                <input type="file" accept="video/*" multiple class="d-none" onchange="AA.uploadFiles(this, {{ $step->id }}, 'video')">
                                                                <i class="bx bx-video-plus"></i>
                                                                <span>Add video <small class="{{ $step->videos === 'mandatory' ? 'text-danger' : 'text-muted' }}">({{ $step->videos === 'mandatory' ? 'required' : 'optional' }})</small></span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-wrap mt-1" style="gap:.5rem;" id="media-{{ $step->id }}">
                                                    @if ($detail)
                                                        @foreach ($detail->media as $m)
                                                            <div class="position-relative" data-media="{{ $m->id }}">
                                                                @if ($m->type === 'photo')
                                                                    <img src="{{ $m->url }}" style="width:64px;height:64px;object-fit:cover;border-radius:.5rem;border:1px solid #eee;">
                                                                @else
                                                                    <video src="{{ $m->url }}" controls preload="metadata" style="width:96px;height:64px;object-fit:cover;border-radius:.5rem;border:1px solid #eee;background:#000;"></video>
                                                                @endif
                                                                <button type="button" onclick="AA.deleteMedia({{ $m->id }})"
                                                                    class="btn btn-danger btn-sm position-absolute p-0"
                                                                    style="top:-8px;right:-8px;width:20px;height:20px;border-radius:50%;line-height:1;">×</button>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">No items in this section.</p>
                                    @endforelse

                                    {{-- Category-level media, kept apart from the questions above:
                                         a single picker taking photos and videos, several at a time. --}}
                                    <div class="sec-media border-top pt-3 mt-3" data-section-media="{{ $section->id }}">
                                        <label class="form-label font-size-13 font-weight-bold mb-1">
                                            {{ $section->section_name }} media
                                            <small class="text-muted font-weight-normal">— photos or videos, select multiple</small>
                                        </label>
                                        <label class="upload-drop">
                                            {{-- One picker for both kinds; the type is derived per file from its MIME type. --}}
                                            <input type="file" accept="image/*,video/*" multiple class="d-none"
                                                   onchange="AA.uploadSection(this, {{ $section->id }})">
                                            <i class="bx bx-images"></i>
                                            <span>Add files <small class="text-muted">(photos or videos)</small></span>
                                        </label>
                                        <div class="d-flex flex-wrap mt-2" style="gap:.75rem;" id="media-section-{{ $section->id }}">
                                            @foreach (($sectionMedia[$section->id] ?? collect()) as $m)
                                                <div class="extra-item" data-media="{{ $m->id }}">
                                                    <div class="extra-item__thumb">
                                                        @if ($m->type === 'photo')
                                                            <img src="{{ $m->url }}">
                                                        @else
                                                            <video src="{{ $m->url }}" controls preload="metadata"></video>
                                                        @endif
                                                        <button type="button" onclick="AA.deleteMedia({{ $m->id }})"
                                                            class="btn btn-danger btn-sm position-absolute p-0"
                                                            style="top:-8px;right:-8px;width:20px;height:20px;border-radius:50%;line-height:1;">×</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Section-level summary + optional rating (shown on the report's Inspection Summary) --}}
                                    @php($sectionSummary = ($sectionSummaries ?? collect())->get($section->id))
                                    @php($secRating = (int) old('section_ratings.'.$section->id, $sectionSummary->rating ?? 0))
                                    <div class="border-top pt-3 mt-2" data-section-summary="{{ $section->id }}">
                                        <label class="form-label font-size-13 font-weight-bold mb-1">
                                            {{ $section->section_name }} summary
                                            <small class="text-muted font-weight-normal">— shown on the report</small>
                                        </label>
                                        <textarea name="section_summaries[{{ $section->id }}]" rows="2"
                                            placeholder="e.g. {{ $section->section_name }} is in good condition"
                                            oninput="AA.debounceSectionSummary({{ $section->id }})"
                                            class="form-control">{{ old('section_summaries.'.$section->id, $sectionSummary->summary ?? '') }}</textarea>

                                        <div class="d-inline-flex align-items-center mt-2" data-section-rating="{{ $section->id }}">
                                            <span class="font-size-12 text-muted mr-2">Section rating <span class="text-muted">(optional)</span>:</span>
                                            <input type="hidden" name="section_ratings[{{ $section->id }}]" value="{{ $secRating ?: '' }}">
                                            @for ($n = 1; $n <= 5; $n++)
                                                <span class="js-secstar" data-section="{{ $section->id }}" data-val="{{ $n }}"
                                                      style="color:{{ $n <= $secRating ? '#f1b44c' : '#ccc' }};">★</span>
                                            @endfor
                                            <small class="text-muted ml-2 js-secrating-label" data-section="{{ $section->id }}">{{ $secRating ? $secRating.'/5' : '' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        {{-- ============================ VERDICT STEP ============================ --}}
                        @else
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Overall Verdict</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3"><label class="form-label">Odometer (km)</label><input name="odometer" type="number" class="form-control" value="{{ old('odometer', $inspection->odometer) }}"></div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Overall condition</label>
                                            <select name="overall_condition" class="form-control form-select">
                                                <option value="">—</option>
                                                @foreach (\App\Models\Inspection::CONDITIONS as $v => $l)<option value="{{ $v }}" @selected($inspection->overall_condition === $v)>{{ $l }}</option>@endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3"><label class="form-label">Est. repair cost</label><input name="estimated_repair_cost" type="number" step="0.01" class="form-control" value="{{ old('estimated_repair_cost', $inspection->estimated_repair_cost) }}"></div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Recommendation</label>
                                            <select name="recommendation" data-wreq class="form-control form-select">
                                                <option value="">—</option>
                                                @foreach (\App\Models\Inspection::RECOMMENDATIONS as $v => $l)<option value="{{ $v }}" @selected($inspection->recommendation === $v)>{{ $l }}</option>@endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12"><label class="form-label">Summary report</label><textarea name="summary" rows="4" class="form-control">{{ old('summary', $inspection->summary) }}</textarea></div>
                                    </div>

                                    {{-- Per-type summaries. Types come from tbl_summary_type, the same
                                         lookup the legacy /inspectionreport summary tab reads. --}}
                                    @if (!empty($summaryTypes))
                                        <hr class="detail-sep">
                                        <h6 class="mb-1"><i class="bx bx-notepad text-success"></i> Summary</h6>
                                        <p class="text-muted font-size-12 mb-3">A note per area — shown on the report.</p>
                                        <div class="row">
                                            @foreach ($summaryTypes as $typeId => $typeName)
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">{{ $typeName }}</label>
                                                    <textarea name="summaries[{{ $typeId }}]" rows="2" class="form-control"
                                                        placeholder="e.g. {{ $typeName }} is in good condition">{{ old('summaries.'.$typeId, $summaries[$typeId] ?? '') }}</textarea>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <hr class="detail-sep">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0"><i class="bx bx-list-check text-success"></i> Completion Status</h6>
                                        <span class="badge badge-soft-info font-size-13" id="overall-badge">0/0 answered</span>
                                    </div>
                                    <div class="progress mb-3" style="height:10px;">
                                        <div id="completion-bar" class="progress-bar bg-success" role="progressbar" style="width:0%"></div>
                                    </div>
                                    <p class="text-muted font-size-12 mb-3">Every section must be fully answered before the inspection can be completed. Tap a section to jump to it.</p>
                                    <div class="row">
                                        @foreach ($sectionProgress as $sp)
                                            <div class="col-md-6 col-lg-4 mb-2">
                                                <button type="button" class="d-flex justify-content-between align-items-center p-2 rounded section-status-item w-100"
                                                        data-status-sec="{{ $sp['id'] }}" data-goto-section="{{ $sp['id'] }}"
                                                        style="border:1px solid #e5e7eb;background:#fff;gap:.5rem;">
                                                    <span class="text-truncate text-dark" style="max-width:72%">{{ $sp['name'] }}</span>
                                                    <span class="badge badge-soft-warning section-status-badge text-nowrap">{{ $sp['answered'] }}/{{ $sp['total'] }}</span>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if ($isCompleted)<div class="mt-2 text-success"><i class="bx bx-check-circle"></i> Completed {{ optional($inspection->completed_at)->format('d M Y, H:i') }}</div>@endif
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach

                {{-- ===== Additional media (always visible across all steps) ===== --}}
                <div class="card detail-card" id="extra-media-block">
                    <div class="card-header" role="button" onclick="document.getElementById('extra-media-body').classList.toggle('d-none'); this.querySelector('.extra-chev').classList.toggle('bx-chevron-down'); this.querySelector('.extra-chev').classList.toggle('bx-chevron-up');">
                        <span class="detail-ico"><i class="bx bx-images"></i></span>
                        <div class="flex-grow-1">
                            <h5 class="mb-0">Additional Media</h5>
                            <small class="text-muted">Extra photos &amp; videos — uploaded instantly, available on every step</small>
                        </div>
                        <i class="bx bx-chevron-up extra-chev font-size-22 text-muted"></i>
                    </div>
                    <div class="card-body" id="extra-media-body">
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label class="upload-drop">
                                    <input type="file" accept="image/*" multiple class="d-none" onchange="AA.uploadExtra(this, 'photo')">
                                    <i class="bx bx-image-add"></i>
                                    <span>Add photos <small class="text-muted">(optional)</small></span>
                                </label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label class="upload-drop">
                                    <input type="file" accept="video/*" multiple class="d-none" onchange="AA.uploadExtra(this, 'video')">
                                    <i class="bx bx-video-plus"></i>
                                    <span>Add videos <small class="text-muted">(optional)</small></span>
                                </label>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap mt-2" style="gap:.75rem;" id="media-extra">
                            @forelse ($extraMedia as $m)
                                <div class="extra-item" data-media="{{ $m->id }}">
                                    <div class="extra-item__thumb">
                                        @if ($m->type === 'photo')
                                            <img src="{{ $m->url }}">
                                        @else
                                            <video src="{{ $m->url }}" controls preload="metadata"></video>
                                        @endif
                                        <button type="button" onclick="AA.deleteMedia({{ $m->id }})"
                                            class="btn btn-danger btn-sm position-absolute p-0"
                                            style="top:-8px;right:-8px;width:20px;height:20px;border-radius:50%;line-height:1;">×</button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm extra-item__label" maxlength="255"
                                        placeholder="Add a label…" value="{{ $m->label }}"
                                        oninput="AA.debounceLabel({{ $m->id }}, this.value)" onblur="AA.saveLabel({{ $m->id }}, this.value)">
                                </div>
                            @empty
                                <span class="text-muted font-size-12" id="extra-empty">No additional media yet.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ===== Fixed bottom navigation ===== --}}
                <div class="wiz-nav">
                    <button type="button" id="wiz-prev" class="btn btn-outline-secondary" disabled><i class="bx bx-chevron-left"></i> Previous</button>
                    <div class="wiz-spacer"></div>
                    <button type="submit" name="complete" value="0" class="btn btn-light"><i class="bx bx-save"></i> Save</button>
                    <button type="button" id="wiz-next" class="btn btn-primary btn-next">Next <i class="bx bx-chevron-right"></i></button>
                    <span id="wiz-finish" style="display:none;">
                        @unless($isCompleted)
                            <button type="submit" name="complete" value="1" id="btn-complete" class="btn btn-success btn-complete" disabled><i class="bx bx-check-double"></i> Complete</button>
                            <span id="complete-hint" class="text-muted font-size-12 ms-2 d-none d-md-inline">⚠ Answer all questions first.</span>
                        @else
                            <span class="text-success"><i class="bx bx-check-circle"></i> Completed</span>
                        @endunless
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Resume on the last-edited card BEFORE first paint, so the page never flashes
     card 1 and then jumps. The target index was decided at save time (below). --}}
<script>
(function () {
    try {
        var raw = localStorage.getItem('aaInspStep:{{ $inspection->id }}');
        if (raw === null) return;
        var idx = parseInt(raw, 10);
        var panels = document.querySelectorAll('#inspection-root [data-wstep]');
        if (isNaN(idx) || idx < 0 || idx >= panels.length) return;
        for (var k = 0; k < panels.length; k++) { panels[k].style.display = (k === idx) ? '' : 'none'; }
        var dots = document.querySelectorAll('#inspection-root [data-wsdot]');
        for (var j = 0; j < dots.length; j++) { dots[j].classList.toggle('active', j === idx); }
    } catch (e) {}
})();
</script>

@endsection

@section('js')
<script>
(function () {
    const root = document.getElementById('inspection-root');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const status = document.getElementById('save-status');
    const urls = {
        step: root.dataset.stepUrl,
        sectionSummary: root.dataset.sectionSummaryUrl,
        customer: root.dataset.customerUrl,
        media: root.dataset.mediaUrl,
        extraMedia: root.dataset.extraMediaUrl,
        sectionMediaBase: root.dataset.sectionMediaBase,
        mediaDelete: root.dataset.mediaDeleteBase,
    };
    let timers = {};

    function setStatus(text, cls) {
        status.textContent = text;
        status.className = 'font-size-12 ' + (cls || 'text-muted');
    }
    const saving = () => setStatus('Saving…', 'text-warning');
    const saved  = () => setStatus('✓ All changes saved', 'text-success');
    const failed = () => setStatus('⚠ Save failed — retrying on next change', 'text-danger');

    async function post(url, body) {
        const opts = { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } };
        if (body instanceof FormData) { opts.body = body; }
        else { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const res = await fetch(url, opts);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    }

    function collectStep(stepId) {
        const el = root.querySelector('[data-step="' + stepId + '"]');
        const rating = el.querySelector('input[name="answers[' + stepId + '][rating]"]');
        const choice = el.querySelector('input[name="answers[' + stepId + '][choice]"]:checked');
        const text = el.querySelector('textarea[name="answers[' + stepId + '][text]"]');
        const remedial = el.querySelector('textarea[name="answers[' + stepId + '][remedial]"]');
        return {
            step_id: stepId,
            rating: rating && rating.value ? parseInt(rating.value, 10) : null,
            choice: choice ? choice.value : null,
            descriptive_answer: text ? text.value : null,
            remedial_suggestion: remedial ? remedial.value : null,
        };
    }

    const AA = {
        async saveStep(stepId) {
            saving();
            try {
                const d = await post(urls.step, collectStep(stepId));
                if (d.progress) {
                    document.getElementById('answered-count').textContent = d.progress.answered;
                    document.getElementById('progress-bar').style.width = d.progress.percent + '%';
                }
                saved();
            } catch (e) { failed(); }
        },
        debounceStep(stepId) {
            clearTimeout(timers['s' + stepId]);
            timers['s' + stepId] = setTimeout(() => AA.saveStep(stepId), 700);
        },
        async saveSectionSummary(sectionId) {
            saving();
            const el = root.querySelector('textarea[name="section_summaries[' + sectionId + ']"]');
            const rt = root.querySelector('input[name="section_ratings[' + sectionId + ']"]');
            try {
                await post(urls.sectionSummary, {
                    section_id: sectionId,
                    summary: el ? el.value : null,
                    rating: rt && rt.value ? parseInt(rt.value, 10) : null,
                });
                saved();
            } catch (e) { failed(); }
        },
        debounceSectionSummary(sectionId) {
            clearTimeout(timers['ss' + sectionId]);
            timers['ss' + sectionId] = setTimeout(() => AA.saveSectionSummary(sectionId), 700);
        },
        async saveCustomer() {
            saving();
            const body = {};
            // Collect every named field in the block (customer, vehicle, assignment)
            // except the template selector, which reloads the checklist on full submit.
            root.querySelectorAll('#customer-block [name]').forEach(i => {
                if (i.name === 'inspection_type_id') return;
                body[i.name] = i.value;
            });
            try { await post(urls.customer, body); saved(); } catch (e) { failed(); }
        },
        debounceCustomer() {
            clearTimeout(timers.cust);
            timers.cust = setTimeout(() => AA.saveCustomer(), 800);
        },
        async uploadFiles(input, stepId, type) {
            const files = Array.from(input.files || []);
            input.value = '';
            const MAX_BYTES = 100 * 1024 * 1024; // must match InspectionController::uploadMedia (max:102400 KB)
            for (const file of files) {
                if (file.size > MAX_BYTES) {
                    failed();
                    alert('"' + file.name + '" is ' + (file.size / 1048576).toFixed(1) + ' MB — the limit is 100 MB. Please upload a smaller file.');
                    continue;
                }
                saving();
                const fd = new FormData();
                fd.append('step_id', stepId);
                fd.append('type', type);
                fd.append('file', file);
                try {
                    const m = await post(urls.media, fd);
                    AA.addThumb(stepId, m);
                    saved();
                } catch (e) {
                    failed();
                    alert('Upload of "' + file.name + '" failed (' + (e.message || 'error') + '). If it is a large video, the server upload limit may be too low.');
                }
            }
        },
        /**
         * Category-level upload. The picker is `multiple`, so every selected file
         * is posted to this section's bucket in turn and thumbnailed as it lands.
         */
        async uploadSection(input, sectionId) {
            const files = Array.from(input.files || []);
            input.value = '';
            const MAX_BYTES = 100 * 1024 * 1024;
            const url = urls.sectionMediaBase + '/' + sectionId + '/media';
            for (const file of files) {
                if (file.size > MAX_BYTES) {
                    failed();
                    alert('"' + file.name + '" is ' + (file.size / 1048576).toFixed(1) + ' MB — the limit is 100 MB.');
                    continue;
                }
                // One picker takes both, so classify each file by its MIME type.
                const type = (file.type || '').startsWith('video/') ? 'video' : 'photo';
                saving();
                const fd = new FormData();
                fd.append('type', type);
                fd.append('file', file);
                try {
                    const m = await post(url, fd);
                    AA.addSectionThumb(sectionId, m);
                    saved();
                } catch (e) {
                    failed();
                    alert('Upload of "' + file.name + '" failed (' + (e.message || 'error') + ').');
                }
            }
        },
        addSectionThumb(sectionId, m) {
            const box = document.getElementById('media-section-' + sectionId);
            if (!box) return;
            const wrap = document.createElement('div');
            wrap.className = 'extra-item';
            wrap.dataset.media = m.id;
            const media = m.type === 'photo'
                ? '<img src="' + m.url + '">'
                : '<video src="' + m.url + '" controls preload="metadata"></video>';
            wrap.innerHTML =
                '<div class="extra-item__thumb">' + media +
                '<button type="button" class="btn btn-danger btn-sm position-absolute p-0" style="top:-8px;right:-8px;width:20px;height:20px;border-radius:50%;line-height:1;">×</button></div>';
            wrap.querySelector('button').addEventListener('click', () => AA.deleteMedia(m.id));
            box.appendChild(wrap);
        },
        async uploadExtra(input, type) {
            const files = Array.from(input.files || []);
            input.value = '';
            const MAX_BYTES = 100 * 1024 * 1024;
            for (const file of files) {
                if (file.size > MAX_BYTES) {
                    failed();
                    alert('"' + file.name + '" is ' + (file.size / 1048576).toFixed(1) + ' MB — the limit is 100 MB.');
                    continue;
                }
                saving();
                const fd = new FormData();
                fd.append('type', type);
                fd.append('file', file);
                try {
                    const m = await post(urls.extraMedia, fd);
                    AA.addExtraThumb(m);
                    saved();
                } catch (e) {
                    failed();
                    alert('Upload of "' + file.name + '" failed (' + (e.message || 'error') + ').');
                }
            }
        },
        addExtraThumb(m) {
            const box = document.getElementById('media-extra');
            const empty = document.getElementById('extra-empty');
            if (empty) empty.remove();
            const wrap = document.createElement('div');
            wrap.className = 'extra-item';
            wrap.dataset.media = m.id;
            const media = m.type === 'photo'
                ? '<img src="' + m.url + '">'
                : '<video src="' + m.url + '" controls preload="metadata"></video>';
            wrap.innerHTML =
                '<div class="extra-item__thumb">' + media +
                '<button type="button" class="btn btn-danger btn-sm position-absolute p-0" style="top:-8px;right:-8px;width:20px;height:20px;border-radius:50%;line-height:1;">×</button></div>' +
                '<input type="text" class="form-control form-control-sm extra-item__label" maxlength="255" placeholder="Add a label…" value="' + (m.label || '') + '">';
            wrap.querySelector('button').addEventListener('click', () => AA.deleteMedia(m.id));
            const label = wrap.querySelector('.extra-item__label');
            label.addEventListener('input', () => AA.debounceLabel(m.id, label.value));
            label.addEventListener('blur', () => AA.saveLabel(m.id, label.value));
            box.appendChild(wrap);
        },
        async saveLabel(id, value) {
            clearTimeout(timers['lbl' + id]);
            try {
                await post(urls.mediaDelete + '/' + id + '/label', { label: value });
                saved();
            } catch (e) { failed(); }
        },
        debounceLabel(id, value) {
            saving();
            clearTimeout(timers['lbl' + id]);
            timers['lbl' + id] = setTimeout(() => AA.saveLabel(id, value), 800);
        },
        addThumb(stepId, m) {
            const box = document.getElementById('media-' + stepId);
            const wrap = document.createElement('div');
            wrap.className = 'position-relative';
            wrap.dataset.media = m.id;
            const inner = m.type === 'photo'
                ? '<img src="' + m.url + '" style="width:64px;height:64px;object-fit:cover;border-radius:.5rem;border:1px solid #eee;">'
                : '<video src="' + m.url + '" controls preload="metadata" style="width:96px;height:64px;object-fit:cover;border-radius:.5rem;border:1px solid #eee;background:#000;"></video>';
            wrap.innerHTML = inner + '<button type="button" class="btn btn-danger btn-sm position-absolute p-0" style="top:-8px;right:-8px;width:20px;height:20px;border-radius:50%;line-height:1;">×</button>';
            wrap.querySelector('button').addEventListener('click', () => AA.deleteMedia(m.id));
            box.appendChild(wrap);
        },
        async deleteMedia(id) {
            const c = window.aaConfirm
                ? await aaConfirm({ title: 'Remove file?', text: 'This file will be permanently deleted.', confirmText: 'Remove' })
                : { isConfirmed: confirm('Remove this file?') };
            if (!c.isConfirmed) return;
            saving();
            try {
                await fetch(urls.mediaDelete + '/' + id, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: new URLSearchParams({ _method: 'DELETE' }),
                });
                const node = root.querySelector('[data-media="' + id + '"]');
                if (node) node.remove();
                saved();
            } catch (e) { failed(); }
        },
    };
    window.AA = AA;

    // ---- Live completion status ------------------------------------------
    function stepAnswered(el) {
        if (el.querySelector('input[type="radio"]:checked')) return true;
        const rating = el.querySelector('input[name$="[rating]"]');
        if (rating && parseInt(rating.value || '0', 10) > 0) return true;
        const text = el.querySelector('textarea[name$="[text]"]');
        if (text && text.value.trim() !== '') return true;
        return false;
    }

    function recompute() {
        let total = 0, ans = 0;
        root.querySelectorAll('[data-section]').forEach(function (sec) {
            // Only count question cards — rating stars & labels also carry
            // data-step, which would otherwise inflate the section total.
            const steps = sec.querySelectorAll('.q-card[data-step]');
            let a = 0;
            steps.forEach(function (s) { if (stepAnswered(s)) a++; });
            total += steps.length; ans += a;
            const done = steps.length > 0 && a >= steps.length;

            const secBadge = root.querySelector('[data-section-badge="' + sec.dataset.section + '"]');
            if (secBadge) {
                secBadge.textContent = a + '/' + steps.length;
                secBadge.className = 'badge font-size-12 ' + (done ? 'badge-soft-success' : 'badge-soft-secondary');
            }
            const item = root.querySelector('[data-status-sec="' + sec.dataset.section + '"]');
            if (item) {
                item.style.borderColor = done ? '#34c38f' : '#e5e7eb';
                item.style.background = done ? '#f1fbf6' : '#fff';
                const b = item.querySelector('.section-status-badge');
                if (b) { b.textContent = a + '/' + steps.length; b.className = 'badge section-status-badge text-nowrap ' + (done ? 'badge-soft-success' : 'badge-soft-warning'); }
            }
        });

        const overall = document.getElementById('overall-badge');
        if (overall) overall.textContent = ans + '/' + total + ' answered';
        const cbar = document.getElementById('completion-bar');
        if (cbar) cbar.style.width = (total > 0 ? Math.round(ans / total * 100) : 0) + '%';
        const pbar = document.getElementById('progress-bar');
        if (pbar) pbar.style.width = (total > 0 ? Math.round(ans / total * 100) : 0) + '%';
        const ac = document.getElementById('answered-count');
        if (ac) ac.textContent = ans;

        const allDone = total > 0 && ans >= total;
        const btn = document.getElementById('btn-complete');
        if (btn) btn.disabled = !allDone;
        const hint = document.getElementById('complete-hint');
        if (hint) hint.style.display = allDone ? 'none' : '';

        recomputeStepper();
        return allDone;
    }
    window.AA.recompute = recompute;

    let recomputeTimer;
    function recomputeSoon() { clearTimeout(recomputeTimer); recomputeTimer = setTimeout(recompute, 250); }
    root.addEventListener('change', recompute);
    root.addEventListener('input', recomputeSoon);

    // Reveal follow-up fields based on the chosen answer:
    //   Observations → shown only for "Fail"
    //   Remedial     → shown only for "Fail"
    function toggleField(sel, stepId, show) {
        const w = root.querySelector(sel + stepId + '"]');
        if (!w) return;
        w.style.display = show ? '' : 'none';
        if (!show) {                                   // clear hidden field so stale text isn't saved
            const t = w.querySelector('textarea');
            if (t && t.value) { t.value = ''; AA.debounceStep(stepId); }
        }
    }
    function toggleAnswerFields(stepId, value) {
        toggleField('[data-observation="', stepId, value === 'Fail');
        // Remedial shows for any of the question's less-than-pass options (Bad, Average, …).
        const remWrap = root.querySelector('[data-remedial="' + stepId + '"]');
        const triggers = (remWrap ? remWrap.getAttribute('data-remedial-triggers') || '' : '')
            .split('|').filter(Boolean);
        toggleField('[data-remedial="', stepId, triggers.includes(value));
    }
    root.addEventListener('change', function (e) {
        const t = e.target;
        if (t && t.matches('input[type="radio"][name^="answers["]')) {
            const m = t.name.match(/answers\[(\d+)\]\[choice\]/);
            if (m) toggleAnswerFields(m[1], t.value);
        }
    });

    // Star rating (vanilla replacement for Alpine)
    function paintStars(stepId, val) {
        root.querySelectorAll('.js-star[data-step="' + stepId + '"]').forEach(s => {
            s.style.color = parseInt(s.dataset.val, 10) <= val ? '#f1b44c' : '#ccc';
        });
        const label = root.querySelector('.js-rating-label[data-step="' + stepId + '"]');
        if (label) label.textContent = val ? val + '/5' : '';
    }
    root.querySelectorAll('.js-star').forEach(star => {
        star.addEventListener('click', () => {
            const stepId = star.dataset.step;
            const val = parseInt(star.dataset.val, 10);
            const hidden = root.querySelector('input[name="answers[' + stepId + '][rating]"]');
            const current = hidden.value ? parseInt(hidden.value, 10) : 0;
            const next = current === val ? 0 : val;       // click same star again to clear
            hidden.value = next || '';
            paintStars(stepId, next);
            AA.saveStep(stepId);
            recompute();
        });
    });

    // Optional per-section rating stars.
    function paintSecStars(sectionId, val) {
        root.querySelectorAll('.js-secstar[data-section="' + sectionId + '"]').forEach(s => {
            s.style.color = parseInt(s.dataset.val, 10) <= val ? '#f1b44c' : '#ccc';
        });
        const label = root.querySelector('.js-secrating-label[data-section="' + sectionId + '"]');
        if (label) label.textContent = val ? val + '/5' : '';
    }
    root.querySelectorAll('.js-secstar').forEach(star => {
        star.addEventListener('click', () => {
            const sectionId = star.dataset.section;
            const val = parseInt(star.dataset.val, 10);
            const hidden = root.querySelector('input[name="section_ratings[' + sectionId + ']"]');
            const current = hidden.value ? parseInt(hidden.value, 10) : 0;
            const next = current === val ? 0 : val;       // click same star again to clear
            hidden.value = next || '';
            paintSecStars(sectionId, next);
            AA.saveSectionSummary(sectionId);
        });
    });

    // Customer/vehicle/assignment field auto-save (all fields except the
    // template selector, which is applied on full submit).
    root.querySelectorAll('#customer-block [name]').forEach(i => {
        if (i.name === 'inspection_type_id') return;
        const evt = (i.tagName === 'SELECT') ? 'change' : 'input';
        i.addEventListener(evt, () => AA.debounceCustomer());
    });

    // ---- Wizard navigation ------------------------------------------------
    const panels = Array.from(root.querySelectorAll('[data-wstep]'));
    const dots   = Array.from(root.querySelectorAll('[data-wsdot]'));
    const lines  = Array.from(root.querySelectorAll('[data-wsline]'));
    const posEl  = document.getElementById('wiz-step-pos');
    const nameEl = document.getElementById('wiz-step-name');
    const prevBtn = document.getElementById('wiz-prev');
    const nextBtn = document.getElementById('wiz-next');
    const finish  = document.getElementById('wiz-finish');
    let cur = 0;

    // Remember which card is being edited so a Save (which reloads the page)
    // can bring the user back to the same card instead of restarting at card 1.
    const RESUME_KEY = 'aaInspStep:{{ $inspection->id }}';
    function rememberStep(i) { try { localStorage.setItem(RESUME_KEY, String(i)); } catch (e) {} }

    function panelDone(p, idx) {
        const steps = p.querySelectorAll('.q-card[data-step]');
        if (steps.length) return Array.from(steps).every(stepAnswered);
        const req = p.querySelector('[data-wreq]');
        return req ? req.value.trim() !== '' : false;
    }

    function recomputeStepper() {
        panels.forEach(function (p, idx) {
            const dot = dots[idx];
            if (!dot) return;
            const done = panelDone(p, idx);
            dot.classList.toggle('done', done && idx !== cur);
            if (lines[idx - 1]) lines[idx - 1].classList.toggle('done', done);
        });
    }

    function showStep(i) {
        cur = Math.max(0, Math.min(i, panels.length - 1));
        rememberStep(cur);
        panels.forEach((p, idx) => p.style.display = idx === cur ? '' : 'none');
        dots.forEach((d, idx) => d.classList.toggle('active', idx === cur));
        // Keep the current bead visible in the horizontal strip.
        if (dots[cur] && dots[cur].scrollIntoView) {
            dots[cur].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
        if (posEl) posEl.textContent = cur + 1;
        if (nameEl) nameEl.textContent = panels[cur].dataset.wname || '';
        prevBtn.disabled = cur === 0;
        const last = cur === panels.length - 1;
        nextBtn.style.display = last ? 'none' : '';
        if (finish) finish.style.display = last ? '' : 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        recomputeStepper();
    }

    prevBtn.addEventListener('click', () => showStep(cur - 1));
    nextBtn.addEventListener('click', () => showStep(cur + 1));
    dots.forEach((d, idx) => d.addEventListener('click', () => showStep(idx)));

    // Jump from a completion chip to that section's wizard step.
    root.querySelectorAll('[data-goto-section]').forEach(function (el) {
        el.addEventListener('click', function () {
            const secId = el.dataset.gotoSection;
            const panel = root.querySelector('[data-wstep] [data-section="' + secId + '"]');
            if (!panel) return;
            const step = panel.closest('[data-wstep]');
            showStep(panels.indexOf(step));
        });
    });

    // Initial paint. Resume on the last-edited card (the early inline script has
    // already shown it pre-paint; this just aligns the wizard state). The target —
    // stay on the card, or advance if it was fully answered — was decided at save
    // time by the submit handler below.
    recompute();
    (function resumeStep() {
        let target = 0;
        try {
            const raw = localStorage.getItem(RESUME_KEY);
            if (raw !== null) {
                const idx = parseInt(raw, 10);
                if (!isNaN(idx) && idx >= 0 && idx < panels.length) target = idx;
            }
        } catch (e) {}
        showStep(target);
    })();

    // Decide the resume target at save time (Save / Complete submit): stay on the
    // current card, or advance to the next when every question on it is answered.
    const wizForm = root.querySelector('form');
    if (wizForm) {
        wizForm.addEventListener('submit', function () {
            let t = cur;
            if (panelDone(panels[cur], cur) && cur < panels.length - 1) t = cur + 1;
            rememberStep(t);
        });
    }

    // ---- Fast Save ---------------------------------------------------------
    // Section/details answers already persist via per-field AJAX autosave, so a
    // full-form submit + full-page reload (re-rendering every card) is wasted
    // work — that was the 3–4s lag. On those cards, just flush any pending
    // autosave and stay in place; only the Verdict card needs a real submit to
    // persist its fields (condition / recommendation / summary / cost).
    const saveBtn = root.querySelector('button[name="complete"][value="0"]');
    if (saveBtn) {
        saveBtn.addEventListener('click', async function (e) {
            const panel = panels[cur];
            if (!panel || panel.dataset.wtype === 'verdict') return;   // allow full submit
            // Changing the Inspection Template must reload so the new checklist's
            // questions render — let that Save go through as a full submit.
            const typeSel = panel.querySelector('select[name="inspection_type_id"]');
            if (typeSel && typeSel.value !== typeSel.dataset.original) return;
            e.preventDefault();
            try {
                if (panel.dataset.wtype === 'details') {
                    if (timers.cust) { clearTimeout(timers.cust); await AA.saveCustomer(); }
                } else {
                    // Flush only steps whose debounced save is still pending.
                    const pending = Array.from(panel.querySelectorAll('.q-card[data-step]'))
                        .filter(s => timers['s' + s.dataset.step]);
                    pending.forEach(s => clearTimeout(timers['s' + s.dataset.step]));
                    await Promise.all(pending.map(s => AA.saveStep(parseInt(s.dataset.step, 10))));
                }
                saved();
                recompute();
                // Same rule as before: advance when the card is fully answered.
                if (panelDone(panel, cur) && cur < panels.length - 1) showStep(cur + 1);
            } catch (err) { failed(); }
        });
    }

    // "Complete Inspection" — confirm with SweetAlert2 before submitting.
    const completeBtn = document.getElementById('btn-complete');
    if (completeBtn) {
        completeBtn.addEventListener('click', function (e) {
            if (completeBtn.dataset.ok === '1' || !window.aaConfirm) return;   // allow submit
            e.preventDefault();
            aaConfirm({
                title: 'Complete inspection?',
                text: 'Mark this inspection as completed. Make sure all mandatory items are filled.',
                icon: 'question', confirmColor: '#34c38f', confirmText: 'Yes, complete'
            }).then(function (r) {
                if (r.isConfirmed) { completeBtn.dataset.ok = '1'; completeBtn.click(); }
            });
        });
    }
})();
</script>
@endsection
