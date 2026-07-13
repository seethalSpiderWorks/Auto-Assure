@extends('layouts.myfudapp')
@section('css')
<style>
    :root { --d-dark:#00263D; --d-brand:#04B084; --d-brand2:#17BC8D; }

    /* Hero */
    .dash-hero { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
        background:linear-gradient(120deg,#00263D 0%,#06655a 60%,#04B084 130%); color:#fff;
        border-radius:18px; padding:20px 26px; margin-bottom:20px; box-shadow:0 14px 34px rgba(0,38,61,.20);
        position:relative; overflow:hidden; }
    .dash-hero::after { content:''; position:absolute; right:-40px; top:-60px; width:210px; height:210px; border-radius:50%; background:rgba(255,255,255,.06); }
    .dash-hero__eyebrow { font-size:12px; text-transform:uppercase; letter-spacing:.5px; opacity:.85; }
    .dash-hero__title { font-size:23px; font-weight:800; margin:2px 0; color:#fff; }
    .dash-hero__sub { margin:0; font-size:13px; opacity:.85; }
    .dash-hero__date { background:rgba(255,255,255,.14); border-radius:10px; padding:9px 14px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px; z-index:1; }

    /* KPI tiles */
    .kpi { border:0; border-radius:16px; background:#fff; box-shadow:0 4px 20px rgba(16,40,70,.06); padding:16px 16px; height:100%;
        display:flex; align-items:center; gap:13px; transition:transform .12s, box-shadow .12s; }
    .kpi:hover { transform:translateY(-3px); box-shadow:0 10px 26px rgba(16,40,70,.12); }
    .kpi__ico { width:50px; height:50px; border-radius:14px; flex:0 0 auto; display:flex; align-items:center; justify-content:center; font-size:25px; color:#fff; }
    .kpi__val { font-size:25px; font-weight:800; color:var(--d-dark); line-height:1; }
    .kpi__lbl { font-size:12px; color:#98a2b3; font-weight:600; margin-top:4px; }
    .kpi--leads  .kpi__ico { background:linear-gradient(135deg,#00263D,#0b466b); }
    .kpi--month  .kpi__ico { background:linear-gradient(135deg,#04B084,#17BC8D); }
    .kpi--follow .kpi__ico { background:linear-gradient(135deg,#f1b44c,#f6c76b); }
    .kpi--assign .kpi__ico { background:linear-gradient(135deg,#2a5bd7,#4f7ef0); }
    .kpi--insp   .kpi__ico { background:linear-gradient(135deg,#6f42c1,#8e5fd8); }

    /* Cards */
    .dash-card { border:0; border-radius:16px; box-shadow:0 4px 20px rgba(16,40,70,.06); }
    .dash-card .card-header { background:transparent; border-bottom:1px solid #eef1f5; padding:14px 18px; display:flex; align-items:center; justify-content:space-between; }
    .dash-card .card-title { margin:0; font-size:15px; font-weight:700; color:var(--d-dark); }
    .dash-card .card-title i { color:var(--d-brand); }
    .dash-viewall { background:var(--d-dark); color:#fff; border-radius:8px; font-weight:600; font-size:12.5px; padding:6px 14px; text-decoration:none; }
    .dash-viewall:hover { background:var(--d-brand); color:#fff; }

    /* Latest inspections table */
    .dash-table { width:100%; margin:0; }
    .dash-table thead th { text-transform:uppercase; font-size:11px; letter-spacing:.4px; color:#98a2b3; font-weight:700; padding:11px 16px; border-bottom:1px solid #eef1f5; background:#f7f9fc; }
    .dash-table tbody td { padding:11px 16px; border-bottom:1px solid #f4f6f9; font-size:13.5px; color:#344054; vertical-align:middle; white-space:nowrap; }
    .dash-table tbody tr:last-child td { border-bottom:0; }
    .dash-table tbody tr:hover td { background:#fafcff; }
    .d-avatar { width:34px; height:34px; border-radius:9px; background:linear-gradient(135deg,#00263D,#04B084); color:#fff; font-weight:700; font-size:13px; display:inline-flex; align-items:center; justify-content:center; margin-right:9px; }
    .dash-insp-link { color:#344054; text-decoration:none; display:inline-flex; align-items:center; font-weight:600; }
    .dash-insp-link:hover { color:#04B084; text-decoration:none; }
    .d-status { font-size:11.5px; font-weight:600; padding:4px 11px; border-radius:20px; display:inline-flex; align-items:center; gap:5px; }
    .d-status::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; }
    .d-status.is-completed { background:#e7f8ef; color:#04B084; }
    .d-status.is-progress  { background:#fff4e0; color:#d98a12; }
    .d-status.is-pending   { background:#eef1f5; color:#5b6472; }

    /* Inspection status breakdown */
    .ins-bar { display:flex; height:10px; border-radius:20px; overflow:hidden; background:#eef1f5; }
    .ins-stat { display:flex; align-items:center; justify-content:space-between; padding:11px 2px; border-bottom:1px dashed #eef1f5; }
    .ins-stat:last-child { border-bottom:0; }
    .ins-stat__label { font-size:13.5px; color:#475467; display:flex; align-items:center; gap:9px; }
    .ins-dot { width:10px; height:10px; border-radius:50%; }
    .ins-stat__val { font-weight:800; color:var(--d-dark); font-size:16px; }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- ===== Hero ===== --}}
        <div class="dash-hero">
            <div style="z-index:1">
                <div class="dash-hero__eyebrow">Dashboard</div>
                <h4 class="dash-hero__title">Welcome back{{ Auth::user()->name ? ', '.Auth::user()->name : '' }}</h4>
                <p class="dash-hero__sub">Here's what's happening at Auto Assure</p>
            </div>
            <div class="dash-hero__date"><i class="bx bx-calendar"></i> {{ date('l, d M Y') }}</div>
        </div>

        {{-- ===== KPI tiles ===== --}}
        <div class="row g-3">
            @unless($isTechnician)
                {{-- Lead metrics are hidden for technicians --}}
                <div class="col-6 col-md-4 col-xl">
                    <a href="{{ url('leadslist') }}" class="text-decoration-none">
                        <div class="kpi kpi--leads">
                            <span class="kpi__ico"><i class="bx bx-user-voice"></i></span>
                            <div><div class="kpi__val" data-plugin="counterup">{{ $lead->count }}</div><div class="kpi__lbl">Total Leads</div></div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                        <div class="kpi kpi--month">
                            <span class="kpi__ico"><i class="bx bx-trending-up"></i></span>
                            <div><div class="kpi__val" data-plugin="counterup">{{ $data->countFM }}</div><div class="kpi__lbl">Leads This Month</div></div>
                        </div>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <a href="{{ url('myfollowup') }}" class="text-decoration-none">
                        <div class="kpi kpi--follow">
                            <span class="kpi__ico"><i class="bx bx-phone-call"></i></span>
                            <div><div class="kpi__val" data-plugin="counterup">{{ $followup }}</div><div class="kpi__lbl">Followups</div></div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-xl">
                        <div class="kpi kpi--assign">
                            <span class="kpi__ico"><i class="bx bx-user-check"></i></span>
                            <div><div class="kpi__val" data-plugin="counterup">{{ $lead->lead_assigned_count }}</div><div class="kpi__lbl">Assigned</div></div>
                        </div>
                </div>
            @else
                {{-- Technician view: assigned + total inspections only --}}
                <div class="col-6 col-md-4 col-xl">
                    <a href="{{ url('inspections') }}" class="text-decoration-none">
                        <div class="kpi kpi--assign">
                            <span class="kpi__ico"><i class="bx bx-user-check"></i></span>
                            <div><div class="kpi__val" data-plugin="counterup">{{ $assignedInspections }}</div><div class="kpi__lbl">Assigned Inspections</div></div>
                        </div>
                    </a>
                </div>
            @endunless
            <div class="col-6 col-md-4 col-xl">
                <a href="{{ url('inspections') }}" class="text-decoration-none">
                    <div class="kpi kpi--insp">
                        <span class="kpi__ico"><i class="bx bx-clipboard"></i></span>
                        <div><div class="kpi__val" data-plugin="counterup">{{ (int) ($inspStats->total ?? 0) }}</div><div class="kpi__lbl">{{ $isTechnician ? 'Total Inspections' : 'Inspections' }}</div></div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ===== Inspection status + Latest inspections ===== --}}
        @php
            $it  = (int) ($inspStats->total ?? 0);
            $ip  = (int) ($inspStats->pending ?? 0);
            $ipr = (int) ($inspStats->in_progress ?? 0);
            $ic  = (int) ($inspStats->completed ?? 0);
        @endphp
        <div class="row g-3 mt-1">

            <div class="col-xl-4">
                <div class="card dash-card h-100">
                    <div class="card-header"><h5 class="card-title"><i class="bx bx-pie-chart-alt-2"></i> Inspection Status</h5></div>
                    <div class="card-body">
                        <div class="ins-bar mb-3">
                            <div style="width:{{ $it ? $ic/$it*100 : 0 }}%;background:#04B084"></div>
                            <div style="width:{{ $it ? $ipr/$it*100 : 0 }}%;background:#f1b44c"></div>
                            <div style="width:{{ $it ? $ip/$it*100 : 0 }}%;background:#c7ccd6"></div>
                        </div>
                        <div class="ins-stat"><span class="ins-stat__label"><span class="ins-dot" style="background:#04B084"></span> Completed</span><span class="ins-stat__val">{{ $ic }}</span></div>
                        <div class="ins-stat"><span class="ins-stat__label"><span class="ins-dot" style="background:#f1b44c"></span> In Progress</span><span class="ins-stat__val">{{ $ipr }}</span></div>
                        <div class="ins-stat"><span class="ins-stat__label"><span class="ins-dot" style="background:#c7ccd6"></span> Pending</span><span class="ins-stat__val">{{ $ip }}</span></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card dash-card h-100">
                    <div class="card-header">
                        <h5 class="card-title"><i class="bx bx-time-five"></i> Latest Inspections</h5>
                        <a href="{{ url('inspections') }}" class="dash-viewall">View all</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Customer</th><th>Vehicle</th><th>Technician</th><th>Scheduled</th><th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestInspections as $insp)
                                        @php
                                            $nm  = optional($insp->lead)->customer_name ?? $insp->customer_name ?? '—';
                                            $ini = strtoupper(mb_substr(trim($nm), 0, 1)) ?: '?';
                                            $veh = trim(($insp->car_year ? $insp->car_year.' ' : '').($insp->car_make ?? '').' '.($insp->car_model ?? ''));
                                            $stMap = ['completed'=>['Completed','is-completed'],'in_progress'=>['In Progress','is-progress'],'pending'=>['Pending','is-pending']];
                                            [$stLbl,$stCls] = $stMap[$insp->status] ?? [ucfirst(str_replace('_',' ',$insp->status)),'is-pending'];
                                        @endphp
                                        <tr>
                                            <td><a href="{{ route('inspections.show', $insp->id) }}" class="dash-insp-link"><span class="d-avatar">{{ $ini }}</span> {{ $nm }}</a></td>
                                            <td>{{ $veh ?: '—' }}</td>
                                            <td>{{ optional($insp->technician)->name ?? 'Unassigned' }}</td>
                                            <td>{{ $insp->scheduled_at ? $insp->scheduled_at->format('d M Y') : '—' }}</td>
                                            <td><span class="d-status {{ $stCls }}">{{ $stLbl }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">No inspections yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
