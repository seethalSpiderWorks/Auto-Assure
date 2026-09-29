{{-- Damage diagrams — included from report_pdf, which decides where it sits:
     straight after the photos on Quick / Fleet, after the checklist on every
     other template.

     $splitDamage off: the heading and all diagrams are one unbreakable block, so
     they always share a page. On (Quick / Fleet): only the heading + first diagram
     are kept together and each diagram stays whole, so the block starts right
     under the photos instead of jumping to a new page and leaving a gap. --}}
@if (empty($splitDamage))
    <div class="avoid">
        <div class="sec-bar">{{ $L($basicReport ? 'Paint Inspection Images' : 'Damage Points') }}</div>
        <div class="card">
            @foreach ($damageDiagrams as $d)
                @include('inspections._report_pdf_damage_row', ['d' => $d, 'last' => $loop->last])
            @endforeach
        </div>
    </div>
@else
    @foreach (array_values($damageDiagrams) as $d)
        @if ($loop->first)
            <div class="avoid">
                <div class="sec-bar">{{ $L($basicReport ? 'Paint Inspection Images' : 'Damage Points') }}</div>
                <div class="card">@include('inspections._report_pdf_damage_row', ['d' => $d, 'last' => true])</div>
            </div>
        @else
            <div class="card avoid">@include('inspections._report_pdf_damage_row', ['d' => $d, 'last' => true])</div>
        @endif
    @endforeach
@endif
