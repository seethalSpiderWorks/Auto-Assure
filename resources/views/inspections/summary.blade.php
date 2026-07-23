@extends('layouts.myfudapp')
@section('content')

@php
    $vehicleName = trim(($inspection->car_make ?? '').' '.($inspection->car_model ?? ''));
    if ($vehicleName === '') { $vehicleName = 'Vehicle'; }
    $cond = strtolower($overview['condition']);
    $condClass = (str_contains($cond,'excellent') || str_contains($cond,'good')) ? 'is-good'
        : (str_contains($cond,'fair') ? 'is-fair'
        : (str_contains($cond,'poor') ? 'is-poor' : 'is-none'));
    $goodCount = collect($sections)->where('status','Completed')->count();
    $warnCount = collect($sections)->where('status','Need Attention')->count();
    $naCount   = collect($sections)->where('status','Not answered')->count();
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Inspection Summary</h4>
                    <div class="page-title-right d-flex align-items-center" style="gap:.5rem;">
                        <a href="{{ route('inspections.edit', $inspection) }}" class="btn btn-sm insp-hlbtn insp-hlbtn--edit"><i class="bx bx-edit"></i> Edit</a>
                        <a href="{{ route('inspections.report', ['inspection' => $inspection, 'download' => 1]) }}" target="_blank" class="btn btn-sm insp-hlbtn insp-hlbtn--report"><i class="bx bx-download"></i> Download Report</a>
                        <ol class="breadcrumb m-0 ms-2">
                            <li class="breadcrumb-item"><a href="{{ url('inspections') }}">Inspections</a></li>
                            <li class="breadcrumb-item active">Summary</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="insp-summary">

            {{-- ===== Top: vehicle + condition side by side ===== --}}
            <div class="row g-3 align-items-stretch">

                {{-- Vehicle --}}
                <div class="col-xl-5 col-lg-6">
                    <div class="insp-card insp-vehicle h-100">
                        <div class="insp-vehicle__img"><i class="bx bxs-car"></i></div>
                        <div class="insp-vehicle__info">
                            <h3>{{ $inspection->car_year ? $inspection->car_year.' ' : '' }}{{ $vehicleName }}</h3>
                            <div class="insp-vehicle__meta">
                                <span><i class="bx bx-user"></i><span class="insp-vehicle__lbl">Owner</span><span class="insp-vehicle__val">{{ $inspection->customer_name ?: 'N/A' }}</span></span>
                                <span><i class="bx bx-phone"></i><span class="insp-vehicle__lbl">Phone</span><span class="insp-vehicle__val">{{ $inspection->customer_phone ?: 'N/A' }}</span></span>
                                @if($inspection->plate_no)<span><i class="bx bx-id-card"></i><span class="insp-vehicle__lbl">Plate No.</span><span class="insp-vehicle__val">{{ $inspection->plate_no }}</span></span>@endif
                                @if($inspection->odometer)<span><i class="bx bx-tachometer"></i><span class="insp-vehicle__lbl">Odometer</span><span class="insp-vehicle__val">{{ number_format($inspection->odometer) }} km</span></span>@endif
                                @if($inspection->fuel_type)<span><i class="bx bx-gas-pump"></i><span class="insp-vehicle__lbl">Fuel</span><span class="insp-vehicle__val">{{ $inspection->fuel_type }}</span></span>@endif
                                @if($inspection->vin)<span><i class="bx bx-barcode"></i><span class="insp-vehicle__lbl">VIN</span><span class="insp-vehicle__val">{{ $inspection->vin }}</span></span>@endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Condition --}}
                <div class="col-xl-7 col-lg-6">
                    <div class="insp-card insp-condition h-100">
                        <div class="insp-condition__gauge">
                            <div class="insp-gauge" style="--pct: {{ $overview['percent'] }}; --gc: {{ ($overview['allAnswered'] ?? ($overview['percent'] >= 100)) ? '#22c55e' : '#f5a623' }};">
                                <span class="insp-gauge__val">{{ $overview['percent'] }}<small>%</small></span>
                                <span class="insp-gauge__sub">{{ $overview['completed'] }} / {{ $overview['total'] }}</span>
                                <span class="insp-gauge__lbl">STEPS COMPLETED</span>
                            </div>
                        </div>
                        <div class="insp-condition__body">
                            <div class="insp-condition__title {{ $condClass }}">{{ $overview['condition'] }} @if($condClass !== 'is-none')Condition @endif</div>
                            <div class="insp-condition__row">
                                <div class="insp-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bx bxs-star {{ $i <= $overview['stars'] ? 'on' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="insp-condition__note">{{ $overview['conditionNote'] }}</p>
                            @if($overview['recommendation'])
                                <p class="insp-condition__rec"><i class="bx bx-check-shield"></i> {{ $overview['recommendation'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Section status tallies ===== --}}
            <div class="row g-3 mt-4">
                <div class="col-md-3 col-6"><div class="insp-stat insp-stat--total"><span class="insp-stat__num">{{ count($sections) }}</span><span class="insp-stat__lbl">Sections</span></div></div>
                <div class="col-md-3 col-6"><div class="insp-stat insp-stat--good"><span class="insp-stat__num">{{ $goodCount }}</span><span class="insp-stat__lbl">Completed</span></div></div>
                <div class="col-md-3 col-6"><div class="insp-stat insp-stat--warn"><span class="insp-stat__num">{{ $warnCount }}</span><span class="insp-stat__lbl">Need Attention</span></div></div>
                <div class="col-md-3 col-6"><div class="insp-stat insp-stat--na"><span class="insp-stat__num">{{ $naCount }}</span><span class="insp-stat__lbl">Not Answered</span></div></div>
            </div>

            <div class="insp-breakdown-head">
                <h5 class="insp-section-title mb-0">INSPECTION BREAKDOWN</h5>
                <div class="insp-legend">
                    <span><i class="dot dot--good"></i> Completed</span>
                    <span><i class="dot dot--warn"></i> Need Attention</span>
                    <span><i class="dot dot--none"></i> Not answered</span>
                </div>
            </div>

            <div class="insp-card insp-tablecard">
                <div class="insp-btable-scroll">
                    <table class="insp-btable">
                        <thead>
                            <tr>
                                <th class="c-no">#</th>
                                <th>Section</th>
                                <th class="c-prog">Progress</th>
                                <th class="c-count">Answered</th>
                                <th class="c-status">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sections as $s)
                                @php
                                    $stClass = $s['status'] === 'Completed' ? 'good' : ($s['status'] === 'Need Attention' ? 'warn' : 'none');
                                    $pct = $s['total'] > 0 ? (int) round($s['answered'] / $s['total'] * 100) : 0;
                                @endphp
                                <tr class="insp-brow insp-brow--{{ $stClass }}">
                                    <td class="c-no"><span class="insp-bno">{{ $s['number'] }}</span></td>
                                    <td class="c-name">
                                        {{ $s['name'] }}
                                        @if(!empty($s['rating']))
                                            <span class="ml-1" title="{{ $s['rating'] }}/5" style="white-space:nowrap;">
                                                @for($i=1;$i<=5;$i++)<span style="color:{{ $i <= $s['rating'] ? '#f1b44c' : '#d3d3d3' }};">★</span>@endfor
                                            </span>
                                        @endif
                                        @if(!empty($s['summary']))
                                            <div class="text-muted font-size-12 mt-1" style="white-space:pre-line;font-weight:400;">{{ $s['summary'] }}</div>
                                        @endif
                                    </td>
                                    <td class="c-prog">
                                        <div class="insp-btprog">
                                            <span class="insp-btbar"><span style="--w: {{ $pct }}%"></span></span>
                                            <span class="insp-btpct">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                    <td class="c-count"><span class="insp-btcount">{{ $s['answered'] }}/{{ $s['total'] }}</span></td>
                                    <td class="c-status">
                                        <span class="insp-btstatus insp-btstatus--{{ $stClass }}">
                                            @if($s['status'] === 'Completed')<i class="bx bxs-check-circle"></i>
                                            @elseif($s['status'] === 'Need Attention')<i class="bx bxs-error"></i>
                                            @else<i class="bx bx-circle"></i>@endif
                                            {{ $s['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No inspection template configured.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@section('css')
<style>
    .btn-brand { background:#00263D; border-color:#00263D; color:#fff; font-weight:600; }
    .btn-brand:hover { background:#04B084; border-color:#04B084; color:#fff; }

    /* Highlighted header action buttons (Edit + Download Report) */
    /* NOTE: @yield('css') loads before bootstrap.min.css in the layout, so these
       selectors are scoped with .btn to out-specify Bootstrap's .btn rules
       (otherwise .btn{display:inline-block} would break the flex alignment). */
    .btn.insp-hlbtn { display:inline-flex !important; align-items:center; justify-content:center; gap:6px;
        line-height:1; font-weight:600; border-radius:9px; padding:8px 16px; transition:all .15s; color:#00263D; }
    .btn.insp-hlbtn i { pointer-events:none; font-size:16px; line-height:1; display:inline-flex;
        align-items:center; vertical-align:middle; }
    /* Edit — soft blue, navy text */
    .btn.insp-hlbtn--edit, .btn.insp-hlbtn--edit:hover { color:#00263D; }
    .btn.insp-hlbtn--edit { background:#dce7fb; border:1px solid #c3d6f7;
        box-shadow:0 3px 9px rgba(47,111,237,.16); }
    .btn.insp-hlbtn--edit:hover { background:#cbdcf8; border-color:#aecbf2;
        box-shadow:0 5px 13px rgba(47,111,237,.22); transform:translateY(-1px); }
    /* Download Report — soft green, navy text */
    .btn.insp-hlbtn--report, .btn.insp-hlbtn--report:hover { color:#00263D; }
    .btn.insp-hlbtn--report { background:#d3f1e5; border:1px solid #b6e6d3;
        box-shadow:0 3px 9px rgba(4,176,132,.16); }
    .btn.insp-hlbtn--report:hover { background:#c2ecda; border-color:#a1dec4;
        box-shadow:0 5px 13px rgba(4,176,132,.22); transform:translateY(-1px); }
    .insp-summary { padding-bottom: 48px; }
    .insp-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 18px rgba(16,40,70,.06); padding: 26px 28px; }

    /* Vehicle */
    .insp-vehicle { display: flex; align-items: center; gap: 22px; }
    .insp-vehicle__img { flex: 0 0 120px; height: 96px; display: flex; align-items: center; justify-content: center; font-size: 68px; color: #101828; }
    .insp-vehicle__info h3 { font-size: 24px; font-weight: 700; margin: 0 0 12px; color: #101828; }
    .insp-vehicle__meta { display: flex; flex-wrap: wrap; gap: 8px 16px; }
    .insp-vehicle__meta > span { display: inline-flex; align-items: center; gap: 5px; color: #475467; font-size: 11px;
        background: #f7f9fc; border: 1px solid #eef1f5; border-radius: 7px; padding: 4px 9px; max-width: 100%; }
    .insp-vehicle__meta > span > i { flex: 0 0 auto; color: #04B084; font-size: 14px; }
    .insp-vehicle__lbl { flex: 0 0 auto; color: #98a2b3; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
    .insp-vehicle__val { color: #101828; font-weight: 600; font-size: 11px; word-break: break-word; overflow-wrap: anywhere; }

    /* Condition */
    .insp-condition { display: flex; align-items: center; gap: 28px; background: #00263D; color: #fff; }
    .insp-gauge {
        --size: 150px;
        width: var(--size); height: var(--size); border-radius: 50%;
        display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; flex: 0 0 auto;
        background:
            radial-gradient(closest-side, #00263D 78%, transparent 79%),
            conic-gradient(var(--gc, #04B084) calc(var(--pct) * 1%), rgba(255,255,255,.14) 0);
    }
    .insp-gauge__val { font-size: 30px; font-weight: 700; line-height: 1; }
    .insp-gauge__val small { font-size: 15px; }
    .insp-gauge__sub { font-size: 13px; opacity: .8; margin-top: 4px; }
    .insp-gauge__lbl { font-size: 9.5px; letter-spacing: .3px; opacity: .6; margin-top: 4px; white-space: nowrap; }
    .insp-condition__body { flex: 1; }
    .insp-condition__title { font-size: 24px; font-weight: 700; margin-bottom: 10px; }
    .insp-condition__title.is-good { color: #17BC8D; }
    .insp-condition__title.is-fair { color: #fbbf24; }
    .insp-condition__title.is-poor { color: #f87171; }
    .insp-condition__title.is-none { color: #cbd5e1; }
    .insp-condition__row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .insp-badge { display: inline-block; font-size: 12px; padding: 5px 14px; border-radius: 20px; }
    .insp-badge--warn { background: rgba(255,255,255,.12); color: #fff; }
    .insp-badge--ok { background: rgba(52,211,153,.18); color: #17BC8D; }
    .insp-stars .bxs-star { color: rgba(255,255,255,.25); font-size: 18px; }
    .insp-stars .bxs-star.on { color: #f5a623; }
    .insp-condition__note { font-size: 13.5px; opacity: .85; margin: 12px 0 0; }
    .insp-condition__rec { font-size: 13.5px; margin: 8px 0 0; color: #17BC8D; }

    /* Stat tiles */
    .insp-stat { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(16,40,70,.06); padding: 20px 22px; display: flex; flex-direction: column; border-left: 4px solid #e2e6ec; }
    .insp-stat__num { font-size: 26px; font-weight: 700; line-height: 1; color: #101828; }
    .insp-stat__lbl { font-size: 12px; color: #667085; margin-top: 4px; }
    .insp-stat--good { border-left-color: #04B084; }
    .insp-stat--warn { border-left-color: #f5a623; }
    .insp-stat--na { border-left-color: #cbd5e1; }
    .insp-stat--total { border-left-color: #00263D; }

    /* Breakdown — desktop-friendly row list (auto multi-column) */
    .insp-breakdown-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin: 36px 2px 16px; }
    .insp-section-title { font-size: 14px; font-weight: 700; letter-spacing: .5px; color: #101828; }
    .insp-legend { display: flex; gap: 18px; font-size: 12px; color: #667085; }
    .insp-legend .dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; margin-right: 5px; vertical-align: middle; }
    .insp-legend .dot--good { background: #04B084; }
    .insp-legend .dot--warn { background: #f5a623; }
    .insp-legend .dot--none { background: #cbd5e1; }

    /* Data table */
    .insp-tablecard { padding: 6px 6px; }
    .insp-btable-scroll { overflow-x: auto; }
    .insp-btable { width: 100%; border-collapse: separate; border-spacing: 0; }
    .insp-btable thead th { position: sticky; top: 0; background: #f7f9fc; text-transform: uppercase; font-size: 11px; letter-spacing: .4px;
        color: #98a2b3; font-weight: 700; padding: 12px 16px; text-align: left; white-space: nowrap; z-index: 1; }
    .insp-btable thead th:first-child { border-radius: 10px 0 0 10px; }
    .insp-btable thead th:last-child  { border-radius: 0 10px 10px 0; }
    .insp-btable tbody td { padding: 12px 16px; border-bottom: 1px solid #f0f2f6; vertical-align: middle; font-size: 13.5px; }
    .insp-btable tbody tr:last-child td { border-bottom: 0; }
    .insp-brow { transition: background .12s; }
    .insp-brow:hover td { background: #fafcff; }
    .insp-brow td.c-name { font-weight: 600; color: #344054; }

    .c-no { width: 56px; }
    .c-prog { width: 22%; }
    .c-count { width: 96px; white-space: nowrap; }
    .c-status { width: 160px; }

    .insp-bno { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; padding: 2px 8px; border-radius: 6px;
        background: #f2f5f8; color: #667085; font-size: 11.5px; font-weight: 700; }
    .insp-btprog { display: flex; align-items: center; gap: 10px; }
    .insp-btbar { flex: 1 1 auto; height: 5px; border-radius: 5px; background: #eef1f5; overflow: hidden; min-width: 60px; }
    .insp-btbar span { display: block; height: 100%; width: 0; border-radius: 5px; background: #cbd5e1; animation: inspFill .9s ease-out forwards; }
    .insp-brow--good .insp-btbar span { background: #04B084; }
    .insp-brow--warn .insp-btbar span { background: #f5a623; }
    .insp-btpct { flex: 0 0 auto; font-size: 12px; font-weight: 700; color: #98a2b3; min-width: 36px; text-align: right; }
    .insp-brow--good .insp-btpct { color: #04B084; }
    .insp-brow--warn .insp-btpct { color: #d98a12; }
    .insp-btcount { font-weight: 700; color: #667085; }

    .insp-btstatus { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px; white-space: nowrap; }
    .insp-btstatus i { font-size: 14px; }
    .insp-btstatus--good { background: #e7f8ef; color: #04B084; }
    .insp-btstatus--warn { background: #fff4e0; color: #d98a12; }
    .insp-btstatus--none { background: #eef1f5; color: #8a94a6; }
    @keyframes inspFill { from { width: 0; } to { width: var(--w); } }

    @media (max-width: 991px) {
        .insp-condition { flex-direction: column; text-align: center; }
        .insp-condition__row { justify-content: center; }
    }
    @media (max-width: 575px) {
        .insp-vehicle { flex-direction: column; text-align: center; }
        .insp-vehicle__meta { justify-content: center; }
        .insp-btable .c-count, .insp-btable thead .c-count { display: none; }
    }
</style>
@endsection
