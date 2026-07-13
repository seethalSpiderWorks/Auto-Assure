<?php

namespace App\Http\Controllers;

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

        return back()->with('success', 'Section updated.');
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
