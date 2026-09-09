<?php

namespace App\Http\Controllers;

use App\Models\DamageDiagram;
use App\Models\InspectionSection;
use App\Models\InspectionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
     * Point the chosen damage diagrams at this section and release the ones that
     * were deselected. A diagram belongs to one section, so assigning it here
     * moves it off whichever section had it before.
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

        DamageDiagram::where('inspection_section_id', $section->id)
            ->whereNotIn('id', $chosen ?: [0])
            ->update(['inspection_section_id' => null]);

        if ($chosen) {
            DamageDiagram::whereIn('id', $chosen)->update(['inspection_section_id' => $section->id]);
        }
    }

    public function destroy(InspectionSection $section): RedirectResponse
    {
        $section->delete();

        return back()->with('success', 'Section deleted.');
    }

    private function validateSection(Request $request): array
    {
        return $request->validate([
            'group_name' => ['nullable', 'string', 'max:255'],
            'group_name_ar' => ['nullable', 'string', 'max:255'],
            'section_name' => ['required', 'string', 'max:255'],
            'section_name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sequence' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
