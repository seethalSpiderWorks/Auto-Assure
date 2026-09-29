{{-- Damage diagrams — included from report_pdf, which decides where it sits:
     straight after the photos on Quick / Fleet, after the checklist on every
     other template.

     Only the heading + first diagram are kept together, and each diagram stays
     whole, so the block starts in whatever room is left on the current page
     instead of jumping to a new page and leaving a gap. --}}
@foreach (array_values($damageDiagrams) as $d)
    @if ($loop->first)
        <div class="avoid">
            <div class="sec-bar">{{ $L($basicReport ? 'Paint Inspection Images' : 'Damage Points') }}</div>
            <div class="card">@include('inspections._report_pdf_damage_row', ['d' => $d])</div>
        </div>
    @else
        <div class="card avoid">@include('inspections._report_pdf_damage_row', ['d' => $d])</div>
    @endif
@endforeach
