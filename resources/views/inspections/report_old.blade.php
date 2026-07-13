@php
    use App\Models\Inspection;
    $lead   = $inspection->lead;
    $reportNo  = optional($lead)->reference ?: ('AAQ-' . str_pad($inspection->id, 3, '0', STR_PAD_LEFT));
    $reportDt  = optional($inspection->completed_at ?: $inspection->updated_at)->format('d-m-Y');
    $inspDt    = optional($inspection->scheduled_at ?: $inspection->started_at ?: $inspection->created_at)->format('d-m-Y');
    $condition = Inspection::CONDITIONS[$inspection->overall_condition] ?? '—';
    $recommend = Inspection::RECOMMENDATIONS[$inspection->recommendation] ?? '—';
    $compliant = $inspection->recommendation !== 'avoid';
    $allMedia  = $inspection->details->flatMap(fn ($d) => $d->media)->values();
    $val = fn ($v) => ($v === null || $v === '') ? 'N/A' : e($v);

    // question -> answer (detail) lookup, for the EV & Technical special blocks
    $qa = [];
    foreach ($inspection->type->sections as $s) {
        foreach ($s->steps as $st) { $qa[$st->question] = $answers->get($st->id); }
    }
    $ev = fn ($q) => optional($qa[$q] ?? null)->descriptive_answer ?: 'N/A';
    // technical result state: pass | fail | na
    $st = fn ($q) => (optional($qa[$q] ?? null)->choice === 'Pass') ? 'pass'
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
    $skip = ['EV and PHEV', 'Technical Inspection Measurements', 'EV & PHEV Details', 'Technical & Emissions Tests'];

    // Mid-level groups (between main heading and section), derived from the section number prefix.
    // checkbox helper
    $ck = fn ($on) => $on ? '<span class="on">&#9746;</span>' : '<span class="off">&#9744;</span>';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inspection Report — {{ $reportNo }}</title>
    <style>
        * { box-sizing:border-box; }
        body { font-family:"Times New Roman", Tahoma, serif; color:#111827; margin:0; padding:18px; font-size:11px; background:#eef0f2; }
        .sheet { max-width:920px; margin:0 auto; background:#fff; padding:24px 28px; box-shadow:0 1px 4px rgba(0,0,0,.12); }
        .ar { font-family:'Tahoma','Geeza Pro','Arial',sans-serif; direction:rtl; unicode-bidi:embed; }
        .ar-blk { display:block; }
        table { border-collapse:collapse; width:100%; }
        td, th { border:1px solid #9aa3af; padding:4px 6px; }
        th { background:#e3e3e3; }
        .nb td, .nb th { border:none; }
        .c { text-align:center; } .b { font-weight:bold; }
        .lbl { background:#ececec; text-align:center; font-weight:bold; font-size:10.5px; line-height:1.35; }
        .head { border-bottom:3px solid #0b8457; padding-bottom:8px; margin-bottom:10px; }
        .head td { border:none; vertical-align:middle; }
        .logo { font-weight:800; color:#0b8457; font-size:24px; letter-spacing:.5px; }
        .title { text-align:center; font-weight:bold; font-size:14px; line-height:1.5; }
        .mainhead { background:#cfcfcf; color:#111; font-weight:bold; font-size:13px; padding:6px 10px; border:1px solid #9aa3af; }
        .mainhead .ar { float:right; }
        .midhead { background:#cfcfcf; color:#111; font-weight:bold; font-size:12px; padding:4px 10px; border:1px solid #9aa3af; }
        .midhead .ar { float:right; }
        .qcell { background:#eef3f6; font-weight:normal; }
        .subgroup { background:#dcdcdc; font-weight:bold; text-align:center; }
        .secbar { background:#cfcfcf; font-weight:bold; padding:5px 8px; border:1px solid #9aa3af; }
        .secbar .en { } .secbar .ar { float:right; }
        .on { color:#0b8457; font-weight:bold; } .off { color:#9aa3af; }
        .green { color:#0b8457; } .red { color:#b42318; }
        .vinbox td { text-align:center; font-weight:bold; width:24px; font-size:13px; }
        .muted { color:#9ca3af; }
        .badge { display:inline-block; font-weight:bold; }
        .stars { color:#f1b44c; }
        .photos img { width:60px; height:60px; object-fit:cover; border:1px solid #e5e7eb; margin:1px; }
        .gallery { display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; }
        .gallery img { width:150px; height:110px; object-fit:cover; border:1px solid #e5e7eb; }
        .foot { border-top:3px solid #0b8457; margin-top:14px; padding-top:4px; text-align:right; color:#6b7280; font-size:10px; }
        .notes td { font-size:9.5px; padding:3px 4px; }
        .photogrid { width:100%; border-collapse:collapse; }
        .photogrid td { border:1px solid #9aa3af; width:50%; padding:6px; vertical-align:top; }
        .pcap { text-align:center; font-weight:bold; text-decoration:underline; margin-bottom:4px; }
        .pbox { height:200px; display:flex; align-items:center; justify-content:center; background:#fafafa; }
        .pbox img { max-width:100%; max-height:200px; object-fit:contain; }
        /* Center every image in the report */
        .sheet img { display:block; margin-left:auto; margin-right:auto; }
        .photos, .gallery, .photogrid td { text-align:center; }
        .pempty { color:#bbb; }
        .signtbl { width:100%; border-collapse:collapse; }
        .signtbl td { border:1px solid #9aa3af; padding:5px 8px; height:26px; }
        .signtbl .siglbl { background:#ececec; font-weight:bold; white-space:nowrap; }
        .terms table td { border:1px solid #9aa3af; padding:8px; font-size:9.5px; vertical-align:top; }
        .terms b { display:block; margin-bottom:4px; }
        .terms ul { margin:0; padding-left:16px; }
        .terms .ar ul { padding-left:0; padding-right:16px; }
        .terms li { margin-bottom:3px; }
        .endrep { text-align:center; color:#c0392b; font-weight:bold; font-size:14px; text-decoration:underline; margin:14px 0 4px; }
        .toolbar { max-width:920px; margin:0 auto 12px; text-align:right; }
        .btn { background:#0b8457; color:#fff; border:none; padding:8px 16px; border-radius:5px; cursor:pointer; font-size:13px; text-decoration:none; }
        @media print { body{background:#fff;padding:0;} .toolbar{display:none;} .sheet{box-shadow:none;max-width:none;}
            .secbar, .lbl, th { -webkit-print-color-adjust:exact; print-color-adjust:exact; } .pb{page-break-before:always;} }
    </style>
</head>
<body>
    <div class="toolbar"><button class="btn" onclick="window.print()">🖨 Print / Save PDF</button></div>
    <div class="sheet">

        {{-- ===== Header ===== --}}
        <table class="head nb"><tr>
            <td style="width:200px"><span class="logo">Auto&nbsp;assure</span></td>
            <td class="title">Inspection Checklist for Used Imported Vehicle
                <span class="ar ar-blk" style="font-size:13px">قائمة فحص المركبات المستوردة المستعملة</span></td>
            <td style="width:120px"></td>
        </tr></table>

        {{-- ===== Report no / date ===== --}}
        <table style="margin-bottom:12px"><tr>
            <td class="c b" style="width:25%">{{ $reportDt }}</td>
            <td class="lbl" style="width:20%"><span class="ar ar-blk">تاريخ التقرير</span>Report Date</td>
            <td class="c b" style="width:30%">{{ $reportNo }}</td>
            <td class="lbl" style="width:25%"><span class="ar ar-blk">رقم التقرير</span>Report No.</td>
        </tr></table>

        {{-- ===== Vehicle Information ===== --}}
        <div class="secbar"><span class="en">Vehicle Information</span><span class="ar">بيانات المركبة</span></div>
        <table style="margin-bottom:12px">
            @php
                $info = [
                    [$inspDt,'Inspection Date','تاريخ الفحص', $val($inspection->vehicle_type),'Vehicle Type','نوع المركبة'],
                    [$val($inspection->country_of_origin),'Country of Origin','بلد المنشأ', $val($inspection->manufacturer_name),'Manufacturer Name','اسم الصانع'],
                    [$val($inspection->country_of_export),'Country of Export','البلد المستورد منه', $val($inspection->car_year),'Model year','سنة الطراز'],
                    [$val($inspection->motor_power_kw ? $inspection->motor_power_kw.' KW' : null),'Elec. Motor power','قدرة المحرك الكهربائي', $val(trim($inspection->car_make.' '.$inspection->car_model)),'Model','الطراز'],
                    [$val($inspection->cylinders_cc),'No. of cylinder and CC','عدد السلندرات والقدرة', $val($inspection->color),'Color','اللون'],
                    [$val($inspection->fuel_type),'Fuel Type','نوع الوقود', $val($inspection->odometer),'Odometer Reading','قراءة العداد'],
                    [$val($inspection->fuel_economy),'Fuel Economy','اقتصاد الوقود', $val($inspection->passengers),'No. of Passenger','عدد الركاب'],
                ];
            @endphp
            @foreach ($info as $r)
                <tr>
                    <td class="c" style="width:25%">{{ $r[0] }}</td>
                    <td class="lbl" style="width:20%"><span class="ar ar-blk">{{ $r[2] }}</span>{{ $r[1] }}</td>
                    <td class="c" style="width:30%">{{ $r[3] }}</td>
                    <td class="lbl" style="width:25%"><span class="ar ar-blk">{{ $r[5] }}</span>{{ $r[4] }}</td>
                </tr>
            @endforeach
            {{-- VIN row --}}
            <tr>
                <td colspan="3" style="padding:0">
                    <table class="vinbox" style="border:none"><tr>
                        @foreach (str_split(strtoupper($inspection->vin ?? '')) as $chx)
                            <td>{{ $chx }}</td>
                        @endforeach
                    </tr></table>
                </td>
                <td class="lbl"><span class="ar ar-blk">الرقم المميز للمركبة</span>(VIN)</td>
            </tr>
        </table>

        {{-- ===== Inspection Result ===== --}}
        <table style="margin-bottom:12px">
            <tr>
                <td style="width:45%" class="c">
                    <span class="ar b red">غير مطابق للمتطلبات</span><br><span class="b red">Non-Compliance to Requirements</span>
                    &nbsp;&nbsp;{!! $ck(!$compliant) !!}
                </td>
                <td style="width:35%" class="c">
                    <span class="ar b green">مطابق للمتطلبات</span><br><span class="b green">Compliance to Requirements</span>
                    &nbsp;&nbsp;{!! $ck($compliant) !!}
                </td>
                <td class="lbl" style="width:20%"><span class="ar ar-blk">نتيجة الفحص</span>Inspection Result</td>
            </tr>
            <tr>
                <td colspan="2" class="c" style="font-size:10px;line-height:1.4">
                    SASO Technical Regulation for Used Imported Vehicles MA-179-21-09-02 &amp; SASO GSO 42,
                    SASO GSO 1680, GSO 1040,<br>Administrative and technical instructions regarding periodic technical inspection procedures (VSC-VS-P-6.0)
                </td>
                <td class="lbl"><span class="ar ar-blk">اللائحة الفنية والمواصفات</span>Technical Regulation and Standards</td>
            </tr>
            <tr>
                <td colspan="2" style="height:34px">{{ $val($inspection->summary) === 'N/A' ? '' : $inspection->summary }}</td>
                <td class="lbl"><span class="ar ar-blk">ملاحظات</span>Notes</td>
            </tr>
        </table>

        {{-- ===== EV and PHEV ===== --}}
        <div class="secbar c"><span class="en">EV and PHEV</span> / <span class="ar" style="float:none">للسيارات الكهربائية والهجينة بقابس</span></div>
        <table style="margin-bottom:12px">
            <tr>
                <td class="c" style="width:8%">N/A<span class="ar ar-blk b" style="font-size:9px">لا ينطبق</span>{!! $ck(true) !!}</td>
                <td class="c" style="width:12%">Issued By<span class="ar ar-blk b" style="font-size:9px">جهة الإصدار</span></td>
                <td class="lbl" style="width:22%">Type Approval Certificate<span class="ar ar-blk">شهادة مطابقة الطراز</span></td>
                <td class="c" style="width:13%">{{ $ev('Footprint (M2)') }}</td>
                <td class="lbl">Footprint (M2)<span class="ar ar-blk">البصمة (مساحة تلامس المركبة على الأرض)</span></td>
            </tr>
            <tr>
                <td colspan="2" class="c">{{ $ev('Full Battery Charge Time (Min or H)') }}</td>
                <td class="lbl">Full Battery Charge Time (Min or H)<span class="ar ar-blk">الوقت المستغرق للشحن</span></td>
                <td class="c">{{ $ev('Battery Capacity (KW/h)') }}</td>
                <td class="lbl">Battery Capacity (KW/h)<span class="ar ar-blk">قدرة البطارية</span></td>
            </tr>
            <tr>
                <td colspan="2" class="c">{{ $ev('Battery Type') }}</td>
                <td class="lbl">Battery Type<span class="ar ar-blk">نوع البطارية</span></td>
                <td class="c">{{ $ev('Electric Consumption (KWh/100KM)') }}</td>
                <td class="lbl">Electric Consumption (KWh/100KM)<span class="ar ar-blk">الاستهلاك الكهربائي</span></td>
            </tr>
            <tr>
                <td colspan="2" class="c">{{ $ev('Battery Voltage (V)') }}</td>
                <td class="lbl">Battery Voltage (V)<span class="ar ar-blk">فولت البطارية</span></td>
                <td class="c">{{ $ev('Equivalent fuel economy (KM/L)') }}</td>
                <td class="lbl">Equivalent fuel economy (KM/L)<span class="ar ar-blk">اقتصاد الوقود المكافئ</span></td>
            </tr>
        </table>

        {{-- ===== Technical Inspection Measurements ===== --}}
        <div class="secbar"><span class="en">Technical Inspection Measurements:</span><span class="ar">قياسات الفحص الفني:</span></div>
        <table style="margin-bottom:12px">
            <thead>
                <tr class="c b">
                    <th colspan="3"><span class="ar ar-blk">النتيجة</span>Result</th>
                    <th rowspan="2"><span class="ar ar-blk">القياسات</span>Measurements</th>
                    <th rowspan="2"><span class="ar ar-blk">حد المعيار</span>Criteria Limit</th>
                    <th rowspan="2"><span class="ar ar-blk">بند الفحص</span>Inspection Item</th>
                    <th rowspan="2">No.</th>
                </tr>
                <tr class="c b">
                    <th style="width:34px"><span class="ar ar-blk" style="font-size:9px">لا ينطبق</span>N/A</th>
                    <th style="width:34px"><span class="ar ar-blk" style="font-size:9px">غير مطابق</span>Fail</th>
                    <th style="width:34px"><span class="ar ar-blk" style="font-size:9px">مطابق</span>Pass</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1. Main Brake --}}
                <tr class="c">
                    @php $s1 = $st('Main Brake (Static Device) — Automated brake efficiency check'); @endphp
                    <td>{!! $ck($s1==='na') !!}</td><td>{!! $ck($s1==='fail') !!}</td><td>{!! $ck($s1==='pass') !!}</td>
                    <td style="padding:0">
                        <table style="border:none"><tr><td style="border:none">RB</td><td style="border:none">FB</td></tr>
                        <tr><td style="border:none">{{ $reading('Main Brake (Static Device) — Automated brake efficiency check') }}</td><td style="border:none">{{ $reading('Main Brake (Static Device) — Automated brake efficiency check') }}</td></tr></table>
                    </td>
                    <td>Brake efficiency ≥ 45%</td>
                    <td><span class="ar ar-blk">الفحص الآلي لكفاءة المكابح</span>Main Brake (Static Device)<br>Automated brake efficiency check</td>
                    <td>1</td>
                </tr>
                {{-- 2. Gaseous Pollutants (CO, HC, Smoke) --}}
                @php $s2 = $st('Pollution - Gasoline Engines (CO)'); @endphp
                <tr class="c">
                    <td>{!! $ck($s2==='na') !!}</td><td>{!! $ck($s2==='fail') !!}</td><td>{!! $ck($s2==='pass') !!}</td>
                    <td>{{ $reading('Pollution - Gasoline Engines (CO)') }}</td>
                    <td>(CO) ≤ 3.5%</td>
                    <td rowspan="3"><span class="ar ar-blk">الملوثات الغازية</span>Gaseous Pollutants</td>
                    <td rowspan="3">2</td>
                </tr>
                @php $s3 = $st('Pollution - Gasoline Engines (HC)'); @endphp
                <tr class="c">
                    <td>{!! $ck($s3==='na') !!}</td><td>{!! $ck($s3==='fail') !!}</td><td>{!! $ck($s3==='pass') !!}</td>
                    <td>{{ $reading('Pollution - Gasoline Engines (HC)') }}</td>
                    <td>(HC) ≤ 1200 ppm</td>
                </tr>
                @php $s4 = $st('Smoke Density - Diesel Engines'); @endphp
                <tr class="c">
                    <td>{!! $ck($s4==='na') !!}</td><td>{!! $ck($s4==='fail') !!}</td><td>{!! $ck($s4==='pass') !!}</td>
                    <td>{{ $reading('Smoke Density - Diesel Engines') }}</td>
                    <td>Reading ≤ 40% <span class="ar ar-blk">كثافة الدخان — محركات الديزل</span></td>
                </tr>
                {{-- 3. Glass --}}
                @php $s5 = $st('Glass Transparency'); @endphp
                <tr class="c">
                    <td>{!! $ck($s5==='na') !!}</td><td>{!! $ck($s5==='fail') !!}</td><td>{!! $ck($s5==='pass') !!}</td>
                    <td>{{ $reading('Glass Transparency') }}</td>
                    <td>Transparency ≥ 70%</td>
                    <td><span class="ar ar-blk">شفافية الزجاج</span>Glass Transparency</td>
                    <td>3</td>
                </tr>
                {{-- 4. Noise --}}
                @php $s6 = $st('Noise Emissions'); @endphp
                <tr class="c">
                    <td>{!! $ck($s6==='na') !!}</td><td>{!! $ck($s6==='fail') !!}</td><td>{!! $ck($s6==='pass') !!}</td>
                    <td>{{ $reading('Noise Emissions') }}</td>
                    <td>Table on clause (1.19) of the checklist below</td>
                    <td><span class="ar ar-blk">التلوث الضوضائي</span>Noise Emissions</td>
                    <td>4</td>
                </tr>
            </tbody>
        </table>

        <div class="foot">Page 1 — Auto Assure</div>

        {{-- ===== Detailed checklist — one table, one common header ===== --}}
        <div class="pb"></div>
        <table class="checklist">
            <thead>
                <tr class="c b">
                    <th colspan="3">Acceptance &amp; Reject<span class="ar ar-blk" style="font-size:9px">القبول والرفض</span></th>
                    <th rowspan="2"><div style="display:flex;justify-content:space-between;align-items:center"><span class="en">1) Technical Requirements</span><span class="ar ar-blk" style="float:none">المتطلبات الفنية</span></div></th>
                    <th rowspan="2" style="width:32px"></th>
                </tr>
                <tr class="c b">
                    <th style="width:34px">N/A<span class="ar ar-blk" style="font-size:9px">لا ينطبق</span></th>
                    <th style="width:34px">Fail<span class="ar ar-blk" style="font-size:9px">غير مطابق</span></th>
                    <th style="width:34px">Pass<span class="ar ar-blk" style="font-size:9px">مطابق</span></th>
                </tr>
            </thead>
            <tbody>
            @php
                $lastGroup = null;
                $splitNum = function ($s) {
                    return preg_match('/^(\d+(?:\.\d+)*)[\).]?\s+(.+)$/u', (string) $s, $m) ? [$m[1], $m[2]] : ['', (string) $s];
                };
            @endphp
            @foreach ($inspection->type->sections as $section)
                @continue(in_array($section->section_name, $skip, true))
                @if ($section->group_name && $section->group_name !== $lastGroup)
                    @php [$gNum, $gTitle] = $splitNum($section->group_name); @endphp
                    <tr><td colspan="4" class="mainhead c"><span class="en">{{ $gTitle }}</span>@if($section->group_name_ar) / <span class="ar" style="float:none;display:inline">{{ $section->group_name_ar }}</span>@endif</td><td class="mainhead c">{{ $gNum }}</td></tr>
                    @php $lastGroup = $section->group_name; @endphp
                @endif
                @php [$sNum, $sTitle] = $splitNum($section->section_name); @endphp
                @php $sAr = $section->section_name_ar ?: ($secAr[$section->section_name] ?? ''); @endphp
                <tr><td colspan="4" class="secbar c"><span class="en">{{ $sTitle }}</span>@if($sAr) / <span class="ar" style="float:none;display:inline">{{ $sAr }}</span>@endif</td><td class="secbar c">{{ $sNum }}</td></tr>
                @php $lastSub = null; $letterIdx = 0; @endphp
                @foreach ($section->steps as $step)
                    @if ($step->description && $step->description !== $lastSub)
                        @php [$subNum, $subTitle] = $splitNum($step->description); [, $subTitleAr] = $splitNum($step->description_ar); @endphp
                        @if (trim($subTitle) !== trim($sTitle))
                            <tr><td colspan="4" class="subgroup c"><span class="en">{{ $subTitle }}</span>@if($subTitleAr) / <span class="ar" style="float:none;display:inline">{{ $subTitleAr }}</span>@endif</td><td class="subgroup c">{{ $subNum }}</td></tr>
                        @endif
                        @php $lastSub = $step->description; $letterIdx = 0; @endphp
                    @endif
                    @php
                        $d = $answers->get($step->id); $choice = $d->choice ?? null; $rating = $d->rating ?? null;
                        $isPass = in_array($choice,['Pass','Yes'],true) || ($rating!==null && $rating>=3);
                        $isFail = in_array($choice,['Fail','No'],true) || ($rating!==null && $rating<3);
                        $isNa = ! $isPass && ! $isFail;
                        $letterIdx++;
                        $letter = $letterIdx <= 26 ? chr(96 + $letterIdx) : $letterIdx;
                    @endphp
                    <tr class="c">
                        <td style="width:34px">{!! $ck($isNa) !!}</td><td style="width:34px">{!! $ck($isFail) !!}</td><td style="width:34px">{!! $ck($isPass) !!}</td>
                        <td class="qcell">@if($step->question_ar)<span class="ar ar-blk" style="text-align:right">{{ $step->question_ar }}</span>@endif<span style="display:block;text-align:left">{{ $step->question }}</span></td>
                        <td>({{ $letter }})</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>

        {{-- Standard footnotes (image-22) --}}
        <table class="nb notes" style="margin-top:4px">
            <tr><td style="text-align:left">* In the event that the above standard does not match, a proof of the distinguished number (VIN) from the manufacturer shall be attached.<span class="ar ar-blk" style="display:block;text-align:right">* في حالة عدم مطابقة المواصفة أعلاه، يتم إرفاق ما يثبت دلالات الرقم المميز من الشركة الصانعة.</span></td></tr>
            <tr><td style="text-align:left">** Optional requirement.<span class="ar ar-blk" style="display:block;text-align:right">** متطلب اختياري.</span></td></tr>
        </table>

        {{-- Verdict --}}
        <div class="secbar"><span class="en">Overall Verdict</span><span class="ar">النتيجة النهائية</span></div>
        <table style="margin-bottom:10px">
            <tr><th class="c" style="width:25%">Overall Condition<span class="ar ar-blk">الحالة العامة</span></th><td>{{ $condition }}</td>
                <th class="c" style="width:25%">Recommendation<span class="ar ar-blk">التوصية</span></th><td>{{ $recommend }}</td></tr>
            <tr><th class="c">Summary<span class="ar ar-blk">ملخص</span></th><td colspan="3" style="white-space:pre-line">{{ $val($inspection->summary) }}</td></tr>
        </table>

        {{-- ===== Inspection Photos — 2 per row, captioned by section (image-24 style) ===== --}}
        @php
            // step_id -> section, so each photo is captioned with its section name
            $stepSection = [];
            foreach ($inspection->type->sections as $sec) {
                foreach ($sec->steps as $st) { $stepSection[$st->id] = $sec; }
            }
            $reportPhotos = [];
            foreach ($inspection->details as $d) {
                $sec = $stepSection[$d->inspection_step_id] ?? null;
                foreach ($d->media->where('type', 'photo') as $m) {
                    // Only show the image if the file actually exists on its disk.
                    try {
                        if (! $m->path || ! \Illuminate\Support\Facades\Storage::disk($m->disk ?: 'public')->exists($m->path)) {
                            continue;
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                    // Prefer the media's own label (e.g. additional-media captions); fall back to the section name.
                    $reportPhotos[] = ['caption' => $m->label ?: ($sec?->section_name ?? 'Photo'), 'media' => $m];
                }
            }
        @endphp
        @if (! empty($reportPhotos))
            <div class="pb"></div>
            <div class="secbar c"><span class="en">Inspection Photos</span></div>
            <table class="photogrid">
                @foreach (array_chunk($reportPhotos, 2) as $row)
                    <tr>
                        @foreach ($row as $p)
                            <td>
                                <div class="pcap">{{ $p['caption'] }}</div>
                                <div class="pbox"><img src="{{ $p['media']->url }}"></div>
                            </td>
                        @endforeach
                        @if (count($row) === 1)<td></td>@endif
                    </tr>
                @endforeach
            </table>
        @endif

        {{-- ===== Signatures &amp; date (image-24) ===== --}}
        <table class="signtbl" style="margin-top:10px">
            <tr>
                <td class="c">{{ $reportDt }}</td>
                <td class="c siglbl">Date <span class="ar">التاريخ</span></td>
                <td style="width:120px"></td>
                <td class="c siglbl">Sign <span class="ar">التوقيع</span></td>
                <td class="c"><b>{{ $val(optional($inspection->technician)->name) }}</b></td>
                <td class="c siglbl">Inspector Name <span class="ar">اسم المفتش</span></td>
            </tr>
            <tr>
                <td class="c">{{ $reportDt }}</td>
                <td class="c siglbl">Date <span class="ar">التاريخ</span></td>
                <td></td>
                <td class="c siglbl">Sign <span class="ar">التوقيع</span></td>
                <td class="c"></td>
                <td class="c siglbl">Technical Manager <span class="ar">المدير الفني</span></td>
            </tr>
        </table>

        {{-- ===== Terms and conditions (image-24) ===== --}}
        <div class="terms" style="margin-top:12px">
            <table class="nb">
                <tr>
                    <td style="width:50%">
                        <b>Terms and conditions</b>
                        <ul>
                            <li>This report is for the vehicle provided by the customer and tested/inspected only.</li>
                            <li>This report is considered void in the event of any scraping, modification, deletion, or addition.</li>
                            <li>The data in this report is confidential and private and no company personnel has the right to publish or announce it except with the prior approval of the customer or according to a court ruling or a request from the competent authorities.</li>
                            <li>The vehicle owner (customer) is obligated to attend again if requested.</li>
                        </ul>
                    </td>
                    <td style="width:50%" class="ar">
                        <b>الشروط والأحكام</b>
                        <ul>
                            <li>هذا التقرير يخص المركبة التي قدمها العميل وتم اختبارها/فحصها فقط.</li>
                            <li>يعتبر هذا التقرير لاغياً في حالة حدوث أي كشط أو تعديل أو حذف أو إضافة.</li>
                            <li>بيانات هذا التقرير سرية وخاصة ولا يحق لأي من أفراد الشركة نشرها أو الإعلان عنها إلا بموافقة مسبقة من العميل وبموجب حكم قضائي أو طلب من الجهات المختصة.</li>
                            <li>يلتزم صاحب المركبة (العميل) بالحضور مرة أخرى إذا طُلب منه.</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <div class="endrep">End of Report</div>
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
