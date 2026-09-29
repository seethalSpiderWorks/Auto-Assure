{{--
    Downloadable PDF edition of the inspection report, rendered by dompdf
    (barryvdh/laravel-dompdf). Same data and the same sections as the on-screen
    report (inspections/report.blade.php), laid out with tables and plain CSS:
    dompdf supports neither flexbox nor gradients, so the screen layout cannot be
    reused as is. Images are read from disk (public/), never over HTTP.
--}}
@php
    use App\Models\Inspection;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $isAr = ($lang ?? 'en') === 'ar';
    $pick = fn ($en, $ar) => $isAr && filled($ar) ? $ar : $en;
    $val  = fn ($v) => ($v === null || $v === '') ? 'N/A' : $v;

    // A URL on this site -> the file under public/ (dompdf reads local files).
    $local = function (?string $url) {
        if (! $url) return null;
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $file = public_path(ltrim(rawurldecode($path), '/'));
        return is_file($file) ? $file : null;
    };

    $reportNo = $inspection->reference;
    $inspDt   = optional($inspection->date_of_inspection ?: $inspection->scheduled_at ?: $inspection->started_at ?: $inspection->created_at)->format('d-M-Y');

    // ---- verdict (same rules as the screen report) ----
    $usesCalculated = $inspection->usesCalculatedVerdict();
    $recommend = Inspection::RECOMMENDATIONS[$inspection->recommendation] ?? '—';
    $overallRatingVal = (float) ($inspection->overall_rating ?? 0);
    $scorePct = $overallRatingVal > 0 ? round(($overallRatingVal / 5) * 100) : 0;
    $condition = match (true) {
        $overallRatingVal >= 4.5 => 'Excellent',
        $overallRatingVal >= 3.5 => 'Very Good',
        $overallRatingVal >= 2.5 => 'Good',
        $overallRatingVal >= 1.5 => 'Fair',
        $overallRatingVal > 0    => 'Poor',
        default                  => null,
    };
    $weightedVerdict = $inspection->weightedVerdict($sectionSummaries ?? collect());
    if ($weightedVerdict) {
        $recommend        = $isAr ? $weightedVerdict['band']['guide_ar'] : $weightedVerdict['band']['guide'];
        $overallRatingVal = $weightedVerdict['rating'];
        $scorePct         = $weightedVerdict['score'];
        $condition        = $weightedVerdict['band']['condition'];
    } elseif ($isAr) {
        $recommend = Inspection::RECOMMENDATIONS_AR[$inspection->recommendation] ?? $recommend;
    }
    $condColors = ['Excellent' => '#2fa84f', 'Very Good' => '#5ab84d', 'Good' => '#f2903f', 'Fair' => '#efb008', 'Poor' => '#e0483d', 'Critical' => '#e0483d'];
    $condColor  = $condColors[$condition] ?? '#8ea3b5';

    // ---- title ----
    $reportKind = $inspection->type?->reportKind() ?: 'Comprehensive';
    if ($isAr) {
        $reportKind = $inspection->type?->name_ar ?: $reportKind;
    }
    if ($inspection->type && ! ($inspection->type->show_name_in_report ?? true)) {
        $reportKind = '';
    }
    $title = trim($reportKind.' '.($isAr ? 'تقرير الفحص' : 'Inspection Report'));
    $basicReport = (bool) $inspection->type?->isBasicReport();

    // ---- vehicle summary rows ----
    $rows = [
        ['Make', $val($inspection->car_make)],
        ['Model', $val($inspection->car_model)],
        ['Year', $val($inspection->car_year)],
        ['Region', $val($inspection->region)],
        ['Customer Name', $val($inspection->customer_name)],
        ['Reference', $val($reportNo)],
        ['Inspection Date', $val($inspDt)],
        ['Plate No', $val($inspection->plate_no)],
        ['VIN / Chassis No', $val($inspection->vin)],
        ['Odometer', $val($inspection->odometer)],
        ['Exterior Colour', $val($inspection->exterior_color)],
        ['Gearbox', $val($inspection->gearbox)],
        ['Last Service Date', $val(optional($inspection->last_service_date)->format('d-m-Y'))],
        ['Fuel Type', $val($inspection->fuel_type)],
        ['Body Type', $val($inspection->body_type)],
        ['No. of Keys', $val($inspection->number_of_keys)],
        ['With Service History', $inspection->with_service_history === null ? 'N/A' : ($inspection->with_service_history ? 'Yes' : 'No')],
        ['Vehicle Condition', $val($inspection->vehicle_condition)],
    ];
    $rowCols = array_chunk($rows, (int) ceil(count($rows) / 2));

    // ---- photos: checklist + section media (general), diagnostic bucket ----
    $sectionById = $inspection->type->sections->keyBy('id');
    $stepSection = [];
    foreach ($inspection->type->sections as $sec) {
        foreach ($sec->steps as $stp) { $stepSection[$stp->id] = $sec; }
    }
    $wantsDiagnostic = (bool) $inspection->type?->has_diagnostic_media;
    $photos = [];
    $diagnosticPhotos = [];
    foreach ($inspection->details as $d) {
        $isGlobal = is_null($d->inspection_step_id) && is_null($d->inspection_section_id);
        if ($isGlobal && ! $wantsDiagnostic) continue;
        $isCategory = is_null($d->inspection_step_id) && ! is_null($d->inspection_section_id);
        $sec = $isCategory ? ($sectionById[$d->inspection_section_id] ?? null) : ($stepSection[$d->inspection_step_id] ?? null);
        foreach ($d->media->where('type', 'photo') as $m) {
            $file = $local($m->thumbUrl(480)) ?: $local($m->url);
            if (! $file) continue;
            if ($isGlobal) { $diagnosticPhotos[] = ['file' => $file, 'caption' => $m->label ?: '']; continue; }
            $caption = $isCategory ? ($sec?->section_name ?? '') : ($m->label ?: ($sec?->section_name ?? ''));
            $photos[] = ['file' => $file, 'caption' => $caption];
        }
    }

    // Cover image: the vehicle image, else the first photo.
    $coverImg = $inspection->vehicle_image
        ? ($local(\App\Support\Thumbnailer::url($inspection->vehicle_image, 900)) ?: $local($inspection->vehicleImageUrl()))
        : ($photos[0]['file'] ?? null);

    // ---- per-area summary notes ----
    $areaNotes = [];
    foreach (($summaryTypes ?? []) as $tid => $tname) {
        if (filled($summaries[$tid] ?? null)) $areaNotes[] = ['name' => $tname, 'note' => $summaries[$tid]];
    }

    // ---- checklist: answered steps only, EV / technical sections apart ----
    $evSections   = ['EV and PHEV', 'EV & PHEV Details'];
    $techSections = ['Technical Inspection Measurements', 'Technical & Emissions Tests'];
    $skip = array_merge($evSections, $techSections);
    $rank = ['pass' => 0, 'na' => 1, 'fail' => 2];
    $checklist = [];
    foreach ($inspection->type->sections as $section) {
        if (in_array($section->section_name, $skip, true)) continue;
        $steps = $section->steps
            ->filter(fn ($s) => Inspection::isReportable($answers->get($s->id)))
            ->sortBy(fn ($s) => $rank[Inspection::choiceState($answers->get($s->id))] ?? 1)
            ->values();
        if ($steps->isEmpty()) continue;
        $meta = ($sectionSummaries ?? collect())->get($section->id);
        $checklist[] = [
            'group'  => $section->group_name ? $pick($section->group_name, $section->group_name_ar) : null,
            'name'   => $pick($section->section_name, $section->section_name_ar),
            'rating' => $section->weight !== null ? (float) (optional($meta)->rating ?: 0) : 0,
            'steps'  => $steps,
        ];
    }

    // EV & technical blocks read answers by question text.
    $qa = [];
    foreach ($inspection->type->sections as $s) {
        foreach ($s->steps as $stp) { $qa[$stp->question] = $answers->get($stp->id); }
    }
    $ev = fn ($q) => optional($qa[$q] ?? null)->descriptive_answer ?: 'N/A';
    $techState = fn ($q) => (optional($qa[$q] ?? null)->choice === 'Pass') ? 'pass' : ((optional($qa[$q] ?? null)->choice === 'Fail') ? 'fail' : 'na');
    $reading = fn ($q) => optional($qa[$q] ?? null)->descriptive_answer ?: 'N/A';
    $sectionNames = $inspection->type->sections->pluck('section_name');
    $hasEv   = $sectionNames->intersect($evSections)->isNotEmpty();
    $hasTech = $sectionNames->intersect($techSections)->isNotEmpty();

    // ---- damage diagrams ----
    $damageDiagrams = [];
    $typeId = $inspection->inspection_type_id;
    $diagrams = \App\Models\DamageDiagram::with('sections')->ordered()->get()->keyBy('key');
    foreach ($inspection->damageImages() as $key => $path) {
        try {
            if (! $path || ! Storage::disk('public')->exists($path)) continue;
        } catch (\Throwable $e) { continue; }
        $dg = $diagrams->get($key);
        $damageDiagrams[] = [
            'label'   => $dg?->name ?? Str::headline($key),
            'section' => optional($dg?->sections->firstWhere('inspection_type_id', $typeId))->section_name,
            'file'    => Storage::disk('public')->path($path),
            'key'     => $dg ? \App\Models\DamageColour::forDiagram($dg->id)->ordered()->get() : collect(),
        ];
    }

    $mark = fn ($state) => $state === 'pass'
        ? '<span class="mk pass">&#10003;</span>'
        : ($state === 'fail' ? '<span class="mk fail">&#10007;</span>' : '<span class="mk na">~</span>');
    $logo = public_path('img/pdf_design/auto-logo.png');
    $logo = is_file($logo) ? $logo : public_path('img/pdf_design/auto-logo.svg');
