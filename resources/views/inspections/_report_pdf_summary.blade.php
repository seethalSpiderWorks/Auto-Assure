{{-- Vehicle Summary card on the PDF cover (inspections/report_pdf). Uses the
     parent view's variables: $rowCols, $L, $end. --}}
<div class="rc-card">
    <div class="rc-card__head">{{ $L('Vehicle Summary') }}</div>
    <div class="rc-card__body">
        <table><tr>
            @foreach ($rowCols as $ci => $col)
                <td style="width:{{ round(100 / count($rowCols), 2) }}%;padding:0 {{ $ci < count($rowCols) - 1 ? '9px' : '0' }} 0 {{ $ci > 0 ? '9px' : '0' }};">
                    <table class="rc-kv">
                        @foreach ($col as $r)
                            <tr class="{{ $loop->last ? 'last' : '' }}"><td class="k">{{ $L($r[0]) }}</td><td class="v">{{ $r[1] }}</td></tr>
                        @endforeach
                    </table>
                </td>
            @endforeach
        </tr></table>
    </div>
</div>
