{{--
    Downloadable PDF edition of the inspection report, rendered by dompdf
    (barryvdh/laravel-dompdf). It mirrors the on-screen report
    (inspections/report.blade.php) — same data, sections, colours and fonts —
    within what dompdf can draw:

      - no flexbox/grid, so every row and column is a table;
      - no CSS gradients, so the cover canvas and the footer bar are pre-drawn
        PNGs (public/img/pdf_design/cover-bg.png, footer-gradient.png);
      - no web fonts over HTTP, so Poppins and Quicksand ship in resources/fonts;
      - the gauge and area icons are SVGs handed over as images.

    Images are read from disk (public/), never over HTTP. The cover fills page 1;
    the running header is drawn on every page but the cover paints over it there.
--}}
@php
    use App\Models\Inspection;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $isAr = ($lang ?? 'en') === 'ar';
    $pick = fn ($en, $ar) => $isAr && filled($ar) ? $ar : $en;
    $val  = fn ($v) => ($v === null || $v === '') ? 'N/A' : $v;
    $start = $isAr ? 'right' : 'left';
    $end   = $isAr ? 'left' : 'right';

    // Poppins / Quicksand carry no Arabic glyphs — the Arabic edition uses DejaVu.
    $fBody = $isAr ? "'DejaVu Sans'" : "'Poppins'";
    $fSemi = $isAr ? "'DejaVu Sans'" : "'Poppins SemiBold'";
    $fHead = $isAr ? "'DejaVu Sans'" : "'Quicksand'";
    $fHeadSemi = $isAr ? "'DejaVu Sans'" : "'Quicksand SemiBold'";
    $font = fn ($f) => resource_path('fonts/'.$f);

    // A URL on this site -> the file under public/ (dompdf reads local files).
    $local = function (?string $url) {
        if (! $url) return null;
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $file = public_path(ltrim(rawurldecode($path), '/'));
        return is_file($file) ? $file : null;
    };
    $svgImg = fn (string $svg) => 'data:image/svg+xml;base64,'.base64_encode($svg);

    // Fixed headings, Arabic as on the screen report.
    $L = function (string $key) use ($isAr) {
        $ar = [
            'Inspection Report' => 'تقرير الفحص', 'Inspector Comment' => 'ملاحظات الفاحص',
            'Inspection Summary' => 'ملخص الفحص', 'Reference' => 'المرجع', 'Inspection Date' => 'تاريخ الفحص',
            'Plate No' => 'رقم اللوحة', 'Diagnostic Media' => 'تقارير الفحص بالكمبيوتر', 'General Photos' => 'صور عامة',
            'Vehicle Photos' => 'صور المركبة', 'Vehicle Summary' => 'بيانات المركبة', 'Make' => 'الشركة المصنعة',
            'Model' => 'الطراز', 'Year' => 'السنة', 'Vehicle Condition' => 'حالة المركبة', 'Fuel Type' => 'نوع الوقود',
            'Odometer' => 'عداد المسافة', 'Exterior Colour' => 'اللون الخارجي', 'Customer Name' => 'اسم العميل',
            'Damage Points' => 'مواضع الأضرار', 'Paint Inspection Images' => 'صور فحص الطلاء', 'Signatures' => 'التواقيع',
            'Terms & Conditions' => 'الشروط والأحكام', 'Overall Rating' => 'التقييم العام', 'Recommendation' => 'التوصية',
            'Excellent' => 'ممتاز', 'Very Good' => 'جيد جداً', 'Good' => 'جيد', 'Fair' => 'مقبول', 'Poor' => 'ضعيف', 'Critical' => 'حرج',
        ][$key] ?? null;
        return $isAr && $ar ? $ar : $key;
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
    $overallCond = Inspection::CONDITIONS[$inspection->overall_condition] ?? null;
    $weightedVerdict = $inspection->weightedVerdict($sectionSummaries ?? collect());
    if ($weightedVerdict) {
        $recommend        = $isAr ? $weightedVerdict['band']['guide_ar'] : $weightedVerdict['band']['guide'];
        $overallRatingVal = $weightedVerdict['rating'];
        $scorePct         = $weightedVerdict['score'];
        $condition        = $weightedVerdict['band']['condition'];
    } elseif ($isAr) {
        $recommend   = Inspection::RECOMMENDATIONS_AR[$inspection->recommendation] ?? $recommend;
        $overallCond = Inspection::CONDITIONS_AR[$inspection->overall_condition] ?? $overallCond;
    }
    $condColors = ['Excellent' => '#2fa84f', 'Very Good' => '#5ab84d', 'Good' => '#f2903f', 'Fair' => '#efb008', 'Poor' => '#e0483d', 'Critical' => '#e0483d'];

    // ---- gauge (same geometry and bands as the screen report) ----
    $scoreF = (float) $scorePct;
    $bands = [['Poor', 0, 20, '#e0483d'], ['Fair', 20, 40, '#efb008'], ['Good', 40, 60, '#f2903f'], ['Very Good', 60, 80, '#5ab84d'], ['Excellent', 80, 100.01, '#2fa84f']];
    $cColor = '#e0483d'; $band = 'Poor';
    foreach ($bands as $b) { if ($scoreF >= $b[1] && $scoreF < $b[2]) { $band = $b[0]; $cColor = $b[3]; break; } }
    $condition = $condition ?: $band;
    $condColor = $condColors[$condition] ?? $cColor;
    $gauge = '';
    if ($usesCalculated) {
        $cx = 200; $cy = 170; $rBand = 150; $rMinO = 133; $rMinI = 126; $rMajI = 116; $rLabel = 102; $rNeedle = 112;
        $ang = fn ($v) => deg2rad(225 - 2.7 * max(0, min(100, $v)));
        $pt  = function ($v, $rr) use ($cx, $cy, $ang) { $a = $ang($v); return [round($cx + $rr * cos($a), 1), round($cy - $rr * sin($a), 1)]; };
        [$ax, $ay] = $pt(0, $rBand); [$bx, $by] = $pt(100, $rBand);
        $arc = "M {$ax} {$ay} A {$rBand} {$rBand} 0 1 1 {$bx} {$by}";
        // The coloured part of the arc, drawn as its own path (dompdf has no dash offsets).
        [$sx, $sy] = $pt($scoreF, $rBand);
        $large = $scoreF * 2.7 > 180 ? 1 : 0;
        $valArc = $scoreF > 0 ? "M {$ax} {$ay} A {$rBand} {$rBand} 0 {$large} 1 {$sx} {$sy}" : null;
        $na = $ang($scoreF); $nperp = $na + M_PI / 2; $nw = 6.5;
        [$ntx, $nty] = $pt($scoreF, $rNeedle);
        $nblx = round($cx + $nw * cos($nperp), 1); $nbly = round($cy - $nw * sin($nperp), 1);
        $nbrx = round($cx - $nw * cos($nperp), 1); $nbry = round($cy + $nw * sin($nperp), 1);

        $g = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">';
        $g .= '<path d="'.$arc.'" fill="none" stroke="#2b4760" stroke-width="22" stroke-linecap="round"/>';
        if ($valArc) $g .= '<path d="'.$valArc.'" fill="none" stroke="'.$cColor.'" stroke-width="22" stroke-linecap="round"/>';
        for ($v = 0; $v <= 100; $v += 2.5) {
            [$x1, $y1] = $pt($v, $rMinO); [$x2, $y2] = $pt($v, $rMinI);
            $g .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="#4d6479" stroke-width="1.4"/>';
        }
        for ($v = 0; $v <= 100; $v += 10) {
            [$x1, $y1] = $pt($v, $rMinO); [$x2, $y2] = $pt($v, $rMajI); [$lx, $ly] = $pt($v, $rLabel);
            $g .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="#8193a4" stroke-width="2.2"/>';
            $g .= '<text x="'.$lx.'" y="'.($ly + 4).'" font-size="12" font-family="Helvetica" fill="#c2cede" text-anchor="middle">'.$v.'</text>';
        }
        $g .= '<polygon points="'.$ntx.','.$nty.' '.$nblx.','.$nbly.' '.$nbrx.','.$nbry.'" fill="'.$cColor.'"/>';
        $g .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="10" fill="#ffffff" stroke="'.$cColor.'" stroke-width="3"/>';
        $g .= '</svg>';
        $gauge = $svgImg($g);
    }

    // ---- title ----
    $reportKind = $inspection->type?->reportKind() ?: 'Comprehensive';
    if ($isAr) {
        $reportKind = $inspection->type?->name_ar ?: $reportKind;
    }
    if ($inspection->type && ! ($inspection->type->show_name_in_report ?? true)) {
        $reportKind = '';
    }
    $docTag = trim($reportKind.' '.$L('Inspection Report'));
    $basicReport = (bool) $inspection->type?->isBasicReport();
    // Quick / Fleet print Damage Points straight after the photos. Matched on the
    // name's first word so a renamed template ("Fleet") still qualifies.
    $quickOrFleet = $basicReport || (bool) preg_match('/^\s*(quick|fleet)\b/i', (string) $inspection->type?->name);

    // ---- vehicle summary rows (same order as the cover card) ----
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
    // Three columns under the gauge; two when the card sits beside the image.
    $colCount = $usesCalculated ? 3 : 2;
    $rowCols = array_chunk($rows, (int) ceil(count($rows) / $colCount));

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
    $diagnosticDocs = ! $wantsDiagnostic ? collect() : (optional($inspection->details
        ->first(fn ($d) => is_null($d->inspection_step_id) && is_null($d->inspection_section_id)))
        ?->media?->filter(fn ($m) => $m->isDocument()) ?? collect());

    // Cover image: the vehicle image, else the first photo, else the stock art.
    $coverImg = $inspection->vehicle_image
        ? ($local(\App\Support\Thumbnailer::url($inspection->vehicle_image, 900)) ?: $local($inspection->vehicleImageUrl()))
        : ($photos[0]['file'] ?? null);
    $coverImg = $coverImg ?: (is_file(public_path('img/pdf_design/cover-photo.webp')) ? public_path('img/pdf_design/cover-photo.webp') : null);

    // ---- per-area summary notes, with the screen report's icons ----
    $areaIcon = function ($name) use ($svgImg) {
        $n = strtolower((string) $name);
        $inner = match (true) {
            str_contains($n, 'exterior') => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 1 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>',
            str_contains($n, 'interior') => '<path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"/><path d="M3 11v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v2H7v-2a2 2 0 0 0-4 0Z"/><path d="M5 18v2M19 18v2"/>',
            str_contains($n, 'engine') => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/>',
            str_contains($n, 'brake') => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/>',
            str_contains($n, 'transmission') || str_contains($n, 'gearbox') => '<line x1="6" x2="6" y1="3" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/>',
            str_contains($n, 'suspension') || str_contains($n, 'steering') => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2"/><path d="M12 3v7M4.5 8.5 10 12M19.5 8.5 14 12M12 14v7"/>',
            str_contains($n, 'tire') || str_contains($n, 'tyre') || str_contains($n, 'wheel') => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><path d="M12 8v8M8 12h8"/>',
            str_contains($n, 'undercarriage') => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
            str_contains($n, 'safety') => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
            str_contains($n, 'road') || str_contains($n, 'drive') => '<circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/>',
            str_contains($n, 'electr') => '<polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
            default => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>',
        };
        return $svgImg('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0b8a68" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$inner.'</svg>');
    };
    $areaNotes = [];
    foreach (($summaryTypes ?? []) as $tid => $tname) {
        if (filled($summaries[$tid] ?? null)) $areaNotes[] = ['name' => $tname, 'note' => $summaries[$tid], 'icon' => $areaIcon($tname)];
    }

    // ---- per-section / group banner images (same mapping as the screen report) ----
    $sectionBanners = [
        'EV and PHEV' => 'EV&PHEV.png', 'EV & PHEV Details' => 'EV&PHEV.png',
        'Technical Inspection Measurements' => 'Technical-Inspection-Measurements.png',
        'Technical & Emissions Tests' => 'Technical-Inspection-Measurements.png',
        '1. Inspection Exterior' => 'Inspection-Exterior.png', '1.7 Brake System' => 'Brake-System.png',
        '1.7.7 Automated brake efficiency check (using static or dynamic inspection device)' => 'Automated-brake-efficiency-check.png',
        '1.8 Lights' => 'Lights.png', '1.10 Steering System' => 'Steering-System.png',
        '1.11 Suspension System' => 'Suspension-System.png', '1.12 Exhaust System' => 'Exhaust-System.png',
        '1.22 Gaseous Pollutants' => 'Gaseous-Pollutants.png', '2. Interior Inspection' => 'Interior-Inspection.png',
        '6. Buses (model year 2023 and above)' => 'Buses.png',
    ];
    $banner = function ($name) use ($sectionBanners) {
        $file = isset($sectionBanners[$name]) ? public_path('img/pdf_design/'.$sectionBanners[$name]) : null;
        return $file && is_file($file) ? $file : null;
    };
    $splitNum = fn ($s) => preg_match('/^(\d+(?:\.\d+)*)[\).]?\s+(.+)$/u', (string) $s, $m) ? $m[2] : (string) $s;

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
            'groupKey' => $section->group_name,
            'group'    => $section->group_name ? $splitNum($pick($section->group_name, $section->group_name_ar)) : null,
            'name'     => $splitNum($pick($section->section_name, $section->section_name_ar)),
            'banner'   => $banner($section->section_name),
            'rating'   => $section->weight !== null ? (float) (optional($meta)->rating ?: 0) : 0,
            'steps'    => $steps,
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

    // Pass / fail / n-a badge, same colours as the screen report.
    $badge = fn ($state) => $state === 'pass'
        ? '<span class="badge b-pass">&#10003;</span>'
        : ($state === 'fail' ? '<span class="badge b-fail">&#10007;</span>' : '<span class="badge b-na">~</span>');
    // Whole stars (dompdf cannot clip a half-filled glyph).
    $stars = function (float $rating, string $off = '#dfe3ea', int $size = 14) {
        $full = (int) round($rating);
        $out = '';
        for ($i = 1; $i <= 5; $i++) {
            $out .= '<span style="font-family:\'DejaVu Sans\';font-size:'.$size.'px;color:'.($i <= $full ? '#f1b44c' : $off).';">&#9733;</span>';
        }
        return $out;
    };
    $fmtRating = fn ($r) => rtrim(rtrim(number_format($r, 1), '0'), '.');

    // Inspector Comment sits on the cover, but the cover is one fixed page: a
    // comment too long for the room left under the cards moves to the top of
    // page 2 instead of spilling the navy cover onto it. Room is smaller under
    // the gauge layout (summary card + verdict strip) than beside the image.
    $comment = filled($inspection->summary) ? $pick($inspection->summary, $inspection->summary_ar) : null;
    $commentRoom = $usesCalculated ? 700 : 1400;
    $commentOnCover = $comment !== null && mb_strlen($comment) + 90 * substr_count($comment, "\n") <= $commentRoom;

    $logo = public_path('img/pdf_design/auto-logo.png');
    $coverBg = public_path('img/pdf_design/cover-bg.png');
    $footBg = public_path('img/pdf_design/footer-gradient.png');
@endphp
<!doctype html>
<html lang="{{ $isAr ? 'ar' : 'en' }}" @if($isAr) dir="rtl" @endif>
<head>
<meta charset="utf-8">
<title>{{ $docTag }} — {{ $reportNo }}</title>
<style>
    @font-face { font-family: 'Poppins'; font-weight: normal; src: url('{{ $font('Poppins-Regular.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Poppins'; font-weight: bold; src: url('{{ $font('Poppins-Bold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Poppins SemiBold'; font-weight: normal; src: url('{{ $font('Poppins-SemiBold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Poppins ExtraBold'; font-weight: normal; src: url('{{ $font('Poppins-ExtraBold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Poppins Italic'; font-weight: normal; src: url('{{ $font('Poppins-SemiBoldItalic.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Quicksand'; font-weight: normal; src: url('{{ $font('Quicksand-Bold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Quicksand'; font-weight: bold; src: url('{{ $font('Quicksand-Bold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Quicksand SemiBold'; font-weight: normal; src: url('{{ $font('Quicksand-SemiBold.ttf') }}') format('truetype'); }

    /* Page 1 is the full-bleed cover; the other pages keep a band for the running
       header at the top and the footer bar at the bottom. */
    /* Top band = 66px header + 12px gap, so content that dompdf starts a little
       high on a new page still clears the header. */
    @page { margin: 78px 0 60px 0; }
    * { box-sizing: border-box; }
    /* body only — a margin on html overrides the @page margins in dompdf. */
    body { margin: 0; padding: 0; }
    body { font-family: {!! $fBody !!}; font-size: 11px; color: #1c2430; }
    /* Page colour as a fixed layer drawn first on every page — a body background
       would be painted over the header and footer bands. */
    .page-bg { position: fixed; top: -78px; bottom: -60px; left: 0; right: 0; background: #eef1f5; z-index: -10; }
    @if($isAr) body { direction: rtl; text-align: right; } @endif
    table { border-collapse: collapse; width: 100%; }
    td { vertical-align: top; }

    /* ---- running header (pages 2+) and footer (all pages) ---- */
    .run-head { position: fixed; z-index: 5; top: -78px; left: 0; right: 0; height: 66px; background: #eef1f5; padding: 17px 24px 0; }
    .run-head img { height: 32px; }
    .run-head .doc-tag { font-family: {!! $fHeadSemi !!}; font-size: 12px; color: #aab0bb; }
    .run-foot { position: fixed; z-index: 5; bottom: -60px; left: 0; right: 0; height: 60px; background: #0a1f33 url('{{ $footBg }}') repeat-y; }
    .run-foot table { margin-top: 10px; }
    .run-foot td { font-family: {!! $isAr ? "'DejaVu Sans'" : "'Poppins Italic'" !!}; font-size: 9px; color: #fff; line-height: 1.1; letter-spacing: .6px; padding: 0 34px; }

    /* ---- cover ---- */
    /* position + z-index lift the cover over the running header on page 1. */
    .cover { position: relative; z-index: 10; margin-top: -78px; height: 1062px; background: #00263d url('{{ $coverBg }}') no-repeat; page-break-after: always; overflow: hidden; }
    /* Without the gauge (Quick / Fleet) the cover is short: it ends after the
       Inspector Comment and the report carries on below on the same page,
       instead of leaving the rest of a full navy page empty. */
    .cover--short { height: auto; padding-bottom: 8px; page-break-after: auto; margin-bottom: 18px; }
    .rc-head { background: #fff; padding: 18px 34px; border-bottom: 2px solid #2fa84f; }
    .rc-head img { height: 46px; }
    .rc-head h1 { margin: 0; font-family: {!! $fHead !!}; font-weight: bold; font-size: 26px; color: #0f2d43; text-align: {{ $end }}; }
    .rc-head h1 .g { color: #2fa84f; }
    .rc-hero { padding: 24px 34px 18px; }
    .car { position: relative; }
    .car img.photo { width: 100%; height: 250px; border-radius: 14px; }
    .inspected { position: absolute; top: 12px; {{ $end }}: 12px; background: #2fa84f; color: #fff; font-family: {!! $fSemi !!};
        font-size: 10px; letter-spacing: .4px; padding: 4px 12px; border-radius: 12px; }
    .gauge-score { font-family: 'Poppins ExtraBold', {!! $fBody !!}; font-size: 30px; color: #fff; text-align: center; margin-top: -40px; }
    .gauge-score span { font-size: 16px; color: #8ea3b5; }
    .gauge-pill { text-align: center; margin-top: 8px; }
    .gauge-pill span { font-family: {!! $fSemi !!}; font-size: 14px; padding: 4px 20px; border-radius: 14px; border: 1.5px solid; }
    .gauge-legend { text-align: center; margin-top: 14px; font-family: {!! $fSemi !!}; font-size: 10px; color: #c2cede; }
    .gauge-legend span { margin: 0 5px; }
    .gauge-legend i { display: inline-block; width: 8px; height: 8px; border-radius: 4px; margin-{{ $end }}: 4px; }

    .rc-card { background: #fff; border-radius: 12px; }
    .rc-card__head { background: #0f2d43; color: #fff; padding: 10px 18px; border-radius: 12px 12px 0 0;
        font-family: {!! $fHead !!}; font-weight: bold; font-size: 15px; }
    .rc-card__body { padding: 6px 18px 10px; }
    .rc-kv td { padding: 6px 2px; border-bottom: 1px solid #eef0f4; font-size: 10.5px; vertical-align: middle; }
    .rc-kv tr.last td { border-bottom: none; }
    /* Half-width card beside the photo: tighter text so the two columns fit. */
    .rc-card--narrow .rc-card__body { padding: 4px 12px 8px; }
    .rc-card--narrow .rc-kv td { font-size: 9.5px; padding: 5px 2px; }
    /* Labels stay on one line; long values wrap instead. */
    .rc-kv .k { color: #5b6472; white-space: nowrap; }
    .rc-kv .v { text-align: {{ $end }}; font-family: {!! $fBody !!}; font-weight: bold; color: #0f2d43; padding-{{ $start }}: 6px; }
    .rc-verdict td { background: #fff; padding: 14px 22px; }
    .rc-verdict .lbl { font-family: {!! $fSemi !!}; font-size: 12px; color: #5b6472; }
    .rc-verdict .num { font-family: {!! $fHead !!}; font-weight: bold; font-size: 20px; color: #0f2d43; }
    .rc-verdict .rec { font-family: {!! $fHead !!}; font-weight: bold; font-size: 17px; color: #0f2d43; margin-top: 4px; }
    /* dompdf spaces Poppins lines from its tall font box: a unitless 1.0 here
       matches the browser's ~1.5, and px values are not honoured. */
    .rc-comment__body { padding: 12px 20px; font-size: 11.5px; line-height: 1.05; color: #2b3340; }

    /* ---- content pages ---- */
    .content { padding: 0 24px 10px; }
    .sec-bar { background: #00263d; color: #fff; border-radius: 10px; border-{{ $start }}: 5px solid #12a150;
        padding: 10px 12px; margin: 14px 0 14px; font-family: {!! $fHeadSemi !!}; font-size: 16px; page-break-after: avoid; page-break-inside: avoid; }
    .sec-bar .rate { float: {{ $end }}; font-size: 12px; color: #cfd4dc; }
    .make-h { font-family: {!! $fHead !!}; font-weight: bold; font-size: 20px; color: #1c2431; margin: 18px 2px 2px; page-break-after: avoid; }
    .make-h .u { width: 64px; height: 4px; background: #12a150; border-radius: 3px; margin-top: 6px; }
    .sec-banner { width: 100%; border-radius: 10px; border: 1px solid #e7eaef; margin: 4px 0 14px; }
    .card { background: #fff; border: 1px solid #e7eaef; border-radius: 8px; padding: 10px; margin-bottom: 14px; }

    /* two-up card grid */
    .grid2 { border-collapse: separate; border-spacing: 0 10px; width: 100%; margin-top: -10px; }
    .grid2 td.cell { width: 49%; background: #fff; border: 1px solid #e7eaef; border-radius: 10px; padding: 10px; }
    .grid2 td.gap { width: 2%; }
    .grid2 td.blank { width: 49%; }
    .badge { font-family: 'DejaVu Sans'; font-weight: bold; color: #fff; font-size: 11px; padding: 1px 5px; border-radius: 4px; }
    .b-pass { background: #35a44d; } .b-fail { background: #e02424; } .b-na { background: #f5a623; }
    .item-title { font-family: {!! $fBody !!}; font-weight: bold; font-size: 12px; color: #1c2431; }
    .item-note { margin-top: 6px; line-height: 1.05; font-family: {!! $fSemi !!}; font-size: 11px; color: #3b4453; }
    .area-ico { width: 32px; height: 32px; background: #e8f7f1; border: 1px solid #cdeee1; border-radius: 9px; text-align: center; }
    .area-ico img { width: 20px; height: 20px; margin-top: 5px; }
    .area-name { font-family: {!! $fHead !!}; font-weight: bold; font-size: 13px; color: #1c2431; padding-{{ $start }}: 10px; vertical-align: middle; }
    .area-note { color: #475467; line-height: 1.05; font-size: 11px; margin-top: 8px; }

    /* gallery */
    .gal { border-collapse: separate; border-spacing: 6px 6px; width: 100%; }
    .gal td { width: 33.33%; text-align: center; }
    .gal img { width: 100%; height: 120px; border-radius: 10px; border: 1px solid #e7eaef; }
    .gal .cap { font-size: 10px; color: #8b93a1; margin-top: 4px; }
    .doc-btn { background: #0b8a68; border-radius: 8px; padding: 8px 14px; color: #fff; font-family: {!! $fBody !!}; font-weight: bold; font-size: 12px; }

    /* EV / technical tables */
    .dt { border: 1px solid #e7eaef; border-radius: 10px; background: #fff; }
    .dt td, .dt th { padding: 8px 10px; border-bottom: 1px solid #e7eaef; font-size: 11px; }
    .dt th { background: #f3f5f8; font-weight: bold; text-align: center; }
    .dt .lbl { background: #f7f9fb; font-family: {!! $fSemi !!}; }
    .dt .c { text-align: center; }

    .sign td { width: 50%; padding: 6px 10px; }
    .sign .slot { border-bottom: 2px dashed #cfd4dc; height: 34px; margin-bottom: 6px; }
    .sign .sl { color: #8b93a1; font-family: {!! $fSemi !!}; font-size: 10.5px; }
    .terms p { margin: 0; font-size: 11px; line-height: 1.05; color: #3b4453; }
    .dot { display: inline-block; width: 10px; height: 10px; border-radius: 5px; border: 1px solid #8a94a3; }
    .avoid { page-break-inside: avoid; }
</style>
</head>
<body>
<div class="page-bg"></div>

{{-- Running header: drawn on every page, hidden under the cover on page 1. --}}
<div class="run-head">
    <table><tr>
        <td style="vertical-align:middle;"><img src="{{ $logo }}" alt="Auto Assure"></td>
        <td style="vertical-align:middle;text-align:{{ $end }};"><span class="doc-tag">{{ $docTag }}</span></td>
    </tr></table>
</div>

<div class="run-foot">
    <table><tr>
        <td style="text-align:left;">Auto Assure – Technical Inspection Services<br>Shop 4, Zone 91, Street 7009, Ezdan Oasis, Al Wukhair, State of Qatar</td>
        <td style="text-align:right;">Website: www.auto-assure.com<br>Email: info@auto-assure.com</td>
    </tr></table>
</div>

{{-- ============================== COVER ============================== --}}
<div class="cover{{ $usesCalculated ? '' : ' cover--short' }}">
    <div class="rc-head">
        <table><tr>
            <td style="vertical-align:middle;width:40%;"><img src="{{ $logo }}" alt="Auto Assure"></td>
            <td style="vertical-align:middle;">
                <h1>@if ($reportKind !== '')<span class="g">{{ $reportKind }}</span> @endif{{ $L('Inspection Report') }}</h1>
            </td>
        </tr></table>
    </div>

    <div class="rc-hero">
        <table @unless ($usesCalculated) style="table-layout:fixed;" @endunless><tr>
            @if ($usesCalculated)
                {{-- Gauge (Calculated Overall Verdict) + vehicle image --}}
                <td style="width:42%;vertical-align:middle;padding-{{ $end }}:20px;">
                    <img src="{{ $gauge }}" alt="" style="width:100%;">
                    <div class="gauge-score">{{ $fmtRating($scoreF) }}<span> / 100</span></div>
                    <div class="gauge-pill"><span style="color:{{ $condColor }};border-color:{{ $condColor }};">{{ $L($condition) }}</span></div>
                    <div class="gauge-legend">
                        <span><i style="background:#e0483d"></i>{{ $L('Poor') }}</span>
                        <span><i style="background:#efb008"></i>{{ $L('Fair') }}</span>
                        <span><i style="background:#f2903f"></i>{{ $L('Good') }}</span>
                        <span><i style="background:#5ab84d"></i>{{ $L('Very Good') }}</span>
                        <span><i style="background:#2fa84f"></i>{{ $L('Excellent') }}</span>
                    </div>
                </td>
                <td style="vertical-align:middle;">
                    @if ($coverImg)
                        <div class="car"><img class="photo" src="{{ $coverImg }}" alt=""><span class="inspected"><span style="font-family:'DejaVu Sans';">&#10003;</span> INSPECTED</span></div>
                    @endif
                </td>
            @else
                {{-- No gauge: Vehicle Summary on the left, vehicle image on the right --}}
                {{-- 50 / 50, as on the screen report; fixed so the card's content
                     can't claim the photo's half. --}}
                <td style="width:50%;vertical-align:top;padding-{{ $end }}:20px;">
                    @include('inspections._report_pdf_summary')
                </td>
                @if ($coverImg)
                    {{-- The photo is the cell's background, sized to cover: the cell is
                         as tall as the Vehicle Summary card beside it, so the image fills
                         that height and is cropped to fit — the screen report's
                         object-fit: cover, which an <img> cannot do in dompdf. --}}
                    <td style="width:50%;vertical-align:top;border-radius:14px;background:#0c2136 url('{{ $coverImg }}') no-repeat center center;background-size:cover;">
                        <div style="padding:12px;"><div style="float:{{ $end }};background:#2fa84f;color:#fff;font-family:{!! $fSemi !!};font-size:10px;letter-spacing:.4px;padding:4px 12px;border-radius:12px;"><span style="font-family:'DejaVu Sans';">&#10003;</span> INSPECTED</div></div>
                    </td>
                @endif
            @endif
        </tr></table>
    </div>

    @if ($usesCalculated)
        <div style="padding:0 34px 16px;">@include('inspections._report_pdf_summary')</div>

        <div style="padding:0 34px 16px;">
            <table class="rc-verdict" style="border-collapse:separate;border-spacing:0;">
                <tr>
                    <td style="width:50%;border-radius:12px 0 0 12px;border-{{ $end }}:1px solid #eef0f4;">
                        <div class="lbl">{{ $L('Overall Rating') }}</div>
                        <div style="margin-top:6px;">
                            @if ($overallRatingVal > 0)
                                {!! $stars($overallRatingVal, '#dfe3ea', 20) !!} <span class="num">{{ number_format($overallRatingVal, 1) }}/5</span>
                            @else
                                <span class="num">{{ $overallCond ?? $L($condition) }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="width:50%;border-radius:0 12px 12px 0;">
                        <div class="lbl">{{ $L('Recommendation') }}</div>
                        <div class="rec">{{ $recommend }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    @if ($commentOnCover)
        <div style="padding:0 34px 16px;">
            <div class="rc-card">
                <div class="rc-card__head">{{ $L('Inspector Comment') }}</div>
                <div class="rc-comment__body">{!! nl2br(e($comment)) !!}</div>
            </div>
        </div>
    @endif
</div>

<div class="content">

{{-- Long Inspector Comment: too big for the cover, so it leads page 2. --}}
@if ($comment !== null && ! $commentOnCover)
    <div class="sec-bar">{{ $L('Inspector Comment') }}</div>
    <div class="card rc-comment__body" style="padding:14px 18px;">{!! nl2br(e($comment)) !!}</div>
@endif

{{-- ============================== INSPECTION SUMMARY ============================== --}}
@if (! empty($areaNotes))
    <div class="sec-bar">{{ $L('Inspection Summary') }}</div>
    <table class="grid2">
        @foreach (array_chunk($areaNotes, 2) as $pair)
            <tr class="avoid">
                @foreach ($pair as $an)
                    <td class="cell">
                        <table><tr>
                            <td style="width:34px;"><div class="area-ico"><img src="{{ $an['icon'] }}" alt=""></div></td>
                            <td class="area-name">{{ $an['name'] }}</td>
                        </tr></table>
                        <div class="area-note">{!! nl2br(e($an['note'])) !!}</div>
                    </td>
                    @if (! $loop->last)<td class="gap"></td>@endif
                @endforeach
                @if (count($pair) === 1)<td class="gap"></td><td class="blank"></td>@endif
            </tr>
        @endforeach
    </table>
@endif

{{-- ============================== DIAGNOSTIC MEDIA ============================== --}}
@if ($wantsDiagnostic && (! empty($diagnosticPhotos) || $diagnosticDocs->isNotEmpty()))
    <div class="sec-bar">{{ $L('Diagnostic Media') }}</div>
    @if (! empty($diagnosticPhotos))
        <div class="card">
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
        </div>
    @endif
    @if ($diagnosticDocs->isNotEmpty())
        <div class="card">
            {{-- Fixed layout: a long name can't widen its column. --}}
            <table class="gal" style="table-layout:fixed;">
                @foreach ($diagnosticDocs->chunk(3) as $three)
                    <tr class="avoid">
                        @foreach ($three as $doc)
                            @php $docName = $doc->label ?: ($doc->original_name ?: 'Document'); @endphp
                            {{-- File name only; the button links to the document. --}}
                            <td style="width:33.33%;text-align:{{ $start }};">
                                @if ($doc->url)
                                    <a href="{{ $doc->url }}" style="text-decoration:none;"><div class="doc-btn">{{ Str::limit($docName, 28) }}</div></a>
                                @else
                                    <div class="doc-btn">{{ Str::limit($docName, 28) }}</div>
                                @endif
                            </td>
                        @endforeach
                        @for ($i = $three->count(); $i < 3; $i++)<td style="width:33.33%;"></td>@endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
@endif

{{-- ============================== PHOTOS ============================== --}}
@if (! empty($photos))
    <div class="sec-bar">{{ $L($basicReport ? 'Vehicle Photos' : 'General Photos') }}</div>
    <div class="card">
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
    </div>
@endif

{{-- Quick / Fleet: Damage Points follow the photos directly, so the short
     report doesn't leave a gap before them at the end. --}}
@if ($quickOrFleet && ! empty($damageDiagrams))
    @include('inspections._report_pdf_damage', ['splitDamage' => true])
@endif

{{-- ============================== EV & TECHNICAL (full report only) ============================== --}}
@unless ($basicReport)
    @if ($hasEv)
        <div class="sec-bar">EV &amp; PHEV</div>
        @php $evBanner = $banner('EV and PHEV') ?: $banner('EV & PHEV Details'); @endphp
        @if ($evBanner)<img class="sec-banner" src="{{ $evBanner }}" alt="">@endif
        <table class="dt" style="border-collapse:separate;border-spacing:0;">
            <tr><td class="lbl">Type Approval Certificate</td><td class="c">N/A</td><td class="lbl">Footprint (M2)</td><td class="c">{{ $ev('Footprint (M2)') }}</td></tr>
            <tr><td class="lbl">Full Battery Charge Time</td><td class="c">{{ $ev('Full Battery Charge Time (Min or H)') }}</td><td class="lbl">Battery Capacity (KW/h)</td><td class="c">{{ $ev('Battery Capacity (KW/h)') }}</td></tr>
            <tr><td class="lbl">Battery Type</td><td class="c">{{ $ev('Battery Type') }}</td><td class="lbl">Electric Consumption</td><td class="c">{{ $ev('Electric Consumption (KWh/100KM)') }}</td></tr>
            <tr><td class="lbl" style="border-bottom:none;">Battery Voltage (V)</td><td class="c" style="border-bottom:none;">{{ $ev('Battery Voltage (V)') }}</td><td class="lbl" style="border-bottom:none;">Equivalent fuel economy</td><td class="c" style="border-bottom:none;">{{ $ev('Equivalent fuel economy (KM/L)') }}</td></tr>
        </table>
    @endif

    @if ($hasTech)
        <div class="sec-bar">Technical Inspection Measurements</div>
        @php $techBanner = $banner('Technical Inspection Measurements') ?: $banner('Technical & Emissions Tests'); @endphp
        @if ($techBanner)<img class="sec-banner" src="{{ $techBanner }}" alt="">@endif
        <table class="dt" style="border-collapse:separate;border-spacing:0;">
            <tr><th>Inspection Item</th><th>Criteria Limit</th><th>Measurement</th><th>Result</th></tr>
            @foreach ([
                ['Main Brake (Static Device)', 'Brake efficiency ≥ 45%', 'Main Brake (Static Device) — Automated brake efficiency check'],
                ['Gaseous Pollutants (CO)', '(CO) ≤ 3.5%', 'Pollution - Gasoline Engines (CO)'],
                ['Gaseous Pollutants (HC)', '(HC) ≤ 1200 ppm', 'Pollution - Gasoline Engines (HC)'],
                ['Smoke Density (Diesel)', 'Reading ≤ 40%', 'Smoke Density - Diesel Engines'],
                ['Glass Transparency', 'Transparency ≥ 70%', 'Glass Transparency'],
                ['Noise Emissions', 'Per clause 1.19', 'Noise Emissions'],
            ] as $tr)
                <tr><td class="lbl">{{ $tr[0] }}</td><td class="c">{{ $tr[1] }}</td><td class="c">{{ $reading($tr[2]) }}</td><td class="c">{!! $badge($techState($tr[2])) !!}</td></tr>
            @endforeach
        </table>
    @endif
@endunless

{{-- ============================== CHECKLIST ============================== --}}
{{-- One row of two item cards. --}}
@php
    $itemRow = function ($pair) use ($answers, $badge, $pick) {
        $html = '<tr class="avoid">';
        foreach ($pair->values() as $i => $step) {
            $d = $answers->get($step->id);
            $note = optional($d)->descriptive_answer ?: optional($d)->remedial_suggestion;
            if ($i > 0) $html .= '<td class="gap"></td>';
            $html .= '<td class="cell">'.$badge(Inspection::choiceState($d)).'&nbsp;&nbsp;<span class="item-title">'.e($pick($step->question, $step->question_ar)).'</span>'
                .($note ? '<div class="item-note">'.e($note).'</div>' : '').'</td>';
        }
        if ($pair->count() === 1) $html .= '<td class="gap"></td><td class="blank"></td>';
        return $html.'</tr>';
    };
    $lastGroup = null; $shownGroupBanners = [];
@endphp
@foreach ($checklist as $sec)
    @php
        $rows = $sec['steps']->chunk(2)->values();
        $newGroup = $sec['groupKey'] && $sec['groupKey'] !== $lastGroup;
        $gBanner = null;
        if ($newGroup) {
            $lastGroup = $sec['groupKey'];
            $gBanner = in_array($sec['groupKey'], $shownGroupBanners, true) ? null : $banner($sec['groupKey']);
            if ($gBanner) $shownGroupBanners[] = $sec['groupKey'];
        }
    @endphp
    {{-- dompdf ignores "keep with next", so the group heading, the section bar
         and the first row of items are one unbreakable block: a heading is never
         left alone at the bottom of a page. --}}
    <div class="avoid">
        @if ($newGroup)
            <div class="make-h">{{ $sec['group'] }}<div class="u"></div></div>
            @if ($gBanner)<img class="sec-banner" src="{{ $gBanner }}" alt="">@endif
        @endif
        <div class="sec-bar">
            {{ $sec['name'] }}
            @if ($sec['rating'] > 0)<span class="rate">{!! $stars($sec['rating'], '#4a6278', 13) !!} {{ $fmtRating($sec['rating']) }}/5</span>@endif
        </div>
        @if ($sec['banner'])<img class="sec-banner" src="{{ $sec['banner'] }}" alt="">@endif
        <table class="grid2">{!! $itemRow($rows[0]) !!}</table>
    </div>
    @if ($rows->count() > 1)
        <table class="grid2">
            @foreach ($rows->slice(1) as $pair){!! $itemRow($pair) !!}@endforeach
        </table>
    @endif
@endforeach

{{-- ============================== DAMAGE DIAGRAMS (both on one page) ============================== --}}
{{-- Quick / Fleet print these straight after the photos instead. --}}
@if (! $quickOrFleet && ! empty($damageDiagrams))
    @include('inspections._report_pdf_damage')
@endif

{{-- ============================== SIGNATURES ============================== --}}
{{-- Signatures and Terms & Conditions are one unbreakable block, so they always
     share a page. --}}
<div class="avoid">
    {{-- On every report, basic (Quick / Fleet) included. --}}
    <div>
        <div class="sec-bar">{{ $L('Signatures') }}</div>
        <div class="card">
            <table class="sign"><tr>
                <td>
                    <div style="font-family:{!! $fBody !!};font-weight:bold;font-size:12px;margin-bottom:6px;">{{ $val(optional($inspection->technician)->name) }}</div>
                    <div class="slot"></div><div class="sl">Inspector — Sign &amp; Date</div>
                </td>
                <td>
                    <div style="font-size:12px;margin-bottom:6px;">&nbsp;</div>
                    <div class="slot"></div><div class="sl">Technical Manager — Sign &amp; Date</div>
                </td>
            </tr></table>
        </div>
    </div>

{{-- ============================== TERMS & CONDITIONS ============================== --}}
<div>
    <div class="sec-bar">{{ $L('Terms & Conditions') }}</div>
    <div class="card terms">
        @if ($isAr)
            <p>هذا التقرير يخص المركبة التي قدمها العميل وتم اختبارها/فحصها فقط. يعتبر هذا التقرير لاغياً في حالة حدوث أي كشط أو تعديل أو حذف أو إضافة. بيانات هذا التقرير سرية وخاصة ولا يحق لأي من أفراد الشركة نشرها أو الإعلان عنها إلا بموافقة مسبقة من العميل وبموجب حكم قضائي أو طلب من الجهات المختصة. يلتزم صاحب المركبة (العميل) بالحضور مرة أخرى إذا طُلب منه.</p>
        @else
            <p>This report is for the vehicle provided by the customer and tested/inspected only. This report is considered void in the event of any scraping, modification, deletion, or addition. The data in this report is confidential and private and no company personnel has the right to publish or announce it except with the prior approval of the customer or according to a court ruling or a request from the competent authorities. The vehicle owner (customer) is obligated to attend again if requested.</p>
        @endif
    </div>
</div>
</div>

</div>
</body>
</html>