@endphp
<!doctype html>
<html lang="{{ $isAr ? 'ar' : 'en' }}" @if($isAr) dir="rtl" @endif>
<head>
<meta charset="utf-8">
<title>{{ $title }} — {{ $reportNo }}</title>
<style>
    @page { margin: 78px 34px 74px 34px; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1c2431; margin: 0; }
    @if($isAr) body { direction: rtl; text-align: right; } @endif

    /* running header / footer (repeated on every page by dompdf) */
    .hdr { position: fixed; top: -62px; left: 0; right: 0; height: 48px; border-bottom: 2px solid #2fa84f; }
    .hdr img { height: 32px; }
    .hdr .t { font-size: 15px; font-weight: bold; color: #0f2d43; }
    .ftr { position: fixed; bottom: -60px; left: -34px; right: -34px; height: 50px; background: #0a1f33; color: #fff;
        font-size: 8px; font-style: italic; font-weight: bold; padding: 9px 34px 0; }
    .ftr td { color: #fff; vertical-align: top; line-height: 1.5; }

    table { border-collapse: collapse; width: 100%; }
    .bar { background: #0f2d43; color: #fff; font-size: 12px; font-weight: bold; padding: 7px 12px; margin: 14px 0 8px;
        border-left: 4px solid #2fa84f; }
    .grp { font-size: 14px; font-weight: bold; color: #0f2d43; margin: 16px 0 2px; border-bottom: 2px solid #2fa84f; padding-bottom: 3px; }
    .card { border: 1px solid #e3e7ee; padding: 8px 10px; }
    .kv td { padding: 4px 3px; border-bottom: 1px solid #eef0f4; font-size: 9.5px; }
    .kv td.k { color: #5b6472; width: 46%; }
    .kv td.v { font-weight: bold; color: #0f2d43; text-align: {{ $isAr ? 'left' : 'right' }}; }
    .cover-img { width: 100%; height: 230px; }

    .verdict td { padding: 8px 10px; border: 1px solid #e3e7ee; vertical-align: top; }
    .verdict .l { font-size: 9px; color: #5b6472; }
    .verdict .v { font-size: 14px; font-weight: bold; color: #0f2d43; margin-top: 3px; }

    .items td { width: 50%; padding: 5px 7px; border: 1px solid #eef0f4; vertical-align: top; font-size: 9.5px; }
    .note { color: #6b7280; font-size: 8.5px; margin-top: 2px; }
    /* Plain coloured symbols: dompdf draws small rounded badges badly. */
    .mk { font-weight: bold; font-size: 11px; margin-{{ $isAr ? 'left' : 'right' }}: 5px; }
    .mk.pass { color: #2fa84f; } .mk.fail { color: #e0483d; } .mk.na { color: #9aa3af; }

    .gal td { width: 33.33%; padding: 4px; vertical-align: top; text-align: center; }
    .gal img { width: 100%; height: 120px; }
    .gal .cap { font-size: 8px; color: #5b6472; margin-top: 2px; }

    .dt th, .dt td { border: 1px solid #e3e7ee; padding: 5px 6px; font-size: 9px; }
    .dt th { background: #f3f5f8; text-align: center; }
    .dt td.c { text-align: center; }
    .sig td { width: 33.33%; padding: 30px 8px 6px; text-align: center; font-size: 9px; color: #5b6472; }
    .sig .ln { border-top: 1px solid #9aa3af; padding-top: 4px; }
    .avoid { page-break-inside: avoid; }
    .dot { display: inline-block; width: 8px; height: 8px; border-radius: 4px; border: 1px solid #8a94a3; }
</style>
</head>
<body>

<div class="hdr">
    <table><tr>
        <td style="vertical-align:middle;">@if(is_file($logo))<img src="{{ $logo }}" alt="Auto Assure">@endif</td>
        <td class="t" style="text-align:{{ $isAr ? 'left' : 'right' }};vertical-align:middle;">{{ $title }}</td>
    </tr></table>
</div>

<div class="ftr">
    <table><tr>
        <td>Auto Assure – Technical Inspection Services<br>Shop 4, Zone 91, Street 7009, Ezdan Oasis, Al Wukhair, State of Qatar</td>
        <td style="text-align:right;">Website: www.auto-assure.com<br>Email: info@auto-assure.com</td>
    </tr></table>
</div>

{{-- ============================== COVER ============================== --}}
<table>
    <tr>
        <td style="width:{{ $coverImg ? '58%' : '100%' }};vertical-align:top;padding-{{ $isAr ? 'left' : 'right' }}:10px;">
            <div class="bar" style="margin-top:0;">{{ $isAr ? 'بيانات المركبة' : 'Vehicle Summary' }}</div>
            <table><tr>
                @foreach ($rowCols as $col)
                    <td style="width:50%;vertical-align:top;padding:0 4px;">
                        <table class="kv">
                            @foreach ($col as $r)
                                <tr><td class="k">{{ $r[0] }}</td><td class="v">{{ $r[1] }}</td></tr>
                            @endforeach
                        </table>
                    </td>
                @endforeach
            </tr></table>
        </td>
        @if ($coverImg)
            <td style="width:42%;vertical-align:top;">
                <img class="cover-img" src="{{ $coverImg }}" alt="Vehicle">
            </td>
        @endif
    </tr>
</table>

@if ($usesCalculated)
    <table class="verdict" style="margin-top:12px;">
        <tr>
            <td style="width:25%;"><div class="l">{{ $isAr ? 'النتيجة' : 'Overall Score' }}</div><div class="v">{{ $scorePct }} / 100</div></td>
            <td style="width:25%;"><div class="l">{{ $isAr ? 'التقييم العام' : 'Overall Rating' }}</div><div class="v" style="color:{{ $condColor }};">{{ $condition ?: '—' }}@if($overallRatingVal > 0) ({{ number_format($overallRatingVal, 1) }}/5)@endif</div></td>
            <td style="width:50%;"><div class="l">{{ $isAr ? 'التوصية' : 'Recommendation' }}</div><div class="v">{{ $recommend }}</div></td>
        </tr>
    </table>
@endif

@if (filled($inspection->summary))
    <div class="bar">{{ $isAr ? 'ملاحظات الفاحص' : 'Inspector Comment' }}</div>
    <div class="card" style="line-height:1.6;">{!! nl2br(e($pick($inspection->summary, $inspection->summary_ar))) !!}</div>
@endif

{{-- ============================== INSPECTION SUMMARY ============================== --}}
@if (! empty($areaNotes))
    <div class="bar">{{ $isAr ? 'ملخص الفحص' : 'Inspection Summary' }}</div>
    <table class="items">
        @foreach (array_chunk($areaNotes, 2) as $pair)
            <tr class="avoid">
                @foreach ($pair as $an)
                    <td><strong>{{ $an['name'] }}</strong><div class="note" style="font-size:9px;color:#475467;">{!! nl2br(e($an['note'])) !!}</div></td>
                @endforeach
                @if (count($pair) === 1)<td style="border:none;"></td>@endif
            </tr>
        @endforeach
    </table>
@endif

{{-- ============================== DIAGNOSTIC MEDIA ============================== --}}
@if ($wantsDiagnostic && ! empty($diagnosticPhotos))
    <div class="bar">{{ $isAr ? 'تقارير الفحص بالكمبيوتر' : 'Diagnostic Media' }}</div>
    <table class="gal">
        @foreach (array_chunk($diagnosticPhotos, 3) as $three)
            <tr class="avoid">
                @foreach ($three as $p)
                    <td><img src="{{ $p['file'] }}" alt="">@if($p['caption'] !== '')<div class="cap">{{ $p['caption'] }}</div>@endif</td>
                @endforeach
                @for ($i = count($three); $i < 3; $i++)<td></td>@endfor
            </tr>
        @endforeach
    </table>
@endif

{{-- ============================== PHOTOS ============================== --}}
@if (! empty($photos))
    <div class="bar">{{ $basicReport ? ($isAr ? 'صور المركبة' : 'Vehicle Photos') : ($isAr ? 'صور عامة' : 'General Photos') }}</div>
    <table class="gal">
        @foreach (array_chunk($photos, 3) as $three)
            <tr class="avoid">
                @foreach ($three as $p)
                    <td><img src="{{ $p['file'] }}" alt=""><div class="cap">{{ $p['caption'] }}</div></td>
                @endforeach
                @for ($i = count($three); $i < 3; $i++)<td></td>@endfor
            </tr>
        @endforeach
    </table>
@endif

{{-- ============================== EV & TECHNICAL (full report only) ============================== --}}
@unless ($basicReport)
    @if ($hasEv)
        <div class="bar">EV &amp; PHEV</div>
        <table class="dt">
            <tr><td>Footprint (M2)</td><td class="c">{{ $ev('Footprint (M2)') }}</td><td>Full Battery Charge Time</td><td class="c">{{ $ev('Full Battery Charge Time (Min or H)') }}</td></tr>
            <tr><td>Battery Capacity (KW/h)</td><td class="c">{{ $ev('Battery Capacity (KW/h)') }}</td><td>Battery Type</td><td class="c">{{ $ev('Battery Type') }}</td></tr>
            <tr><td>Electric Consumption</td><td class="c">{{ $ev('Electric Consumption (KWh/100KM)') }}</td><td>Battery Voltage (V)</td><td class="c">{{ $ev('Battery Voltage (V)') }}</td></tr>
            <tr><td>Equivalent fuel economy</td><td class="c">{{ $ev('Equivalent fuel economy (KM/L)') }}</td><td></td><td></td></tr>
        </table>
    @endif

    @if ($hasTech)
        <div class="bar">Technical Inspection Measurements</div>
        <table class="dt">
            <tr><th>Inspection Item</th><th>Criteria Limit</th><th>Measurement</th><th>Result</th></tr>
            @foreach ([
                ['Main Brake (Static Device)', 'Brake efficiency ≥ 45%', 'Main Brake (Static Device) — Automated brake efficiency check'],
                ['Gaseous Pollutants (CO)', '(CO) ≤ 3.5%', 'Pollution - Gasoline Engines (CO)'],
                ['Gaseous Pollutants (HC)', '(HC) ≤ 1200 ppm', 'Pollution - Gasoline Engines (HC)'],
                ['Smoke Density (Diesel)', 'Reading ≤ 40%', 'Smoke Density - Diesel Engines'],
                ['Glass Transparency', 'Transparency ≥ 70%', 'Glass Transparency'],
                ['Noise Emissions', 'Per clause 1.19', 'Noise Emissions'],
            ] as $tr)
                <tr><td>{{ $tr[0] }}</td><td class="c">{{ $tr[1] }}</td><td class="c">{{ $reading($tr[2]) }}</td><td class="c">{!! $mark($techState($tr[2])) !!}</td></tr>
            @endforeach
        </table>
    @endif
@endunless

{{-- ============================== CHECKLIST ============================== --}}
@php $lastGroup = null; @endphp
@foreach ($checklist as $sec)
    @if ($sec['group'] && $sec['group'] !== $lastGroup)
        <div class="grp">{{ $sec['group'] }}</div>
        @php $lastGroup = $sec['group']; @endphp
    @endif
    <div class="bar">
        {{ $sec['name'] }}
        @if ($sec['rating'] > 0)<span style="float:{{ $isAr ? 'left' : 'right' }};color:#f1b44c;">{{ rtrim(rtrim(number_format($sec['rating'], 1), '0'), '.') }}/5</span>@endif
    </div>
    <table class="items">
        @foreach ($sec['steps']->chunk(2) as $pair)
            <tr class="avoid">
                @foreach ($pair as $step)
                    @php
                        $d = $answers->get($step->id);
                        $note = optional($d)->descriptive_answer ?: optional($d)->remedial_suggestion;
                    @endphp
                    <td>{!! $mark(Inspection::choiceState($d)) !!}{{ $pick($step->question, $step->question_ar) }}@if($note)<div class="note">{{ $note }}</div>@endif</td>
                @endforeach
                @if ($pair->count() === 1)<td style="border:none;"></td>@endif
            </tr>
        @endforeach
    </table>
@endforeach

{{-- ============================== DAMAGE DIAGRAMS (both on one page) ============================== --}}
@if (! empty($damageDiagrams))
    <div class="avoid">
        <div class="bar">{{ $basicReport ? ($isAr ? 'صور فحص الطلاء' : 'Paint Inspection Images') : ($isAr ? 'مواضع الأضرار' : 'Damage Points') }}</div>
        @foreach ($damageDiagrams as $d)
            <div class="card avoid" style="margin-bottom:8px;">
                <div style="font-weight:bold;font-size:10px;text-transform:uppercase;color:#3b4655;">{{ $d['label'] }}@if($d['section']) — {{ $d['section'] }}@endif</div>
                @if ($d['key']->isNotEmpty())
                    <div style="margin:4px 0;font-size:8.5px;color:#3b4655;">
                        @foreach ($d['key'] as $kc)
                            <span style="margin-{{ $isAr ? 'left' : 'right' }}:10px;"><span class="dot" style="background:{{ $kc->colour }};"></span> <strong>{{ $kc->label }}</strong>@if($kc->description) — {{ $kc->description }}@endif</span>
                        @endforeach
                    </div>
                @endif
                <div style="text-align:center;"><img src="{{ $d['file'] }}" alt="" style="max-width:80%;max-height:250px;"></div>
            </div>
        @endforeach
    </div>
@endif

{{-- ============================== SIGNATURES + TERMS ============================== --}}
@unless ($basicReport)
    <div class="avoid">
        <div class="bar">{{ $isAr ? 'التواقيع' : 'Signatures' }}</div>
        <table class="sig"><tr>
            <td style="width:50%;"><div class="ln">Inspector — Sign &amp; Date</div></td>
            <td style="width:50%;"><div class="ln">Technical Manager — Sign &amp; Date</div></td>
        </tr></table>
    </div>
@endunless

<div class="avoid">
    <div class="bar">{{ $isAr ? 'الشروط والأحكام' : 'Terms & Conditions' }}</div>
    <div class="card" style="line-height:1.6;font-size:9px;">
        @if ($isAr)
            هذا التقرير يخص المركبة التي قدمها العميل وتم اختبارها/فحصها فقط. يعتبر هذا التقرير لاغياً في حالة حدوث أي كشط أو تعديل أو حذف أو إضافة. بيانات هذا التقرير سرية وخاصة ولا يحق لأي من أفراد الشركة نشرها أو الإعلان عنها إلا بموافقة مسبقة من العميل وبموجب حكم قضائي أو طلب من الجهات المختصة. يلتزم صاحب المركبة (العميل) بالحضور مرة أخرى إذا طُلب منه.
        @else
            This report is for the vehicle provided by the customer and tested/inspected only. This report is considered void in the event of any scraping, modification, deletion, or addition. The data in this report is confidential and private and no company personnel has the right to publish or announce it except with the prior approval of the customer or according to a court ruling or a request from the competent authorities. The vehicle owner (customer) is obligated to attend again if requested.
        @endif
    </div>
</div>

</body>
</html>
