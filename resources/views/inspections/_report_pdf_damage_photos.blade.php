{{-- Additional Damage Photos — included from report_pdf straight after the
     damage diagrams, wherever those sit. Prints nothing when none were taken. --}}
@if (! empty($damagePhotos))
    <div class="sec-bar">{{ $L('Damage Photos') }}</div>
    <div class="card">
        <table class="gal gal-4">
            @foreach (array_chunk($damagePhotos, 4) as $four)
                <tr class="avoid">
                    @foreach ($four as $p)
                        <td><img src="{{ $p['file'] }}" alt="">@if($p['caption'] !== '')<div class="cap">{{ $p['caption'] }}</div>@endif</td>
                    @endforeach
                    @for ($i = count($four); $i < 4; $i++)<td></td>@endfor
                </tr>
            @endforeach
        </table>
    </div>
@endif
