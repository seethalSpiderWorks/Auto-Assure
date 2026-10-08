{{-- Vehicle Summary card on the PDF cover (inspections/report_pdf). Uses the
     parent view's variables: $rowCols, $L. One table, one row per line across
     all columns, so a value that wraps in one column keeps the rows level in
     the others (the columns are still filled top to bottom).

     Three columns (full width, under the gauge): labels and short values stay on
     one line. Two columns (half width, beside the photo): fixed column widths,
     labels and values wrap as on the screen report. --}}
@php
    $colN  = count($rowCols);
    $lineN = max(array_map('count', $rowCols));
    $narrow = $colN === 2;
    // dompdf breaks lines only at spaces: give long unspaced values (a VIN) a
    // 1px break point every 11 characters so they wrap instead of overflowing.
    $wbr = '<span style="font-size:1px;"> </span>';
    $fmtVal = function ($v) use ($narrow, $wbr) {
        $v = (string) $v;
        if (! $narrow) return e($v);
        return implode(' ', array_map(
            fn ($w) => mb_strlen($w) > 16 ? implode($wbr, array_map('e', mb_str_split($w, 11))) : e($w),
            explode(' ', $v)
        ));
    };
@endphp
<div class="rc-card{{ $narrow ? ' rc-card--narrow' : '' }}">
    <div class="rc-card__head">{{ $L('Vehicle Summary') }}</div>
    <div class="rc-card__body">
        <table class="rc-kv" @if ($narrow) style="table-layout:fixed;" @endif>
            @if ($narrow)
                {{-- label 20% / value 28%, twice, with a 4% gutter --}}
                <tr>
                    <td style="width:20%;padding:0;border:none;"></td><td style="width:28%;padding:0;border:none;"></td>
                    <td style="width:4%;padding:0;border:none;"></td>
                    <td style="width:20%;padding:0;border:none;"></td><td style="width:28%;padding:0;border:none;"></td>
                </tr>
            @endif
            @for ($r = 0; $r < $lineN; $r++)
                <tr class="{{ $r === $lineN - 1 ? 'last' : '' }}">
                    @foreach ($rowCols as $ci => $col)
                        @if ($ci > 0)<td class="gap" style="{{ $narrow ? '' : 'width:18px;' }}border-bottom:none;"></td>@endif
                        @php $item = $col[$r] ?? null; @endphp
                        @if ($item)
                            <td class="k" @if ($narrow) style="white-space:normal;" @endif>{{ $L($item[0]) }}</td>
                            <td class="v" @if (! $narrow && mb_strlen((string) $item[1]) <= 12) style="white-space:nowrap;" @endif>{!! $fmtVal($item[1]) !!}</td>
                        @else
                            <td class="k" style="border-bottom:none;"></td><td class="v" style="border-bottom:none;"></td>
                        @endif
                    @endforeach
                </tr>
            @endfor
        </table>
    </div>
</div>
