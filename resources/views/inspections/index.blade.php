@extends('layouts.myfudapp')
@section('content')

@php
    $statusMeta = [
        'pending'     => ['Pending', 'is-pending'],
        'in_progress' => ['In Progress', 'is-progress'],
        'completed'   => ['Completed', 'is-completed'],
    ];
    $hasFilters = request()->hasAny(['q', 'status', 'technician_id', 'from', 'to'])
        && collect(request()->only(['q','status','technician_id','from','to']))->filter()->isNotEmpty();
@endphp

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Inspections</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Inspections</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        {{-- ===== Filter bar ===== --}}
        <form method="GET" class="insp-filterbar" id="inspFilters">
            <div class="insp-filterbar__search">
                <i class="bx bx-search"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search customer, vehicle, phone, reference…" autocomplete="off">
            </div>

            <div class="insp-ctrl">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    @foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if(($technicians ?? collect())->isNotEmpty())
                <div class="insp-ctrl">
                    <label>Technician</label>
                    <select name="technician_id" onchange="this.form.submit()">
                        <option value="">All technicians</option>
                        @foreach ($technicians as $tech)
                            <option value="{{ $tech->id }}" @selected((string) request('technician_id') === (string) $tech->id)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="insp-ctrl insp-ctrl--dates">
                <label>Scheduled date</label>
                <div class="insp-daterange" id="inspDateRange">
                    <i class="bx bx-calendar"></i>
                    <input type="text" id="insp_daterange" class="insp-daterange__field"
                           placeholder="Select start &amp; end date" autocomplete="off" readonly
                           aria-label="Scheduled date range">
                    <button type="button" id="insp_daterange_clear" class="insp-daterange__clear"
                            title="Clear dates" aria-label="Clear dates"
                            @style(['display:none' => !request('from') && !request('to')])>&times;</button>
                </div>
                {{-- Hidden fields keep the backend contract (from / to) unchanged --}}
                <input type="hidden" name="from" id="insp_from" value="{{ request('from') }}">
                <input type="hidden" name="to" id="insp_to" value="{{ request('to') }}">
            </div>

            <div class="insp-filterbar__actions">
                <button type="submit" class="insp-apply"><i class="bx bx-filter-alt"></i> Filter</button>
                @if ($hasFilters)
                    <a href="{{ route('inspections.index') }}" class="insp-resetbtn"><i class="bx bx-x"></i> Reset</a>
                @endif
            </div>
        </form>

        {{-- ===== Table card ===== --}}
        <div class="card insp-card-modern">
            <div class="card-body">

                <div class="insp-tablehead">
                    <span class="insp-count">{{ $inspections->total() }} {{ \Illuminate\Support\Str::plural('inspection', $inspections->total()) }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table insp-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Vehicle</th>
                                <th>Technician</th>
                                <th>Scheduled</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inspections as $inspection)
                                @php
                                    $name = optional($inspection->lead)->customer_name ?? $inspection->customer_name ?? '—';
                                    $phone = $inspection->customer_phone;
                                    $initials = collect(explode(' ', trim($name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: '?';
                                    [$stLabel, $stClass] = $statusMeta[$inspection->status] ?? [ucfirst(str_replace('_',' ',$inspection->status)), 'is-pending'];
                                    $vehicle = $inspection->car();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="insp-cust">
                                            <span class="insp-avatar">{{ $initials }}</span>
                                            <div class="insp-cust__meta">
                                                <a href="{{ route('inspections.show', $inspection) }}" class="insp-cust__name">{{ $name }}</a>
                                                <span class="insp-cust__ref">{{ optional($inspection->lead)->reference ?? ('#'.$inspection->id) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($phone)
                                            <a href="tel:{{ $phone }}" class="insp-cust__phone"><i class="bx bx-phone"></i> {{ $phone }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vehicle)
                                            <span class="insp-vehicle"><i class="bx bxs-car"></i> {{ $vehicle }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(optional($inspection->technician)->name)
                                            <span class="insp-tech"><i class="bx bx-user"></i> {{ $inspection->technician->name }}</span>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inspection->scheduled_at)
                                            <span class="insp-date">{{ $inspection->scheduled_at->format('d M Y') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="insp-status {{ $stClass }}">{{ $stLabel }}</span></td>
                                    <td class="text-end">
                                        <div class="insp-actions">
                                            <a href="{{ route('inspections.show', $inspection) }}" class="insp-act insp-act--view" title="View details" data-bs-toggle="tooltip"><i class="bx bx-show"></i></a>
                                            <a href="{{ route('inspections.edit', $inspection) }}" class="insp-act insp-act--edit" title="Open / Edit" data-bs-toggle="tooltip"><i class="bx bx-edit-alt"></i></a>
                                            <a href="{{ route('inspections.summary', $inspection) }}" target="_blank" class="insp-act insp-act--summary" title="Summary" data-bs-toggle="tooltip"><i class="bx bx-list-check"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="insp-empty">
                                            <i class="bx bx-clipboard"></i>
                                            <p>No inspections found{{ $hasFilters ? ' for these filters' : '' }}.</p>
                                            @if($hasFilters)<a href="{{ route('inspections.index') }}" class="btn btn-sm btn-light">Clear filters</a>@endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($inspections->hasPages())
                    <div class="mt-3">{{ $inspections->links('pagination::bootstrap-4') }}</div>
                @endif

            </div>
        </div>

    </div>
</div>

@include('partials._notify')
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
<style>
    /* ===== Modern filter bar ===== */
    .insp-filterbar { display:flex; flex-wrap:wrap; align-items:flex-end; gap:14px; background:#fff;
        border:1px solid #eef1f5; border-left:4px solid #04B084; border-radius:16px;
        box-shadow:0 6px 24px rgba(16,40,70,.07); padding:16px 18px; margin-bottom:20px; }
    .insp-filterbar__search { position:relative; flex:1 1 180px; min-width:160px; }
    .insp-filterbar__search i { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:18px; color:#98a2b3; }
    .insp-filterbar__search input { width:100%; height:42px; border:1px solid #e2e6ec; border-radius:12px; padding:0 14px 0 40px; font-size:14px; outline:none; transition:.15s; }
    .insp-filterbar__search input:focus { border-color:#04B084; box-shadow:0 0 0 3px rgba(4,176,132,.14); }

    .insp-ctrl { display:flex; flex-direction:column; gap:5px; }
    .insp-ctrl > label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#98a2b3; margin:0; }
    .insp-ctrl select { height:42px; border:1px solid #e2e6ec; border-radius:10px; padding:0 12px; font-size:13.5px; color:#344054; background:#fff; min-width:155px; cursor:pointer; transition:.15s; }
    .insp-ctrl select:focus { border-color:#04B084; box-shadow:0 0 0 3px rgba(4,176,132,.14); outline:none; }

    /* grouped from -> to date pill */
    .insp-daterange { display:inline-flex; align-items:center; gap:6px; height:42px; border:1px solid #e2e6ec;
        border-radius:10px; padding:0 10px; background:#fff; transition:.15s; }
    .insp-daterange > i { color:#04B084; font-size:17px; }
    .insp-daterange__field { border:0; outline:none; font-size:13px; width:290px; background:transparent; color:#344054; cursor:pointer; }
    .insp-daterange__field::placeholder { color:#98a2b3; }
    .insp-daterange__clear { border:0; background:transparent; color:#98a2b3; font-size:20px; line-height:1;
        cursor:pointer; padding:0 2px; transition:.15s; }
    .insp-daterange__clear:hover { color:#e5484d; }
    .insp-daterange__arrow { color:#b0b8c4; font-weight:700; }
    .insp-daterange:focus-within { border-color:#04B084; box-shadow:0 0 0 3px rgba(4,176,132,.14); }
    /* Flatpickr range highlight in brand colour */
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange,
    .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover {
        background:#04B084; border-color:#04B084; }
    .flatpickr-day.inRange { background:#e7f8ef; border-color:#e7f8ef; box-shadow:-5px 0 0 #e7f8ef,5px 0 0 #e7f8ef; }
    .flatpickr-day.today { border-color:#04B084; }
    .insp-daterange.is-invalid { border-color:#e5484d; box-shadow:0 0 0 3px rgba(229,72,77,.12); }
    .insp-daterange__err { flex-basis:100%; color:#e5484d; font-size:12px; margin-top:2px; }
    .insp-daterange__err:empty { display:none; }

    .insp-filterbar__actions { display:flex; gap:8px; margin-left:auto; }
    .insp-apply { height:42px; background:#00263D; border:1px solid #00263D; color:#fff; font-weight:600; border-radius:10px;
        display:inline-flex; align-items:center; gap:6px; padding:0 20px; cursor:pointer; transition:.15s; }
    .insp-apply:hover { background:#04B084; border-color:#04B084; }
    .insp-resetbtn { height:42px; background:#f2f5f8; border:1px solid #e6eaf0; color:#5b6472; font-weight:600; border-radius:10px;
        display:inline-flex; align-items:center; gap:6px; padding:0 16px; text-decoration:none; transition:.15s; }
    .insp-resetbtn:hover { background:#fdecec; border-color:#f6c6c8; color:#e5484d; }
    .insp-apply i, .insp-resetbtn i { pointer-events:none; }

    @media (max-width:767px){
        .insp-filterbar__actions { margin-left:0; width:100%; }
        .insp-filterbar__search, .insp-ctrl { flex:1 1 100%; }
        .insp-ctrl select, .insp-daterange { width:100%; }
        .insp-apply, .insp-resetbtn { flex:1 1 0; justify-content:center; }
    }

    /* Table card */
    .insp-card-modern { border:0; border-radius:16px; box-shadow:0 4px 18px rgba(16,40,70,.06); }
    .insp-tablehead { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
    .insp-count { font-size:13px; font-weight:600; color:#667085; background:#f2f5f8; padding:4px 12px; border-radius:20px; }

    .insp-table thead th { border:0; text-transform:uppercase; font-size:11.5px; letter-spacing:.4px; color:#98a2b3; font-weight:700; padding:10px 14px; background:#f7f9fc; }
    .insp-table thead th:first-child { border-radius:10px 0 0 10px; }
    .insp-table thead th:last-child { border-radius:0 10px 10px 0; }
    .insp-table tbody td { border:0; border-bottom:1px solid #f0f2f6; padding:14px; vertical-align:middle; }
    .insp-table tbody tr:hover td { background:#fafcff; }

    .insp-cust { display:flex; align-items:center; gap:12px; }
    .insp-avatar { flex:0 0 auto; width:40px; height:40px; border-radius:11px; background:linear-gradient(135deg,#00263D,#04B084); color:#fff; font-size:14px; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .insp-cust__meta { display:flex; flex-direction:column; min-width:0; }
    .insp-cust__name { font-size:14px; font-weight:600; color:#1f2a37; text-decoration:none; }
    .insp-cust__name:hover { color:#04B084; }
    .insp-cust__ref { font-size:12px; color:#98a2b3; }
    .insp-cust__phone { font-size:12.5px; color:#667085; text-decoration:none; display:inline-flex; align-items:center; gap:4px; margin-top:1px; width:fit-content; }
    .insp-cust__phone i { color:#04B084; font-size:14px; }
    .insp-cust__phone:hover { color:#04B084; }
    .insp-vehicle, .insp-tech { font-size:13.5px; color:#475467; display:inline-flex; align-items:center; gap:6px; }
    .insp-vehicle i, .insp-tech i { color:#04B084; font-size:16px; }
    .insp-date { font-size:13.5px; color:#344054; display:flex; flex-direction:column; line-height:1.25; }
    .insp-date small { color:#98a2b3; font-size:11.5px; }

    .insp-status { font-size:12px; font-weight:600; padding:5px 13px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; }
    .insp-status::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; }
    .insp-status.is-completed { background:#e7f8ef; color:#04B084; }
    .insp-status.is-progress  { background:#fff4e0; color:#d98a12; }
    .insp-status.is-pending   { background:#eef1f5; color:#5b6472; }

    .insp-actions { display:inline-flex; gap:8px; justify-content:flex-end; }
    .insp-act { width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; font-size:18px; text-decoration:none; transition:transform .12s, box-shadow .12s, background .12s, color .12s; }
    .insp-act:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(16,40,70,.16); }
    .insp-act--view    { background:#eef3f8; color:#00263D; }
    .insp-act--edit    { background:#eaf1ff; color:#2a5bd7; }
    .insp-act--summary { background:#e7f8ef; color:#04B084; }
    .insp-act--view:hover    { background:#00263D; color:#fff; }
    .insp-act--edit:hover    { background:#2a5bd7; color:#fff; }
    .insp-act--summary:hover { background:#04B084; color:#fff; }

    .insp-empty { text-align:center; padding:38px 10px; color:#98a2b3; }
    .insp-empty i { font-size:44px; display:block; margin-bottom:8px; opacity:.6; }
    .insp-empty p { margin:0 0 12px; }

    @media (max-width: 767px) {
        .insp-filters__actions { margin-left:0; width:100%; }
        .insp-filters__search { flex:1 1 100%; }
    }
</style>
@endsection

@section('js')
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        if (window.bootstrap && bootstrap.Tooltip) new bootstrap.Tooltip(el);
    });

    // Scheduled date range: a single Flatpickr calendar. Click a start date then an
    // end date to pick the whole range at once. The chosen dates are mirrored into
    // the hidden `from`/`to` fields (Y-m-d) that the backend already understands.
    (function () {
        if (!window.flatpickr) return;

        var fromInput = document.getElementById('insp_from');
        var toInput   = document.getElementById('insp_to');
        var clearBtn  = document.getElementById('insp_daterange_clear');

        var defaults = [];
        if (fromInput.value) defaults.push(fromInput.value);
        if (toInput.value)   defaults.push(toInput.value);

        var fp = flatpickr('#insp_daterange', {
            mode: 'range',
            dateFormat: 'Y-m-d',        // internal value format
            altInput: true,             // pretty, read-only display
            altFormat: 'd M Y',
            defaultDate: defaults,
            onChange: function (dates) {
                fromInput.value = dates[0] ? flatpickr.formatDate(dates[0], 'Y-m-d') : '';
                // Start-date only: filters everything on/after that date (see controller).
                toInput.value   = dates[1] ? flatpickr.formatDate(dates[1], 'Y-m-d') : '';
                clearBtn.style.display = (dates[0] || dates[1]) ? '' : 'none';
            },
            onClose: function (dates, str, instance) {
                // Flatpickr wipes an "incomplete" single-date range on close. If the
                // user only picked a start date, re-apply it so the start-date-only
                // filter sticks (both visibly and in the hidden `from` field).
                if (fromInput.value && !toInput.value && dates.length < 2) {
                    instance.setDate(fromInput.value, false);
                    clearBtn.style.display = '';
                }
            },
        });

        clearBtn.addEventListener('click', function () {
            fp.clear();
            fromInput.value = '';
            toInput.value   = '';
            clearBtn.style.display = 'none';
        });
    })();
</script>
@endsection
