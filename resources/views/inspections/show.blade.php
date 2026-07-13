@extends('layouts.myfudapp')
@section('content')

@php
    use App\Models\Inspection;

    $vehicleName = trim(($inspection->car_make ?? '').' '.($inspection->car_model ?? ''));
    if ($vehicleName === '') { $vehicleName = $inspection->manufacturer_name ?: 'Vehicle'; }

    $statusMap = [
        'pending'     => ['Pending', 'is-pending'],
        'in_progress' => ['In Progress', 'is-progress'],
        'completed'   => ['Completed', 'is-completed'],
    ];
    [$statusLabel, $statusClass] = $statusMap[$inspection->status] ?? [ucfirst($inspection->status), 'is-pending'];

    $condition = Inspection::CONDITIONS[$inspection->overall_condition] ?? null;
    $recommend = Inspection::RECOMMENDATIONS[$inspection->recommendation] ?? null;

    // Pass / Fail / N-A for a saved answer (same rule as the report).
    $stateOf = function ($d) {
        if (! $d) return 'na';
        if (in_array($d->choice, ['Pass','Yes'], true) || ($d->rating !== null && $d->rating >= 3)) return 'pass';
        if (in_array($d->choice, ['Fail','No'], true) || ($d->rating !== null && $d->rating < 3)) return 'fail';
        return 'na';
    };

    // Vehicle specification rows (only those with a value).
    $spec = array_filter([
        'VIN'                => $inspection->vin,
        'Registration No'    => $inspection->registration_number,
        'Manufacturer'       => $inspection->manufacturer_name,
        'Variant'            => $inspection->variant,
        'Year'               => $inspection->car_year,
        'Colour'             => $inspection->color,
        'Body Type'          => $inspection->body_type,
        'Vehicle Type'       => $inspection->vehicle_type,
        'Fuel Type'          => $inspection->fuel_type,
        'Transmission'       => $inspection->transmission,
        'Cylinders / CC'     => $inspection->cylinders_cc,
        'Motor Power'        => $inspection->motor_power_kw ? $inspection->motor_power_kw.' kW' : null,
        'Passengers'         => $inspection->passengers,
        'No. of Keys'        => $inspection->number_of_keys,
        'Odometer'           => $inspection->odometer ? number_format($inspection->odometer).' km' : null,
        'Fuel Economy'       => $inspection->fuel_economy,
        'Country of Origin'  => $inspection->country_of_origin,
        'Country of Export'  => $inspection->country_of_export,
    ], fn ($v) => $v !== null && $v !== '');

    // Flat gallery of every media item, in checklist order, for the lightbox.
    $gallery = [];
    foreach ($inspection->type?->sections ?? [] as $gsi => $gsection) {
        foreach ($gsection->steps as $gstep) {
            $gd = $answers->get($gstep->id);
            if (! $gd) continue;
            foreach ($gd->media as $gm) {
                $gallery[] = [
                    'id'      => $gm->id,
                    'type'    => $gm->type,
                    'url'     => $gm->url,
                    'caption' => ($gsi + 1).'. '.$gsection->section_name.' — '.$gstep->question,
                ];
            }
        }
    }
    // Additional (step-less) media bucket.
    $extraDetail = $inspection->details->first(fn ($d) => is_null($d->inspection_step_id));
    $extraMedia = $extraDetail ? $extraDetail->media : collect();
    foreach ($extraMedia as $gm) {
        $gallery[] = ['id' => $gm->id, 'type' => $gm->type, 'url' => $gm->url, 'caption' => $gm->label ?: 'Additional media'];
    }

    $mediaIndex = [];
    foreach ($gallery as $gi => $gitem) { $mediaIndex[$gitem['id']] = $gi; }
    $allMedia = collect($gallery);

    // Per-type completion stats (answered / total / %) so the Completion panel can
    // reflect whichever inspection type is selected in the dropdown.
    $currentTypeId = $inspection->inspection_type_id;
    $typeStats = [];
    foreach (($inspectionTypes ?? collect()) as $t) {
        $tTotal = 0; $tAns = 0;
        foreach ($t->sections as $sec) {
            foreach ($sec->steps as $st) {
                $tTotal++;
                if (Inspection::detailIsAnswered($answers->get($st->id))) { $tAns++; }
            }
        }
        $typeStats[$t->id] = [
            'answered' => $tAns,
            'total'    => $tTotal,
            'percent'  => $tTotal > 0 ? (int) round($tAns / $tTotal * 100) : 0,
        ];
    }
    // Default selection = the inspection's own type, otherwise the first available.
    $selTypeId = ($currentTypeId && isset($typeStats[$currentTypeId]))
        ? $currentTypeId
        : optional(($inspectionTypes ?? collect())->first())->id;
    $selStats  = $typeStats[$selTypeId] ?? ['answered' => 0, 'total' => 0, 'percent' => 0];
