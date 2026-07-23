@php
    use App\Models\Inspection;
    use Illuminate\Support\Facades\Storage;

    $lead      = $inspection->lead;
    $reportNo  = $inspection->reference;
    $reportDt  = optional($inspection->completed_at ?: $inspection->updated_at)->format('d-M-Y');
    $reportTm  = optional($inspection->scheduled_at ?: $inspection->started_at ?: $inspection->created_at)->format('h:i A');
    $inspDt    = optional($inspection->scheduled_at ?: $inspection->started_at ?: $inspection->created_at)->format('d-M-Y');
    $condition = Inspection::CONDITIONS[$inspection->overall_condition] ?? '—';
    $recommend = Inspection::RECOMMENDATIONS[$inspection->recommendation] ?? '—';
    $compliant = $inspection->recommendation !== 'avoid';
    $typeName  = optional($inspection->type)->name ?: 'Inspection';
    $val = fn ($v) => ($v === null || $v === '') ? 'N/A' : e($v);

    // question -> answer (detail) lookup, for the EV & Technical special blocks
    $qa = [];
    foreach ($inspection->type->sections as $s) {
        foreach ($s->steps as $stp) { $qa[$stp->question] = $answers->get($stp->id); }
    }
    $ev = fn ($q) => optional($qa[$q] ?? null)->descriptive_answer ?: 'N/A';
    // technical result state: pass | fail | na
    $techState = fn ($q) => (optional($qa[$q] ?? null)->choice === 'Pass') ? 'pass'
        : ((optional($qa[$q] ?? null)->choice === 'Fail') ? 'fail' : 'na');
    $reading = fn ($q) => optional($qa[$q] ?? null)->descriptive_answer ?: 'N/A';

    $secAr = [
        'Exterior & Body' => 'الفحص الخارجي والهيكل', 'Engine & Mechanical' => 'المحرك والأجزاء الميكانيكية',
        'Transmission' => 'ناقل الحركة', 'Brakes & Suspension' => 'المكابح والتعليق', 'Tyres' => 'الإطارات',
        'Electrical & Electronics' => 'الكهرباء والإلكترونيات', 'Interior & AC' => 'الداخلية والتكييف',
        'Test Drive' => 'تجربة القيادة', '1.9 Vehicle Glass' => 'زجاج المركبة',
        '1.14 Side View Mirrors' => 'المرايا الجانبية', '3. Safety Requirements' => 'متطلبات السلامة',
        '4. Modern Systems' => 'الأنظمة الحديثة', 'Paint Thickness Measurement' => 'قياس سماكة الطلاء',
        'Computer Diagnostics (OBD)' => 'تشخيص الكمبيوتر', 'Undercarriage & Chassis' => 'الهيكل السفلي والشاسيه',
        'Extended Road Test' => 'اختبار الطريق الموسّع',
    ];
    $evSections   = ['EV and PHEV', 'EV & PHEV Details'];
    $techSections = ['Technical Inspection Measurements', 'Technical & Emissions Tests'];
    $skip = array_merge($evSections, $techSections);

    $sectionNames = $inspection->type->sections->pluck('section_name');
    $hasEv   = $sectionNames->intersect($evSections)->isNotEmpty();
    $hasTech = $sectionNames->intersect($techSections)->isNotEmpty();

    $splitNum = fn ($s) => preg_match('/^(\d+(?:\.\d+)*)[\).]?\s+(.+)$/u', (string) $s, $m) ? [$m[1], $m[2]] : ['', (string) $s];

    // Per-section banner images (from public/img/pdf_design). Keyed by section_name.
    // Add an entry as artwork is supplied; sections without a mapping simply show no banner.
    // Keyed by section_name OR group_name (the two never collide in practice).
    $sectionBanners = [
        'EV and PHEV'                          => 'EV&PHEV.png',
        'EV & PHEV Details'                    => 'EV&PHEV.png',
        'Technical Inspection Measurements'    => 'Technical-Inspection-Measurements.png',
        'Technical & Emissions Tests'          => 'Technical-Inspection-Measurements.png',
        '1. Inspection Exterior'               => 'Inspection-Exterior.png',
        '1.7 Brake System'                     => 'Brake-System.png',
        '1.7.7 Automated brake efficiency check (using static or dynamic inspection device)' => 'Automated-brake-efficiency-check.png',
        '1.8 Lights'                           => 'Lights.png',
        '1.10 Steering System'                 => 'Steering-System.png',
        '1.11 Suspension System'               => 'Suspension-System.png',
        '1.12 Exhaust System'                  => 'Exhaust-System.png',
        '1.22 Gaseous Pollutants'              => 'Gaseous-Pollutants.png',
        '2. Interior Inspection'               => 'Interior-Inspection.png',
        '6. Buses (model year 2023 and above)' => 'Buses.png',
    ];
    $bannerUrl = function ($name) use ($sectionBanners) {
        $file = $sectionBanners[$name] ?? null;
        return $file ? asset('img/pdf_design/' . rawurlencode($file)) : null;
    };

    // Per-step pass/fail/na state (mirrors the checklist / summary rule).
    $rowState = function ($step) use ($answers) {
        $d = $answers->get($step->id); $choice = $d->choice ?? null; $rating = $d->rating ?? null;
        if (in_array($choice, ['Pass','Yes'], true) || ($rating !== null && $rating >= 3)) return 'pass';
        if (in_array($choice, ['Fail','No'], true) || ($rating !== null && $rating < 3)) return 'fail';
        return 'na';
    };
    $badge = fn ($state) => $state === 'pass'
        ? '<span class="badge b-pass">PASS</span>'
        : ($state === 'fail' ? '<span class="badge b-fail">FAIL</span>' : '<span class="badge b-na">N/A</span>');

    // Photos attached to a given step, filtered to files that actually exist.
    $stepPhotos = function ($step) use ($answers) {
        $d = $answers->get($step->id);
        if (! $d) return collect();
        return $d->media->where('type', 'photo')->filter(function ($m) {
            try { return $m->path && Storage::disk($m->disk ?: 'public')->exists($m->path); }
            catch (\Throwable $e) { return false; }
        })->values();
    };

    // Overall pass/fail/na tally for the Report Overview donut. N/A and
    // unanswered steps are excluded so the percentages match the printed items.
    $tally = ['pass' => 0, 'fail' => 0, 'na' => 0];
    foreach ($inspection->type->sections as $sec) {
        if (in_array($sec->section_name, $skip, true)) continue;
        foreach ($sec->steps as $stp) {
            if (! Inspection::isReportable($answers->get($stp->id))) continue;
            $tally[$rowState($stp)]++;
        }
    }
    $tTot  = max(1, array_sum($tally));
    $pPass = round($tally['pass'] / $tTot * 100);
    $pFail = round($tally['fail'] / $tTot * 100);

    // step_id -> section (for photo captions), and the flat photo list used by galleries + hero.
    $stepSection = [];
    foreach ($inspection->type->sections as $sec) {
        foreach ($sec->steps as $stp) { $stepSection[$stp->id] = $sec; }
    }
    $reportPhotos = [];
    foreach ($inspection->details as $d) {
        $sec = $stepSection[$d->inspection_step_id] ?? null;
        foreach ($d->media->where('type', 'photo') as $m) {
            try {
                if (! $m->path || ! Storage::disk($m->disk ?: 'public')->exists($m->path)) continue;
            } catch (\Throwable $e) { continue; }
            $reportPhotos[] = ['caption' => $m->label ?: ($sec?->section_name ?? 'Photo'), 'media' => $m];
        }
    }
    $heroPhoto = $reportPhotos[0]['media']->url ?? null;

    // Vehicle specification list (bilingual) for the summary card.
    $specs = [
        ['Make', 'اسم الصانع', $val($inspection->car_make)],
        ['Model', 'الطراز', $val($inspection->car_model)],
        ['Year', 'سنة الطراز', $val($inspection->car_year)],
        ['Manufacturing Year', 'سنة الصنع', $val($inspection->manufacturing_year)],
        ['VIN / Chassis No', 'رقم الهيكل', $val($inspection->vin)],
        ['Plate No', 'رقم اللوحة', $val($inspection->plate_no)],
        ['Odometer', 'قراءة العداد', $val($inspection->odometer)],
        ['Region', 'المنطقة', $val($inspection->region)],
        ['Exterior Colour', 'اللون الخارجي', $val($inspection->exterior_color)],
        ['Gearbox', 'ناقل الحركة', $val($inspection->gearbox)],
        ['Fuel Type', 'نوع الوقود', $val($inspection->fuel_type)],
        ['Body Type', 'نوع الهيكل', $val($inspection->body_type)],
        ['No. of Keys', 'عدد المفاتيح', $val($inspection->number_of_keys)],
        ['With Service History', 'مع سجل الصيانة', $inspection->with_service_history === null ? $val(null) : ($inspection->with_service_history ? 'Yes' : 'No')],
        ['Last Service Date', 'تاريخ آخر صيانة', $val(optional($inspection->last_service_date)->format('d-m-Y'))],
    ];

    $makeHeading = $val($inspection->car_make);
    $ck = fn ($on) => $on ? '<span class="on">&#9746;</span>' : '<span class="off">&#9744;</span>';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inspection Report — {{ $reportNo }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#eef1f5; --card:#ffffff; --ink:#1c2430; --muted:#8b93a1;
            --bar:#00263d; --navy-2:#013a5c; --brand:#0c7a50; --brand-2:#12a150;
            --pass:#35a44d; --fail:#ef8a3c; --na:#aeb6c2; --line:#e7eaef;
        }
        *{ box-sizing:border-box; }
        body{ font-family:'Poppins','Segoe UI',Tahoma,sans-serif; color:var(--ink); margin:0; padding:20px; font-size:12px; background:#d9dde3; }
        .sheet{ max-width:900px; margin:0 auto; }
        .ar{ font-family:'Tahoma','Geeza Pro','Arial',sans-serif; direction:rtl; unicode-bidi:embed; }
        .ar-blk{ display:block; }
        .muted{ color:var(--muted); }
        h1,h2,h3{ margin:0; }

        /* ---- page shell ---- */
        .page{ background:var(--bg); border-radius:6px; padding:22px 24px 30px; margin-bottom:22px; }
        .page-header{ display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .brand-pill{ display:inline-flex; align-items:center; gap:7px; background:var(--brand); color:#fff;
            font-weight:700; font-size:13px; padding:7px 15px 7px 10px; border-radius:20px; letter-spacing:.2px; }
        .brand-mark{ display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px;
            background:#fff; color:var(--brand); border-radius:50%; font-size:12px; font-weight:800; }
        .brand-logo{ height:38px; width:auto; display:block; }
        .doc-tag{ color:#aab0bb; font-family:'Quicksand',sans-serif; font-weight:600; font-size:15px; }

        /* ---- dark section bar (navy w/ emerald accent edge) ---- */
        .sec-bar{ background:var(--bar); color:#fff; border-radius:10px; padding:16px 24px 16px 26px; margin:0 0 18px;
            box-shadow:inset 5px 0 0 var(--brand-2); display:flex; align-items:center; justify-content:space-between; }
        .sec-bar .en{ font-family:'Quicksand',sans-serif; font-weight:600; font-size:20px; }
        .sec-bar .ar{ font-size:14px; color:#cfd4dc; font-weight:400; }

        /* ---- per-section banner image ---- */
        .sec-banner{ display:block; width:100%; border-radius:10px;
            margin:16px 0 16px; border:1px solid var(--line); break-inside:avoid; }
        /* group title + its banner as one unbreakable unit (never split across a page) */
        .group-lead{ break-inside:avoid; margin-top:12px; }
        .group-lead .make-h{ margin-bottom:2px; }

        /* ---- cards ---- */
        .card{ background:var(--card); border-radius:8px; padding:20px 22px; box-shadow:0 6px 18px rgba(24,33,54,.06); margin-bottom:16px; }
        .card.tight{ padding:16px 18px; }
        .grid2{ display:flex; flex-wrap:wrap; gap:16px; }
        .grid2 > .item-card{ flex:1 1 calc(50% - 8px); min-width:calc(50% - 8px); margin-bottom:0; }

        /* ---- item (check) card ---- */
        .item-card{ background:var(--card); border-radius:10px; padding:16px 18px; box-shadow:0 6px 18px rgba(24,33,54,.06);
            min-height:96px; margin-bottom:16px; }
        .item-head{ display:flex; align-items:center; flex-wrap:wrap; gap:10px; }
        .item-title{ font-weight:700; font-size:13px; color:#1c2431; }
        .item-title .ar{ display:block; font-weight:600; font-size:12px; color:#6b7280; margin-top:20px; }
        .item-note{ margin-top:8px; font-weight:600; font-size:12px; color:#3b4453; }
        .item-note.ar{ text-align:right; }
        .thumbs{ display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; }
        .thumb{ width:96px; height:72px; object-fit:cover; border-radius:10px; border:1px solid var(--line); }

        /* ---- badges ---- */
        .badge{ display:inline-block; color:#fff; font-weight:700; font-size:11px; letter-spacing:.4px;
            padding:5px 14px; border-radius:10px; line-height:1; }
        .b-pass{ background:var(--pass); } .b-fail{ background:var(--fail); } .b-na{ background:var(--na); }

        /* ---- key / value spec ---- */
        .kv{ width:100%; border-collapse:collapse; }
        .kv td{ padding:9px 4px; border-bottom:1px solid var(--line); font-size:12.5px; vertical-align:middle; }
        .kv tr:last-child td{ border-bottom:none; }
        .kv .k{ color:#6b7280; font-weight:600; }
        .kv .k .ar{ display:block; font-size:11px; color:#9aa1ad; font-weight:400; }
        .kv .v{ text-align:right; font-weight:700; color:#1c2431; }

        /* ---- inspection-details strip ---- */
        .facts{ display:flex; flex-wrap:wrap; gap:14px; }
        .fact{ flex:1 1 22%; min-width:120px; }
        .fact .fl{ color:var(--muted); font-size:11px; font-weight:600; }
        .fact .fv{ font-weight:700; font-size:13.5px; margin-top:3px; }

        /* ---- make heading ---- */
        .make-h{ font-family:'Quicksand',sans-serif; font-weight:700; font-size:26px; color:#1c2431; margin:6px 2px 4px; }
        .make-h .u{ display:block; width:64px; height:4px; background:var(--brand-2); border-radius:3px; margin-top:6px; }

        /* ---- report overview donut ---- */
        .overview{ display:flex; align-items:center; gap:30px; flex-wrap:wrap; justify-content:center; }
        .gauge-wrap{ text-align:center; flex:0 0 auto; }
        .gauge{ display:block; margin:0 auto; }
        .cond-legend{ display:flex; gap:14px; justify-content:center; flex-wrap:wrap; margin-top:2px; font-size:11.5px; font-weight:600; color:#4a5563; }
        .cond-legend i{ display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:5px; vertical-align:middle; }
        .gauge-title{ font-family:'Quicksand',sans-serif; font-weight:700; font-size:18px; color:var(--bar); margin-top:8px; }
        .legend{ font-size:13px; }
        .legend .row{ display:flex; align-items:center; gap:9px; margin-bottom:9px; }
        .legend .dot{ width:12px; height:12px; border-radius:3px; }
        .legend b{ margin-left:4px; }

        /* ---- pill legend (diagram style) ---- */
        .pill-legend{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px; }
        .plg{ color:#fff; font-weight:700; font-size:11px; padding:5px 14px; border-radius:10px; }

        /* ---- compliance result ---- */
        .result{ display:flex; gap:14px; flex-wrap:wrap; }
        .res-box{ flex:1 1 30%; min-width:150px; text-align:center; border:1px solid var(--line); border-radius:10px; padding:14px; }
        .res-box .en{ font-weight:700; font-size:13px; }
        .res-box .ar{ display:block; font-size:12px; margin-bottom:6px; }
        .green{ color:var(--brand-2); } .red{ color:#c0392b; }
        .on{ color:var(--brand-2); font-weight:bold; } .off{ color:#c3c9d2; }

        /* ---- bilingual data tables (EV / technical) ---- */
        .dt{ width:100%; border-collapse:separate; border-spacing:0; overflow:hidden; border-radius:10px; border:1px solid var(--line); }
        .dt td,.dt th{ padding:9px 11px; border-bottom:1px solid var(--line); font-size:11.5px; }
        .dt tr:last-child td{ border-bottom:none; }
        .dt th{ background:#f3f5f8; font-weight:700; text-align:center; }
        .dt th .ar{ display:block; font-weight:400; color:#8b93a1; }
        .dt .lbl{ background:#f7f9fb; font-weight:600; }
        .dt .lbl .ar{ display:block; font-size:10.5px; color:#8b93a1; font-weight:400; }
        .dt .c{ text-align:center; }

        /* ---- gallery ---- */
        .gal{ display:flex; flex-wrap:wrap; gap:12px; }
        .gal figure{ margin:0; width:calc(33.333% - 8px); }
        .gal img{ width:100%; height:120px; object-fit:cover; border-radius:10px; border:1px solid var(--line); display:block; }
        .gal figcaption{ text-align:center; font-size:10.5px; color:var(--muted); margin-top:5px; }

        /* ---- signatures ---- */
        .sign{ display:flex; gap:16px; flex-wrap:wrap; }
        .sign .col{ flex:1 1 45%; min-width:220px; }
        .sign .slot{ border-bottom:2px dashed #cfd4dc; height:34px; margin-bottom:6px; }
        .sign .sl{ color:var(--muted); font-size:11px; font-weight:600; }
        .sign .sl .ar{ float:right; }

        /* ---- terms ---- */
        .terms .col{ flex:1 1 45%; min-width:240px; }
        .terms h4{ font-size:13px; margin:0 0 8px; }
        .terms ul{ margin:0; padding-left:16px; font-size:11px; line-height:1.55; color:#3b4453; }
        .terms .ar ul{ padding-left:0; padding-right:16px; }
        .terms li{ margin-bottom:5px; }

        /* ---- cover ---- */
        .cover{ position:relative; border-radius:8px; overflow:hidden; min-height:1080px;
            background:linear-gradient(160deg,var(--navy-2) 0%,var(--bar) 58%,#001a2b 100%); display:flex; flex-direction:column; }
        .cover .top{ flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:50px 30px; }
        .cover .logo-chip{ display:inline-flex; align-items:center; justify-content:center; background:#fff;
            padding:16px 30px; border-radius:20px; margin-bottom:26px; box-shadow:0 14px 40px rgba(0,0,0,.28); }
        .cover .logo-chip img{ height:54px; width:auto; display:block; }
        .cover h1{ font-family:'Quicksand',sans-serif; font-weight:700; font-size:42px; color:#ffffff; line-height:1.2; }
        .cover h1 .g{ color:#4fd18b; }
        .cover .site{ margin-top:14px; color:#b9c6d3; font-size:14px; font-weight:600; }
        .cover .site .ar{ color:#cdd8e2; }
        .cover .cover-art{ width:100%; max-width:470px; margin-top:38px; filter:drop-shadow(0 20px 34px rgba(0,0,0,.4)); }
        .cover .hero{ width:100%; max-height:300px; object-fit:cover; }
        /* cover rating gauge (dark theme) */
        .cover .cover-gauge{ display:block; margin:35px auto 0; }
        .cover .cover-cond-legend{ display:flex; gap:16px; justify-content:center; margin-top:2px;
            font-size:12px; font-weight:600; color:#c2cede; }
        .cover .cover-cond-legend i{ display:inline-block; width:9px; height:9px; border-radius:50%;
            margin-right:6px; vertical-align:middle; }
        .cover .cover-rating-title{ font-family:'Quicksand',sans-serif; font-weight:700; font-size:20px;
            color:#fff; margin-top:10px; letter-spacing:.3px; }
        .cover .stat-chips{ display:flex; gap:12px; justify-content:center; margin-top:22px; }
        .cover .stat-chips .chip{ background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15);
            border-radius:14px; padding:11px 22px; text-align:center; min-width:82px; }
        .cover .stat-chips .chip .n{ font-size:22px; font-weight:800; color:#fff; line-height:1; }
        .cover .stat-chips .chip .l{ font-size:10px; color:#9fb0c0; margin-top:5px; letter-spacing:.6px; text-transform:uppercase; }
        .cover .bar{ background:rgba(0,0,0,.28); border-top:1px solid rgba(255,255,255,.12); color:#fff;
            display:flex; justify-content:space-between; padding:22px 40px; }
        .cover .bar .lab{ color:#8ea3b5; font-size:12px; }
        .cover .bar .v{ font-weight:700; font-size:14px; margin-top:2px; }

        /* ---- thank you ---- */
        .thanks{ position:relative; border-radius:8px; overflow:hidden; min-height:1080px; text-align:center;
            background:linear-gradient(160deg,#eef2f4 0%,#dfe6e5 60%,#cfe0d3 100%);
            display:flex; flex-direction:column; align-items:center; justify-content:space-between; padding:150px 40px 90px; }
        .thanks h1{ font-family:'Quicksand',sans-serif; font-weight:700; font-size:44px; color:#2b3340; line-height:1.25; }
        .thanks h1 .g{ color:var(--brand-2); }
        .thanks .site{ margin-top:16px; color:#5b6472; font-weight:600; }
        .thanks .thanks-logo{ height:66px; width:auto; display:block; }
        .thanks .thanks-art{ width:100%; max-width:440px; filter:drop-shadow(0 16px 26px rgba(20,40,30,.16)); }

        /* keep cards / rows intact across page breaks, never orphan a section bar */
        .item-card,.card,.gal figure,.res-box,.fact{ break-inside:avoid; }
        .sec-bar,.make-h{ break-after:avoid; }

        /* ---- toolbar / print ---- */
        .toolbar{ max-width:900px; margin:0 auto 14px; text-align:right; }
        .btn{ background:var(--brand); color:#fff; border:none; padding:9px 18px; border-radius:10px; cursor:pointer;
            font-size:13px; font-weight:600; text-decoration:none; }
        @page{ size:A4; margin:0; }
        @media print{
            body{ background:#fff; padding:0; }
            .toolbar{ display:none; }
            .sheet{ max-width:none; }
            .page,.cover,.thanks{ margin-bottom:0; border-radius:0; }
            .pb{ page-break-before:always; }
            .card,.item-card,.sec-bar,.badge,.plg,.brand-pill,.gauge,.cover,.thanks{
                -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button class="btn" onclick="window.print()">🖨 Print / Save PDF</button></div>
    <div class="sheet">

        {{-- ============================== COVER ============================== --}}
        <div class="cover">
            <div class="top">
                <span class="logo-chip"><img src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure"></span>
                <h1><span class="g">Comprehensive</span><br>Inspection Report</h1>
                <div class="site">Inspection Checklist for Used Imported Vehicle
                    <span class="ar ar-blk" style="font-size:13px;margin-top:4px">قائمة فحص المركبات المستوردة المستعملة</span>
                </div>

                {{-- Overall Rating gauge — hero of the cover, themed for the navy background --}}
                @php
                    $scoreF = round($tally['pass'] / $tTot * 100, 1);
                    $scoreLbl = rtrim(rtrim(number_format($scoreF, 1), '0'), '.');
                    $bands = [
                        ['Bad', 0, 25, '#e0483d'], ['Fair', 25, 50, '#efb008'],
                        ['Good', 50, 75, '#f2903f'], ['Excellent', 75, 100.01, '#2fa84f'],
                    ];
                    $cond = 'Bad'; $cColor = '#e0483d';
                    foreach ($bands as $b) { if ($scoreF >= $b[1] && $scoreF < $b[2]) { $cond = $b[0]; $cColor = $b[3]; break; } }

                    $cx = 200; $cy = 170;
                    $rBand = 150; $rMinO = 133; $rMinI = 126; $rMajI = 116; $rLabel = 102; $rNeedle = 112;
                    $ang = fn ($v) => deg2rad(225 - 2.7 * max(0, min(100, $v)));
                    $pt  = function ($v, $rr) use ($cx, $cy, $ang) {
                        $a = $ang($v);
                        return [round($cx + $rr * cos($a), 1), round($cy - $rr * sin($a), 1)];
                    };
                    [$ax, $ay] = $pt(0, $rBand); [$bx, $by] = $pt(100, $rBand);
                    $arc = "M {$ax} {$ay} A {$rBand} {$rBand} 0 1 1 {$bx} {$by}";
                    $na = $ang($scoreF); $nperp = $na + M_PI / 2; $nw = 6.5;
                    [$ntx, $nty] = $pt($scoreF, $rNeedle);
                    $nblx = round($cx + $nw * cos($nperp), 1); $nbly = round($cy - $nw * sin($nperp), 1);
                    $nbrx = round($cx - $nw * cos($nperp), 1); $nbry = round($cy + $nw * sin($nperp), 1);
                @endphp
                <svg class="cover-gauge" viewBox="0 0 400 300" width="400" role="img" aria-label="Overall rating {{ $scoreLbl }} of 100">
                    {{-- track --}}
                    <path d="{{ $arc }}" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="22" stroke-linecap="round"/>
                    {{-- fill 0..score in the condition colour --}}
                    <path pathLength="100" d="{{ $arc }}" fill="none" stroke="{{ $cColor }}" stroke-width="22"
                          stroke-linecap="round" stroke-dasharray="{{ $scoreF }} 100" stroke-dashoffset="0"/>
                    {{-- minor ticks every 2.5 --}}
                    @for ($v = 0; $v <= 100; $v += 2.5)
                        @php [$mox,$moy] = $pt($v,$rMinO); [$mix,$miy] = $pt($v,$rMinI); @endphp
                        <line x1="{{ $mox }}" y1="{{ $moy }}" x2="{{ $mix }}" y2="{{ $miy }}" stroke="rgba(255,255,255,.28)" stroke-width="1.4"/>
                    @endfor
                    {{-- major ticks + numbers every 10 --}}
                    @for ($v = 0; $v <= 100; $v += 10)
                        @php [$Mox,$Moy] = $pt($v,$rMinO); [$Mix,$Miy] = $pt($v,$rMajI); [$lx2,$ly2] = $pt($v,$rLabel); @endphp
                        <line x1="{{ $Mox }}" y1="{{ $Moy }}" x2="{{ $Mix }}" y2="{{ $Miy }}" stroke="rgba(255,255,255,.5)" stroke-width="2.2"/>
                        <text x="{{ $lx2 }}" y="{{ $ly2 }}" font-size="12" font-weight="600" fill="#c2cede" text-anchor="middle" dominant-baseline="central" font-family="Poppins,sans-serif">{{ $v }}</text>
                    @endfor
                    {{-- needle + hub --}}
                    <polygon points="{{ $ntx }},{{ $nty }} {{ $nblx }},{{ $nbly }} {{ $nbrx }},{{ $nbry }}" fill="{{ $cColor }}"/>
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="10" fill="#fff" stroke="{{ $cColor }}" stroke-width="3"/>
                    {{-- readout --}}
                    <text x="{{ $cx }}" y="{{ $cy + 44 }}" font-size="27" font-weight="800" fill="#ffffff" text-anchor="middle" font-family="Poppins,sans-serif">{{ $scoreLbl }} <tspan font-size="17" fill="#8ea3b5">/ 100</tspan></text>
                    <text x="{{ $cx }}" y="{{ $cy + 66 }}" font-size="13.5" font-weight="700" fill="{{ $cColor }}" text-anchor="middle" font-family="Poppins,sans-serif">{{ $cond }} Condition</text>
                </svg>
                <div class="cover-cond-legend">
                    <span><i style="background:#e0483d"></i>Bad</span>
                    <span><i style="background:#efb008"></i>Fair</span>
                    <span><i style="background:#f2903f"></i>Good</span>
                    <span><i style="background:#2fa84f"></i>Excellent</span>
                </div>
                <div class="cover-rating-title">Overall Rating</div>

                <div class="stat-chips">
                    <div class="chip"><div class="n" style="color:#7fd39a">{{ $tally['pass'] }}</div><div class="l">Passed</div></div>
                    <div class="chip"><div class="n" style="color:#f6a96b">{{ $tally['fail'] }}</div><div class="l">Failed</div></div>
                    <div class="chip"><div class="n" style="color:#cbd5e1">{{ $tally['na'] }}</div><div class="l">N/A</div></div>
                    <div class="chip"><div class="n">{{ array_sum($tally) }}</div><div class="l">Total</div></div>
                </div>
            </div>
            <div class="bar">
                <div><div class="lab">Report No.</div><div class="v">{{ $reportNo }}</div></div>
                <div style="text-align:center"><div class="lab">Report Date</div><div class="v">{{ $reportDt }}</div></div>
                <div style="text-align:right"><div class="lab">Vehicle</div><div class="v">{{ $val(trim($inspection->car_year.' '.$inspection->car_make.' '.$inspection->car_model)) }}</div></div>
            </div>
        </div>

        {{-- ============================== VEHICLE SUMMARY ============================== --}}
        <div class="page pb">
            <div class="page-header">
                <img class="brand-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
                <span class="doc-tag">Comprehensive Inspection Report</span>
            </div>

        

            {{-- Inspection details strip --}}
            <div class="card tight">
                <div class="facts">
                    <div class="fact"><div class="fl">Date <span class="ar">التاريخ</span></div><div class="fv">{{ $inspDt }}</div></div>
                    <div class="fact"><div class="fl">Time <span class="ar">الوقت</span></div><div class="fv">{{ $reportTm ?: 'N/A' }}</div></div>
                    <div class="fact"><div class="fl">Inspection Type <span class="ar">نوع الفحص</span></div><div class="fv">{{ $typeName }}</div></div>
                    <div class="fact"><div class="fl">VIN <span class="ar">رقم الهيكل</span></div><div class="fv" style="letter-spacing:.5px">{{ $val(strtoupper($inspection->vin ?? '')) }}</div></div>
                </div>
            </div>

            {{-- Owner details --}}
            <div class="sec-bar"><span class="en">Owner Details</span><span class="ar">بيانات المالك</span></div>
            <div class="card tight">
                <div class="facts">
                    <div class="fact"><div class="fl">Owner Name <span class="ar">اسم المالك</span></div><div class="fv">{{ $val($inspection->customer_name) }}</div></div>
                    <div class="fact"><div class="fl">Phone <span class="ar">رقم الهاتف</span></div><div class="fv" style="direction:ltr">{{ $val($inspection->customer_phone) }}</div></div>
                    <div class="fact"><div class="fl">Email <span class="ar">البريد الإلكتروني</span></div><div class="fv" style="word-break:break-all">{{ $val($inspection->customer_email) }}</div></div>
                    <div class="fact"><div class="fl">Reference <span class="ar">المرجع</span></div><div class="fv">{{ $reportNo }}</div></div>
                </div>
            </div>

            <div class="sec-bar"><span class="en">Vehicle Summary</span><span class="ar">بيانات المركبة</span></div>

            <div class="make-h">{{ $makeHeading }}<span class="u"></span></div>

            <div class="grid2">
                <div class="item-card" style="min-height:auto">
                    @if ($heroPhoto)
                        <img src="{{ $heroPhoto }}" style="width:100%;height:210px;object-fit:cover;border-radius:14px;border:1px solid var(--line)">
                    @else
                        <img src="{{ asset('img/pdf_design/cover-photo.webp') }}" style="width:100%;height:210px;object-fit:contain;border-radius:14px;background:#f3f5f8;padding:8px">
                    @endif
                </div>
                <div class="item-card" style="min-height:auto">
                    <table class="kv">
                        @foreach ($specs as $sp)
                            <tr>
                                <td class="k">{{ $sp[0] }}<span class="ar">{{ $sp[1] }}</span></td>
                                <td class="v">{{ $sp[2] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            {{-- Inspection result (compliance) --}}
            <div class="card">
                <div class="result">
                    <div class="res-box">
                        <span class="ar red">غير مطابق للمتطلبات</span>
                        <span class="en red">Non-Compliance</span> &nbsp;{!! $ck(!$compliant) !!}
                    </div>
                    <div class="res-box">
                        <span class="ar green">مطابق للمتطلبات</span>
                        <span class="en green">Compliance to Requirements</span> &nbsp;{!! $ck($compliant) !!}
                    </div>
                    <div class="res-box" style="text-align:left">
                        <div class="fl muted" style="font-size:11px;font-weight:600">Overall Condition <span class="ar">الحالة العامة</span></div>
                        <div class="fv" style="font-weight:700;margin:2px 0 8px">{{ $condition }}</div>
                        <div class="fl muted" style="font-size:11px;font-weight:600">Recommendation <span class="ar">التوصية</span></div>
                        <div class="fv" style="font-weight:700">{{ $recommend }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================== EV & PHEV (bilingual, if present) ============================== --}}
        @if ($hasEv)
        <div class="page pb">
            <div class="page-header">
                <img class="brand-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
                <span class="doc-tag">Comprehensive Inspection Report</span>
            </div>
            <div class="sec-bar"><span class="en">EV &amp; PHEV</span><span class="ar">للسيارات الكهربائية والهجينة</span></div>
            @php $evBanner = $bannerUrl('EV and PHEV') ?: $bannerUrl('EV & PHEV Details'); @endphp
            @if ($evBanner)<img class="sec-banner" src="{{ $evBanner }}" alt="">@endif
            <div class="card">
                <table class="dt">
                    <tr>
                        <td class="lbl">Type Approval Certificate<span class="ar">شهادة مطابقة الطراز</span></td>
                        <td class="c">N/A {!! $ck(true) !!}</td>
                        <td class="lbl">Footprint (M2)<span class="ar">البصمة</span></td>
                        <td class="c">{{ $ev('Footprint (M2)') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Full Battery Charge Time<span class="ar">وقت الشحن الكامل</span></td>
                        <td class="c">{{ $ev('Full Battery Charge Time (Min or H)') }}</td>
                        <td class="lbl">Battery Capacity (KW/h)<span class="ar">قدرة البطارية</span></td>
                        <td class="c">{{ $ev('Battery Capacity (KW/h)') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Battery Type<span class="ar">نوع البطارية</span></td>
                        <td class="c">{{ $ev('Battery Type') }}</td>
                        <td class="lbl">Electric Consumption<span class="ar">الاستهلاك الكهربائي</span></td>
                        <td class="c">{{ $ev('Electric Consumption (KWh/100KM)') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Battery Voltage (V)<span class="ar">فولت البطارية</span></td>
                        <td class="c">{{ $ev('Battery Voltage (V)') }}</td>
                        <td class="lbl">Equivalent fuel economy<span class="ar">اقتصاد الوقود المكافئ</span></td>
                        <td class="c">{{ $ev('Equivalent fuel economy (KM/L)') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        {{-- ============================== TECHNICAL MEASUREMENTS (bilingual, if present) ============================== --}}
        @if ($hasTech)
        <div class="page pb">
            <div class="page-header">
                <img class="brand-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
                <span class="doc-tag">Comprehensive Inspection Report</span>
            </div>
            <div class="sec-bar"><span class="en">Technical Inspection Measurements</span><span class="ar">قياسات الفحص الفني</span></div>
            @php $techBanner = $bannerUrl('Technical Inspection Measurements') ?: $bannerUrl('Technical & Emissions Tests'); @endphp
            @if ($techBanner)<img class="sec-banner" src="{{ $techBanner }}" alt="">@endif
            <div class="card">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Inspection Item<span class="ar" style="font-weight:400">بند الفحص</span></th>
                            <th>Criteria Limit<span class="ar" style="font-weight:400">حد المعيار</span></th>
                            <th>Measurement<span class="ar" style="font-weight:400">القياس</span></th>
                            <th>Result<span class="ar" style="font-weight:400">النتيجة</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $techRows = [
                                ['Main Brake (Static Device)', 'المكابح', 'Brake efficiency ≥ 45%', 'Main Brake (Static Device) — Automated brake efficiency check'],
                                ['Gaseous Pollutants (CO)', 'الملوثات الغازية', '(CO) ≤ 3.5%', 'Pollution - Gasoline Engines (CO)'],
                                ['Gaseous Pollutants (HC)', 'الملوثات الغازية', '(HC) ≤ 1200 ppm', 'Pollution - Gasoline Engines (HC)'],
                                ['Smoke Density (Diesel)', 'كثافة الدخان', 'Reading ≤ 40%', 'Smoke Density - Diesel Engines'],
                                ['Glass Transparency', 'شفافية الزجاج', 'Transparency ≥ 70%', 'Glass Transparency'],
                                ['Noise Emissions', 'التلوث الضوضائي', 'Per clause 1.19', 'Noise Emissions'],
                            ];
                        @endphp
                        @foreach ($techRows as $tr)
                            <tr>
                                <td class="lbl">{{ $tr[0] }}<span class="ar">{{ $tr[1] }}</span></td>
                                <td class="c">{{ $tr[2] }}</td>
                                <td class="c">{{ $reading($tr[3]) }}</td>
                                <td class="c">{!! $badge($techState($tr[3])) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ============================== DETAILED CHECKLIST — card grid per section ============================== --}}
        <div class="page pb">
            <div class="page-header">
                <img class="brand-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
                <span class="doc-tag">Comprehensive Inspection Report</span>
            </div>
            @php $lastGroup = null; $shownGroupBanners = []; @endphp
            @foreach ($inspection->type->sections as $section)
                @continue(in_array($section->section_name, $skip, true))
                @php
                    [$sNum, $sTitle] = $splitNum($section->section_name);
                    $sAr  = $section->section_name_ar ?: ($secAr[$section->section_name] ?? '');
                    $steps = $section->steps;
                @endphp

                @if ($section->group_name && $section->group_name !== $lastGroup)
                    @php
                        [$gNum, $gTitle] = $splitNum($section->group_name); $lastGroup = $section->group_name;
                        $gBanner = in_array($section->group_name, $shownGroupBanners, true) ? null : $bannerUrl($section->group_name);
                    @endphp
                    @if ($gBanner)
                        @php $shownGroupBanners[] = $section->group_name; @endphp
                        {{-- keep group title + banner together so they never split across a page --}}
                        <div class="group-lead">
                            <div class="make-h" style="font-size:20px">{{ $gTitle }}<span class="u"></span></div>
                            <img class="sec-banner" style="margin-bottom:0" src="{{ $gBanner }}" alt="">
                        </div>
                    @else
                        <div class="make-h" style="font-size:20px;margin-top:12px">{{ $gTitle }}<span class="u"></span></div>
                    @endif
                @endif

                <div class="sec-bar" style="margin-top:14px">
                    <span class="en">{{ $sTitle }}</span>
                    @if ($sAr)<span class="ar">{{ $sAr }}</span>@endif
                </div>
                @php $secBanner = $bannerUrl($section->section_name); @endphp
                @if ($secBanner)<img class="sec-banner" src="{{ $secBanner }}" alt="">@endif

                @php
                    // N/A (and unanswered) items are left out of the report entirely.
                    $steps = $steps->filter(fn ($s) => Inspection::isReportable($answers->get($s->id)))->values();
                @endphp
                <div class="grid2">
                    @foreach ($steps as $step)
                        @php
                            $state = $rowState($step);
                            $d = $answers->get($step->id);
                            // $d is null for an unanswered step — guard, as report.blade.php does.
                            $note = optional($d)->descriptive_answer ?: (optional($d)->remedial_suggestion ?? null);
                            $photos = $stepPhotos($step);
                        @endphp
                        <div class="item-card">
                            <div class="item-head">
                                <span class="item-title">{{ $step->question }}
                                    @if ($step->question_ar)<span class="ar">{{ $step->question_ar }}</span>@endif
                                </span> 
                                {!! $badge($state) !!}
                            </div>
                            @if ($note)
                                <div class="item-note">{{ $note }}</div>
                            @endif
                            @if ($photos->isNotEmpty())
                                <div class="thumbs">
                                    @foreach ($photos->take(4) as $ph)
                                        <img class="thumb" src="{{ $ph->url }}" alt="">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- ============================== GENERAL PHOTOS ============================== --}}
        @if (! empty($reportPhotos))
        <div class="page pb">
            <div class="page-header">
                <img class="brand-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
                <span class="doc-tag">Comprehensive Inspection Report</span>
            </div>
            <div class="sec-bar"><span class="en">General Photos</span><span class="ar">صور عامة</span></div>
            <div class="card">
                <div class="gal">
                    @foreach ($reportPhotos as $p)
                        <figure>
                            <img src="{{ $p['media']->url }}" alt="">
                            <figcaption>{{ $p['caption'] }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ============================== INSPECTOR COMMENT ============================== --}}
        <div class="page pb">
            <div class="page-header">
                <img class="brand-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
                <span class="doc-tag">Comprehensive Inspection Report</span>
            </div>
            <div class="sec-bar"><span class="en">Inspector Comment</span><span class="ar">ملاحظات المفتش</span></div>
            <div class="card">
                @php $summary = $val($inspection->summary) === 'N/A' ? null : $inspection->summary; @endphp
                @if ($summary)
                    <div style="white-space:pre-line;font-weight:600;color:#2b3340;line-height:1.7">{{ $summary }}</div>
                @else
                    <div class="muted">No additional inspector comments were recorded.</div>
                @endif
            </div>

            {{-- Signatures --}}
            <div class="sec-bar" style="margin-top:6px"><span class="en">Signatures</span><span class="ar">التواقيع</span></div>
            <div class="card">
                <div class="sign">
                    <div class="col">
                        <div class="fv" style="font-weight:700;margin-bottom:6px">{{ $val(optional($inspection->technician)->name) }}</div>
                        <div class="slot"></div>
                        <div class="sl">Inspector — Sign &amp; Date <span class="ar">المفتش — التوقيع والتاريخ</span></div>
                    </div>
                    <div class="col">
                        <div class="fv" style="font-weight:700;margin-bottom:6px">&nbsp;</div>
                        <div class="slot"></div>
                        <div class="sl">Technical Manager — Sign &amp; Date <span class="ar">المدير الفني — التوقيع والتاريخ</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================== TERMS & CONDITIONS ============================== --}}
        <div class="page pb">
            <div class="page-header">
                <img class="brand-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
                <span class="doc-tag">Comprehensive Inspection Report</span>
            </div>
            <div class="sec-bar"><span class="en">Terms &amp; Conditions</span><span class="ar">الشروط والأحكام</span></div>
            <div class="card terms">
                <div class="grid2">
                    <div class="col">
                        <h4>Terms and conditions</h4>
                        <ul>
                            <li>This report is for the vehicle provided by the customer and tested/inspected only.</li>
                            <li>This report is considered void in the event of any scraping, modification, deletion, or addition.</li>
                            <li>The data in this report is confidential and private and no company personnel has the right to publish or announce it except with the prior approval of the customer or according to a court ruling or a request from the competent authorities.</li>
                            <li>The vehicle owner (customer) is obligated to attend again if requested.</li>
                        </ul>
                    </div>
                    <div class="col ar">
                        <h4>الشروط والأحكام</h4>
                        <ul>
                            <li>هذا التقرير يخص المركبة التي قدمها العميل وتم اختبارها/فحصها فقط.</li>
                            <li>يعتبر هذا التقرير لاغياً في حالة حدوث أي كشط أو تعديل أو حذف أو إضافة.</li>
                            <li>بيانات هذا التقرير سرية وخاصة ولا يحق لأي من أفراد الشركة نشرها أو الإعلان عنها إلا بموافقة مسبقة من العميل وبموجب حكم قضائي أو طلب من الجهات المختصة.</li>
                            <li>يلتزم صاحب المركبة (العميل) بالحضور مرة أخرى إذا طُلب منه.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================== THANK YOU ============================== --}}
        <div class="thanks pb">
            <img class="thanks-logo" src="{{ asset('img/pdf_design/auto-logo.svg') }}" alt="Auto Assure">
            <div>
                <h1>Thank You for Choosing<br><span class="g">Auto Assure</span></h1>
                <div class="site">Inspection Checklist for Used Imported Vehicle</div>
            </div>
            <img class="thanks-art" src="{{ asset('img/pdf_design/footer.webp') }}" alt="">
        </div>

    </div>
    @if(request()->boolean('download'))
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 350);
            });
        </script>
    @endif
</body>
</html>
