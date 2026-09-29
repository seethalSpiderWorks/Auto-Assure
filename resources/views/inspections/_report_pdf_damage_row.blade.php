{{-- One damage diagram: label, colour key and the marked-up image. --}}
<div class="avoid">
    <div style="font-family:{!! $fBody !!};font-weight:bold;font-size:11px;letter-spacing:.5px;text-transform:uppercase;color:#3b4655;margin-bottom:5px;">
        {{ $d['label'] }}@if($d['section']) — {{ $d['section'] }}@endif
    </div>
    @if ($d['key']->isNotEmpty())
        <div style="margin-bottom:6px;font-size:10px;color:#3b4655;">
            @foreach ($d['key'] as $kc)
                <span style="margin-{{ $end }}:12px;"><span class="dot" style="background:{{ $kc->colour }};"></span> <strong>{{ $kc->label }}</strong>@if($kc->description)<span style="color:#6b7280;"> — {{ $kc->description }}</span>@endif</span>
            @endforeach
        </div>
    @endif
    <div style="text-align:center;"><img src="{{ $d['file'] }}" alt="" style="max-width:80%;max-height:270px;"></div>
</div>