@endphp

<div class="page-content">
    <div class="container-fluid">

        {{-- ===== Header ===== --}}
        <div class="idet-hero">
            <div class="idet-hero__left">
                <div class="idet-hero__icon"><i class="bx bxs-car"></i></div>
                <div>
                    <div class="idet-hero__eyebrow">Inspection #{{ $inspection->id }} · {{ optional($inspection->lead)->reference ?? '—' }}</div>
                    <h3 class="idet-hero__title">{{ $inspection->car_year ? $inspection->car_year.' ' : '' }}{{ $vehicleName }}</h3>
                    <div class="idet-hero__badges">
                        <span class="idet-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        @if($condition)<span class="idet-chip"><i class="bx bx-check-shield"></i> {{ $condition }}</span>@endif
                        @if($recommend)<span class="idet-chip"><i class="bx bx-bulb"></i> {{ $recommend }}</span>@endif
                    </div>
                </div>
            </div>
            <div class="idet-hero__actions">
                <a href="{{ route('inspections.summary', $inspection) }}" target="_blank" class="btn btn-light btn-sm"><i class="bx bx-list-check"></i> Summary</a>
                <a href="{{ route('inspections.edit', $inspection) }}" class="btn btn-light btn-sm"><i class="bx bx-edit"></i> Edit</a>
                @if($inspection->status === 'completed')
                    <a href="{{ route('inspections.report', ['inspection' => $inspection, 'download' => 1]) }}" target="_blank" class="btn btn-primary btn-sm"><i class="bx bx-download"></i> Download Report</a>
                @endif
                <a href="{{ url('inspections') }}" class="btn btn-outline-light btn-sm">Back</a>
            </div>
        </div>

        {{-- ===== Progress + quick facts ===== --}}
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="idet-card idet-progress">
                    <div class="idet-ring" id="idetRing" style="--pct: {{ $selStats['percent'] }}">
                        <div class="idet-ring__hole">
                            <span class="idet-ring__val" id="idetRingVal">{{ $selStats['percent'] }}%</span>
                            <span class="idet-ring__sub" id="idetRingSub">{{ $selStats['answered'] }}/{{ $selStats['total'] }} steps</span>
                        </div>
                    </div>
                    <div class="idet-progress__meta">
                        <div class="idet-progress__title">Completion</div>
                        <div class="text-muted font-size-13" id="idetRingText">{{ $selStats['answered'] }} of {{ $selStats['total'] }} checklist items answered</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="idet-card h-100">
                    <div class="idet-card__title"><i class="bx bx-user-circle"></i> Customer &amp; Assignment</div>
                    <div class="idet-facts">
                        <div class="idet-fact"><span class="idet-fact__k">Customer</span><span class="idet-fact__v">{{ optional($inspection->lead)->customer_name ?? $inspection->customer_name ?: '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Phone</span><span class="idet-fact__v">{{ $inspection->customer_phone ?: '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Email</span><span class="idet-fact__v">{{ $inspection->customer_email ?: '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Technician</span><span class="idet-fact__v">{{ optional($inspection->technician)->name ?? '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Branch</span><span class="idet-fact__v">{{ optional($inspection->branch)->branch_name ?? '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Template</span><span class="idet-fact__v">{{ optional($inspection->type)->name ?? '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Scheduled</span><span class="idet-fact__v">{{ optional($inspection->scheduled_at)->format('d M Y, H:i') ?? '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Started</span><span class="idet-fact__v">{{ optional($inspection->started_at)->format('d M Y, H:i') ?? '—' }}</span></div>
                        <div class="idet-fact"><span class="idet-fact__k">Completed</span><span class="idet-fact__v">{{ optional($inspection->completed_at)->format('d M Y, H:i') ?? '—' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Verdict ===== --}}
        @if($condition || $recommend || $inspection->estimated_repair_cost || $inspection->summary)
            <div class="idet-card mt-3">
                <div class="idet-card__title"><i class="bx bx-clipboard"></i> Overall Verdict</div>
                <div class="idet-facts idet-facts--verdict">
                    <div class="idet-fact"><span class="idet-fact__k">Condition</span><span class="idet-fact__v">{{ $condition ?: '—' }}</span></div>
                    <div class="idet-fact"><span class="idet-fact__k">Recommendation</span><span class="idet-fact__v">{{ $recommend ?: '—' }}</span></div>
                    <div class="idet-fact"><span class="idet-fact__k">Est. Repair Cost</span><span class="idet-fact__v">{{ $inspection->estimated_repair_cost ? number_format($inspection->estimated_repair_cost, 2) : '—' }}</span></div>
                </div>
                @if($inspection->summary)
                    <p class="idet-summary">{{ $inspection->summary }}</p>
                @endif
            </div>
        @endif

        {{-- ===== Vehicle specification ===== --}}
        @if(count($spec))
            <div class="idet-card mt-3">
                <div class="idet-card__title"><i class="bx bxs-car-garage"></i> Vehicle Specification</div>
                <div class="idet-spec">
                    @foreach($spec as $k => $v)
                        <div class="idet-spec__item"><span class="idet-spec__k">{{ $k }}</span><span class="idet-spec__v">{{ $v }}</span></div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===== Additional media ===== --}}
        @if($extraMedia->count())
            <div class="idet-card mt-3">
                <div class="idet-card__title"><i class="bx bx-images"></i> Additional Media <span class="idet-chip" style="margin-left:6px;background:#eef3f8;color:#04B084;">{{ $extraMedia->count() }}</span></div>
                <div class="idet-extra">
                    @foreach($extraMedia as $m)
                        <div class="idet-extra__item">
                            <a href="{{ $m->url }}" target="_blank" rel="noopener" class="idet-media__item {{ $m->type === 'photo' ? '' : 'idet-media__item--video' }}" data-idx="{{ $mediaIndex[$m->id] ?? 0 }}">
                                @if($m->type === 'photo')
                                    <img src="{{ $m->url }}" alt="" loading="lazy">
                                @else
                                    <i class="bx bx-play-circle"></i>
                                @endif
                            </a>
                            <span class="idet-extra__label" title="{{ $m->label }}">{{ $m->label ?: '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===== Checklist ===== --}}
        <div class="idet-section-head">
            <h5 class="mb-0">Inspection Checklist</h5>
            <div class="idet-cltools">
                <div class="idet-search"><i class="bx bx-search"></i><input type="text" id="idetSearch" placeholder="Search sections…" autocomplete="off"></div>
                <button type="button" class="btn btn-sm btn-light" id="idetExpand"><i class="bx bx-expand-vertical"></i> Expand all</button>
                <button type="button" class="btn btn-sm btn-light" id="idetCollapse"><i class="bx bx-collapse-vertical"></i> Collapse all</button>
                @if($allMedia->count())
                    <button type="button" class="idet-chip idet-chip--btn" id="idetViewAllMedia" title="View all photos & videos"><i class="bx bx-images"></i> {{ $allMedia->count() }} media</button>
                @endif
            </div>
        </div>

        <div class="idet-accordion" id="idetAccordion">
          @forelse(($inspectionTypes ?? collect()) as $type)
            <div class="idet-typepanel" data-type-panel="{{ $type->id }}" @if($type->id != $selTypeId) style="display:none;" @endif>
            @forelse($type->sections as $si => $section)
                @php
                    $tot = $section->steps->count();
                    $ans = $section->steps->filter(fn ($st) => \App\Models\Inspection::detailIsAnswered($answers->get($st->id)))->count();
                    $cntClass = $tot>0 && $ans>=$tot ? 'is-done' : ($ans>0 ? 'is-partial' : 'is-empty');
                @endphp
                <div class="idet-acc" data-name="{{ strtolower($section->section_name) }}">
                    <button type="button" class="idet-acc__head" aria-expanded="false">
                        <span class="idet-acc__no">{{ $si + 1 }}</span>
                        <span class="idet-acc__title">{{ $section->section_name }}</span>
                        <span class="idet-acc__right">
                            <span class="idet-count {{ $cntClass }}">{{ $ans }}/{{ $tot }}</span>
                            <i class="bx bx-chevron-down idet-acc__chev"></i>
                        </span>
                    </button>
                    <div class="idet-acc__body">
                        <div class="idet-steps">
                            @foreach($section->steps as $step)
                                @php
                                    $d = $answers->get($step->id);
                                    $state = $stateOf($d);
                                    $answered = $d && (($d->choice!==null && $d->choice!=='') || $d->rating || ($d->descriptive_answer!==null && $d->descriptive_answer!==''));
                                @endphp
                                <div class="idet-step">
                                    <div class="idet-step__mark idet-step__mark--{{ $state }}">
                                        @if($state==='pass')<i class="bx bx-check"></i>
                                        @elseif($state==='fail')<i class="bx bx-x"></i>
                                        @else<i class="bx bx-minus"></i>@endif
                                    </div>
                                    <div class="idet-step__body">
                                        <div class="idet-step__q">{{ $step->question }}</div>

                                        <div class="idet-step__answers">
                                            @if($d && $d->choice !== null && $d->choice !== '')
                                                <span class="idet-ans idet-ans--{{ $state }}">{{ $d->choice }}</span>
                                            @endif
                                            @if($d && $d->rating)
                                                <span class="idet-rating">
                                                    @for($r=1;$r<=5;$r++)<i class="bx {{ $r <= $d->rating ? 'bxs-star on' : 'bx-star' }}"></i>@endfor
                                                    <span class="idet-rating__n">{{ $d->rating }}/5</span>
                                                </span>
                                            @endif
                                            @unless($answered)
                                                <span class="idet-ans idet-ans--na">Not answered</span>
                                            @endunless
                                        </div>

                                        @if($d && $d->descriptive_answer)
                                            <div class="idet-step__text">{{ $d->descriptive_answer }}</div>
                                        @endif
                                        @if($d && $d->remedial_suggestion)
                                            <div class="idet-step__remedy"><i class="bx bx-wrench"></i> {{ $d->remedial_suggestion }}</div>
                                        @endif

                                        @if($d && $d->media->count())
                                            <div class="idet-media">
                                                @foreach($d->media as $m)
                                                    <a href="{{ $m->url }}" target="_blank" rel="noopener" class="idet-media__item {{ $m->type === 'photo' ? '' : 'idet-media__item--video' }}" data-idx="{{ $mediaIndex[$m->id] ?? 0 }}">
                                                        @if($m->type === 'photo')
                                                            <img src="{{ $m->url }}" alt="" loading="lazy">
                                                        @else
                                                            <i class="bx bx-play-circle"></i>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="idet-card text-center text-muted py-4">This inspection type has no sections configured.</div>
            @endforelse
            </div>
          @empty
            <div class="idet-card text-center text-muted py-4">No inspection template configured.</div>
          @endforelse
        </div>
        <div id="idetNoResults" class="idet-card text-center text-muted py-4" style="display:none;">No sections match your search.</div>

    </div>
</div>

{{-- ===== Media lightbox ===== --}}
<div class="idet-lb" id="idetLightbox" aria-hidden="true">
    <div class="idet-lb__bar">
        <span class="idet-lb__count" id="idetLbCount"></span>
        <button type="button" class="idet-lb__close" id="idetLbClose" aria-label="Close">&times;</button>
    </div>
    <div class="idet-lb__stage">
        <button type="button" class="idet-lb__nav idet-lb__nav--prev" id="idetLbPrev" aria-label="Previous"><i class="bx bx-chevron-left"></i></button>
        <div class="idet-lb__media" id="idetLbMedia"></div>
        <button type="button" class="idet-lb__nav idet-lb__nav--next" id="idetLbNext" aria-label="Next"><i class="bx bx-chevron-right"></i></button>
    </div>
    <div class="idet-lb__caption" id="idetLbCaption"></div>
    <div class="idet-lb__strip" id="idetLbStrip"></div>
</div>

@endsection

@section('css')
<style>
    .page-content { padding-bottom: 48px; }

    /* Hero */
    .idet-hero { display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between;
        background: linear-gradient(120deg, #00263D 0%, #04B084 100%); color:#fff; border-radius:18px; padding:24px 28px; margin-bottom:20px; box-shadow:0 8px 26px rgba(12,44,70,.25); }
    .idet-hero__left { display:flex; align-items:center; gap:18px; }
    .idet-hero__icon { width:66px; height:66px; border-radius:16px; background:rgba(255,255,255,.12); display:flex; align-items:center; justify-content:center; font-size:38px; }
    .idet-hero__eyebrow { font-size:12px; letter-spacing:.4px; opacity:.75; margin-bottom:2px; }
    .idet-hero__title { font-size:26px; font-weight:700; margin:0 0 10px; color:#fff; }
    .idet-hero__badges { display:flex; flex-wrap:wrap; gap:8px; }
    .idet-hero__actions { display:flex; flex-wrap:wrap; gap:8px; }
    .idet-status { font-size:12px; font-weight:600; padding:5px 14px; border-radius:20px; }
    .idet-status.is-completed { background:#e7f8ef; color:#04B084; }
    .idet-status.is-progress  { background:#fff4e0; color:#d98a12; }
    .idet-status.is-pending   { background:#eef1f5; color:#5b6472; }
    .idet-chip { display:inline-flex; align-items:center; gap:5px; font-size:12px; padding:5px 12px; border-radius:20px; background:rgba(255,255,255,.14); color:#fff; }

    /* Cards */
    .idet-card { background:#fff; border-radius:16px; box-shadow:0 4px 18px rgba(16,40,70,.06); padding:22px 24px; }
    .idet-card__title { font-size:13px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:#00263D; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    .idet-card__title i { font-size:17px; color:#04B084; }

    /* Progress ring */
    .idet-progress { display:flex; align-items:center; gap:20px; height:100%; }
    .idet-ring { --size:110px; width:var(--size); height:var(--size); border-radius:50%; flex:0 0 auto;
        background: conic-gradient(#04B084 calc(var(--pct)*1%), #eef1f5 0); display:flex; align-items:center; justify-content:center; }
    .idet-ring__hole { width:78%; height:78%; border-radius:50%; background:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .idet-ring__val { font-size:22px; font-weight:700; color:#00263D; line-height:1; }
    .idet-ring__sub { font-size:11px; color:#8a94a6; margin-top:3px; }
    .idet-progress__title { font-weight:700; color:#101828; margin-bottom:2px; }
    .idet-typeselect__lbl { display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.3px; color:#98a2b3; margin:8px 0 3px; }
    .idet-typeselect { width:100%; max-width:230px; height:36px; border:1px solid #e2e6ec; border-radius:9px; padding:0 10px; font-size:13px; color:#344054; background:#fff; cursor:pointer; }
    .idet-typeselect:focus { border-color:#04B084; box-shadow:0 0 0 3px rgba(4,176,132,.14); outline:none; }
    .idet-progress__meta { min-width:0; }

    /* Facts grid */
    .idet-facts { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:16px 22px; }
    .idet-facts--verdict { grid-template-columns:repeat(3, minmax(0,1fr)); }
    .idet-fact { display:flex; flex-direction:column; }
    .idet-fact__k { font-size:11.5px; text-transform:uppercase; letter-spacing:.3px; color:#98a2b3; margin-bottom:3px; }
    .idet-fact__v { font-size:14.5px; font-weight:600; color:#344054; }
    .idet-summary { margin:16px 0 0; padding-top:16px; border-top:1px solid #eef1f5; color:#475467; font-size:14px; line-height:1.6; }

    /* Spec */
    .idet-spec { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:12px 20px; }
    .idet-spec__item { display:flex; justify-content:space-between; gap:10px; padding:10px 14px; background:#f7f9fc; border-radius:10px; }
    .idet-spec__k { font-size:12.5px; color:#667085; }
    .idet-spec__v { font-size:13px; font-weight:600; color:#101828; text-align:right; }

    /* Checklist toolbar */
    .idet-section-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin:34px 2px 14px; }
    .idet-cltools { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .idet-cltools .idet-chip { background:#eef3f8; color:#04B084; }
    .idet-chip--btn { border:0; cursor:pointer; transition:background .12s, transform .12s; }
    .idet-chip--btn:hover { background:#04B084 !important; color:#fff !important; transform:translateY(-1px); }
    .idet-search { position:relative; }
    .idet-search i { position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:16px; color:#98a2b3; }
    .idet-search input { border:1px solid #e2e6ec; border-radius:9px; padding:7px 12px 7px 32px; font-size:13px; width:210px; outline:none; }
    .idet-search input:focus { border-color:#04B084; box-shadow:0 0 0 3px rgba(20,80,122,.12); }

    .idet-count { font-size:12px; font-weight:700; padding:3px 12px; border-radius:20px; white-space:nowrap; }
    .idet-count.is-done { background:#e7f8ef; color:#04B084; }
    .idet-count.is-partial { background:#fff4e0; color:#d98a12; }
    .idet-count.is-empty { background:#eef1f5; color:#8a94a6; }

    /* Accordion */
    .idet-accordion { display:flex; flex-direction:column; gap:10px; }
    .idet-acc { background:#fff; border-radius:12px; box-shadow:0 3px 14px rgba(16,40,70,.05); overflow:hidden; }
    .idet-acc__head { width:100%; border:0; background:transparent; display:flex; align-items:center; gap:12px; padding:15px 18px; cursor:pointer; text-align:left; }
    .idet-acc__head:hover { background:#f7f9fc; }
    .idet-acc__no { flex:0 0 auto; width:26px; height:26px; border-radius:7px; background:#eef3f8; color:#04B084; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .idet-acc__title { flex:1; font-size:14.5px; font-weight:600; color:#1f2a37; min-width:0; }
    .idet-acc__right { flex:0 0 auto; display:flex; align-items:center; gap:12px; }
    .idet-acc__chev { font-size:20px; color:#98a2b3; transition:transform .2s; }
    .idet-acc.is-open .idet-acc__chev { transform:rotate(180deg); }
    .idet-acc.is-open .idet-acc__head { border-bottom:1px solid #eef1f5; }
    .idet-acc__body { max-height:0; overflow:hidden; transition:max-height .28s ease; }
    .idet-acc.is-open .idet-acc__body { max-height:none; }

    .idet-steps { padding:6px 18px 10px; }
    .idet-step { display:flex; gap:14px; padding:16px 0; border-top:1px solid #f0f2f6; }
    .idet-step:first-child { border-top:0; }
    .idet-step__mark { flex:0 0 auto; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; margin-top:1px; }
    .idet-step__mark--pass { background:#e7f8ef; color:#04B084; }
    .idet-step__mark--fail { background:#fdecec; color:#e5484d; }
    .idet-step__mark--na { background:#eef1f5; color:#aab2c0; }
    .idet-step__body { flex:1; min-width:0; }
    .idet-step__q { font-size:14.5px; font-weight:600; color:#1f2a37; }
    .idet-step__answers { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-top:8px; }
    .idet-ans { font-size:12px; font-weight:600; padding:3px 12px; border-radius:16px; }
    .idet-ans--pass { background:#e7f8ef; color:#04B084; }
    .idet-ans--fail { background:#fdecec; color:#e5484d; }
    .idet-ans--na { background:#eef1f5; color:#8a94a6; }
    .idet-rating { display:inline-flex; align-items:center; gap:1px; }
    .idet-rating .bx { color:#e2c65a; font-size:16px; }
    .idet-rating .bx-star { color:#dfe3ea; }
    .idet-rating__n { font-size:12px; font-weight:600; color:#8a94a6; margin-left:5px; }
    .idet-step__text { margin-top:10px; background:#f7f9fc; border-radius:10px; padding:10px 14px; font-size:13.5px; color:#475467; line-height:1.5; }
    .idet-step__remedy { margin-top:8px; font-size:13px; color:#b26a00; display:flex; align-items:center; gap:6px; }
    .idet-extra { display:flex; flex-wrap:wrap; gap:14px; margin-top:12px; }
    .idet-extra__item { width:88px; display:flex; flex-direction:column; gap:5px; }
    .idet-extra__item .idet-media__item { width:88px; height:88px; }
    .idet-extra__label { font-size:11.5px; color:#475467; line-height:1.3; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .idet-media { display:flex; flex-wrap:wrap; gap:10px; margin-top:12px; }
    .idet-media__item { width:76px; height:76px; border-radius:10px; overflow:hidden; display:block; padding:0; border:0; background:#eef1f5; box-shadow:0 2px 8px rgba(16,40,70,.12); cursor:pointer; position:relative; transition:transform .12s, box-shadow .12s; }
    .idet-media__item:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(16,40,70,.22); }
    .idet-media__item::after { content:'\ea9f'; font-family:'boxicons'; position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:22px; background:rgba(12,44,70,.42); opacity:0; transition:opacity .12s; }
    .idet-media__item:hover::after { opacity:1; }
    .idet-media__item img { width:100%; height:100%; object-fit:cover; display:block; }
    .idet-media__item--video { background:#00263D; color:#fff; display:flex; align-items:center; justify-content:center; font-size:30px; }
    .idet-media__item--video::after { content:'\eb75'; }

    /* Lightbox */
    .idet-lb { position:fixed; inset:0; z-index:2000; background:rgba(8,20,33,.92); display:none; flex-direction:column; }
    .idet-lb.is-open { display:flex; }
    .idet-lb__bar { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; color:#fff; gap:12px; }
    .idet-lb__count { font-size:14px; opacity:.85; }
    .idet-lb__close { background:rgba(255,255,255,.14); border:0; color:#fff; width:40px; height:40px; border-radius:10px; font-size:24px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .idet-lb__close:hover { background:rgba(255,255,255,.26); }
    .idet-lb__stage { flex:1; display:flex; align-items:center; justify-content:center; position:relative; padding:0 14px; min-height:0; }
    .idet-lb__media { max-width:92%; max-height:100%; }
    .idet-lb__media img, .idet-lb__media video { max-width:100%; max-height:78vh; border-radius:10px; box-shadow:0 10px 40px rgba(0,0,0,.5); display:block; }
    .idet-lb__nav { position:absolute; top:50%; transform:translateY(-50%); width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,.14); border:0; color:#fff; font-size:30px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .idet-lb__nav:hover { background:rgba(255,255,255,.28); }
    .idet-lb__nav--prev { left:16px; } .idet-lb__nav--next { right:16px; }
    .idet-lb__caption { color:#e6ebf1; text-align:center; font-size:13.5px; padding:12px 20px 4px; }
    .idet-lb__strip { display:flex; gap:8px; overflow-x:auto; padding:12px 18px 18px; justify-content:center; }
    .idet-lb__thumb { width:58px; height:58px; flex:0 0 auto; border-radius:8px; overflow:hidden; border:2px solid transparent; opacity:.55; cursor:pointer; padding:0; background:#00263D; }
    .idet-lb__thumb img { width:100%; height:100%; object-fit:cover; }
    .idet-lb__thumb.is-active { opacity:1; border-color:#04B084; }
    .idet-lb__thumb--video { display:flex; align-items:center; justify-content:center; color:#fff; font-size:22px; }
    @media (max-width:575px){ .idet-lb__nav{ width:42px; height:42px; font-size:24px; } }

    @media (max-width: 767px) {
        .idet-facts, .idet-facts--verdict { grid-template-columns:repeat(2, minmax(0,1fr)); }
        .idet-hero { padding:20px; }
        .idet-hero__title { font-size:22px; }
    }
    @media (max-width: 479px) {
        .idet-facts, .idet-facts--verdict { grid-template-columns:1fr; }
        .idet-progress { flex-direction:column; text-align:center; }
    }
    @media (max-width: 575px) {
        .idet-search input { width:100%; }
        .idet-cltools { width:100%; }
        .idet-search { flex:1 1 100%; }
    }
</style>
@endsection

@section('js')
<script>
(function () {
    var acc = document.getElementById('idetAccordion');
    if (!acc) return;

    var panels = Array.prototype.slice.call(acc.querySelectorAll('.idet-typepanel'));
    // Accordions inside the currently visible type panel (or all, if untyped).
    function activeItems() {
        var scope = acc;
        if (panels.length) {
            var vis = panels.filter(function (p) { return p.style.display !== 'none'; })[0];
            scope = vis || acc;
        }
        return Array.prototype.slice.call(scope.querySelectorAll('.idet-acc'));
    }

    function toggle(item, open) {
        var head = item.querySelector('.idet-acc__head');
        var isOpen = (open === undefined) ? !item.classList.contains('is-open') : open;
        item.classList.toggle('is-open', isOpen);
        if (head) head.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    // Delegate head clicks so it works across every panel (incl. hidden ones).
    acc.addEventListener('click', function (e) {
        var head = e.target.closest('.idet-acc__head');
        if (head && acc.contains(head)) toggle(head.parentElement);
    });

    var expandBtn = document.getElementById('idetExpand');
    var collapseBtn = document.getElementById('idetCollapse');
    if (expandBtn) expandBtn.addEventListener('click', function () { activeItems().forEach(function (i) { toggle(i, true); }); });
    if (collapseBtn) collapseBtn.addEventListener('click', function () { activeItems().forEach(function (i) { toggle(i, false); }); });

    // Live search by section name (within the active panel only)
    var search = document.getElementById('idetSearch');
    var noResults = document.getElementById('idetNoResults');
    function runSearch() {
        if (!search) return;
        var q = search.value.trim().toLowerCase();
        var visible = 0;
        activeItems().forEach(function (item) {
            var name = item.getAttribute('data-name') || '';
            var match = q === '' || name.indexOf(q) !== -1;
            item.style.display = match ? '' : 'none';
            if (match) { visible++; if (q !== '') toggle(item, true); }
            else { toggle(item, false); }
        });
        if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
    }
    if (search) search.addEventListener('input', runSearch);

    // ===== Inspection-type selector: swap panel + update the Completion ring =====
    var stats  = @json($typeStats, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
    var select = document.getElementById('idetTypeSelect');
    var ring   = document.getElementById('idetRing');
    var ringVal = document.getElementById('idetRingVal');
    var ringSub = document.getElementById('idetRingSub');
    var ringTxt = document.getElementById('idetRingText');

    if (select) {
        select.addEventListener('change', function () {
            var id = this.value;
            panels.forEach(function (p) {
                p.style.display = (p.getAttribute('data-type-panel') === id) ? '' : 'none';
            });
            var s = stats[id] || { answered: 0, total: 0, percent: 0 };
            if (ring)    ring.style.setProperty('--pct', s.percent);
            if (ringVal) ringVal.textContent = s.percent + '%';
            if (ringSub) ringSub.textContent = s.answered + '/' + s.total + ' steps';
            if (ringTxt) ringTxt.textContent = s.answered + ' of ' + s.total + ' checklist items answered';
            if (search) { search.value = ''; }
            if (noResults) noResults.style.display = 'none';
        });
    }
})();

// ===== Media lightbox =====
(function () {
    var gallery = @json($gallery, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
    if (!gallery.length) return;

    var lb      = document.getElementById('idetLightbox');
    var stage   = document.getElementById('idetLbMedia');
    var caption = document.getElementById('idetLbCaption');
    var counter = document.getElementById('idetLbCount');
    var strip   = document.getElementById('idetLbStrip');
    var cur = 0;

    // Build the thumbnail strip once.
    gallery.forEach(function (g, i) {
        var t = document.createElement('button');
        t.type = 'button';
        t.className = 'idet-lb__thumb' + (g.type === 'video' ? ' idet-lb__thumb--video' : '');
        t.innerHTML = g.type === 'video' ? '<i class="bx bx-play-circle"></i>' : '<img src="' + g.url + '" alt="">';
        t.addEventListener('click', function () { show(i); });
        strip.appendChild(t);
    });
    var thumbs = strip.children;

    function render() {
        var g = gallery[cur];
        stage.innerHTML = g.type === 'video'
            ? '<video src="' + g.url + '" controls autoplay playsinline></video>'
            : '<img src="' + g.url + '" alt="">';
        caption.textContent = g.caption || '';
        counter.textContent = (cur + 1) + ' / ' + gallery.length;
        for (var i = 0; i < thumbs.length; i++) thumbs[i].classList.toggle('is-active', i === cur);
        var active = thumbs[cur];
        if (active && active.scrollIntoView) active.scrollIntoView({ block: 'nearest', inline: 'center' });
    }
    function show(i) {
        cur = (i + gallery.length) % gallery.length;
        lb.classList.add('is-open');
        lb.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        render();
    }
    function close() {
        lb.classList.remove('is-open');
        lb.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        stage.innerHTML = '';   // stop any playing video
    }
    function step(dir) { show(cur + dir); }

    document.querySelectorAll('.idet-media__item').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();   // open the in-page gallery instead of navigating
            show(parseInt(el.getAttribute('data-idx'), 10) || 0);
        });
    });
    var viewAll = document.getElementById('idetViewAllMedia');
    if (viewAll) viewAll.addEventListener('click', function () { show(0); });

    document.getElementById('idetLbClose').addEventListener('click', close);
    document.getElementById('idetLbPrev').addEventListener('click', function () { step(-1); });
    document.getElementById('idetLbNext').addEventListener('click', function () { step(1); });
    lb.addEventListener('click', function (e) { if (e.target === lb || e.target.classList.contains('idet-lb__stage')) close(); });
    document.addEventListener('keydown', function (e) {
        if (!lb.classList.contains('is-open')) return;
        if (e.key === 'Escape') close();
        else if (e.key === 'ArrowLeft') step(-1);
        else if (e.key === 'ArrowRight') step(1);
    });
})();
</script>
@endsection
