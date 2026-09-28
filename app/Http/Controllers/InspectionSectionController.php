<?php

namespace App\Http\Controllers;

use App\Models\InspectionSection;
use App\Models\InspectionType;
use Illuminate\Http\RedirectResponse;
use App\Support\BilingualText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionSectionController extends Controller
{
    public function store(Request $request, InspectionType $template): RedirectResponse
    {
        $data = $this->validateSection($request);
        $data['sequence'] = $data['sequence'] ?? (($template->sections()->max('sequence') ?? 0) + 1);

        $template->sections()->create($data);

        return back()->with('success', 'Section added.');
    }

    public function update(Request $request, InspectionSection $section): RedirectResponse
    {
        $section->update($this->validateSection($request));
        $this->syncDamageDiagrams($request, $section);

        return back()->with('success', 'Section updated.');
    }

    /**
     * Attach the chosen damage diagrams to this section and detach the ones that
     * were deselected. A diagram sits in at most one section per template, so
     * ticking it here moves it off the template's other sections — sections of
     * other templates keep theirs.
     */
    private function syncDamageDiagrams(Request $request, InspectionSection $section): void
    {
        if (! $request->has('damage_diagrams')) {
            return;
        }

        // The form always posts a trailing empty value so "none ticked" still
        // reaches the server; drop it before validating ids.
        $ids = array_values(array_filter((array) $request->input('damage_diagrams', []), fn ($v) => $v !== '' && $v !== null));

        validator(['damage_diagrams' => $ids], [
            'damage_diagrams' => ['array'],
            'damage_diagrams.*' => ['integer', 'exists:damage_diagrams,id'],
        ])->validate();

        $chosen = array_map('intval', $ids);

        if ($chosen) {
            $siblings = InspectionSection::where('inspection_type_id', $section->inspection_type_id)
                ->whereKeyNot($section->id)
                ->pluck('id');

            DB::table('damage_diagram_section')
                ->whereIn('inspection_section_id', $siblings)
                ->whereIn('damage_diagram_id', $chosen)
                ->delete();
        }

        $section->damageDiagrams()->sync($chosen);
    }

    public function destroy(InspectionSection $section): RedirectResponse
    {
        $section->delete();

        return back()->with('success', 'Section deleted.');
    }

    private function validateSection(Request $request): array
    {
        $validated = $request->validate([
            'group_name' => ['nullable', 'string', 'max:255'],
            'group_name_ar' => ['nullable', 'string', 'max:255'],
            'section_name' => ['required', 'string', 'max:255'],
            'section_name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sequence' => ['nullable', 'integer', 'min:0'],
            // Share (%) of the Overall Verdict score this section carries.
            'weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // "Engine[ar]المحرك" in the English box fills the Arabic one.
        return BilingualText::applyAll($validated, ['group_name', 'section_name']);
    }
}
