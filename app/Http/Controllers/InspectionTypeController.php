<?php

namespace App\Http\Controllers;

use App\Models\InspectionType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InspectionTypeController extends Controller
{
    public function index(): View
    {
        $types = InspectionType::withCount('sections')->orderBy('sequence')->get();

        return view('templates.index', compact('types'));
    }

    public function create(): View
    {
        return view('templates.create', ['type' => new InspectionType(['is_active' => true, 'has_diagnostic_media' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $type = InspectionType::create($this->validateType($request));

        return redirect()->route('templates.show', $type)->with('success', 'Inspection type created.');
    }

    public function show(InspectionType $template): View
    {
        $template->load(['sections.steps', 'sections.damageDiagrams']);

        return view('templates.show', [
            'type' => $template,
            // Every diagram, so a section's editor can offer the unassigned ones too.
            'damageDiagrams' => \App\Models\DamageDiagram::ordered()->get(),
        ]);
    }

    public function edit(InspectionType $template): View
    {
        return view('templates.edit', ['type' => $template]);
    }

    public function update(Request $request, InspectionType $template): RedirectResponse
    {
        $template->update($this->validateType($request));

        return redirect()->route('templates.show', $template)->with('success', 'Inspection type updated.');
    }

    public function destroy(InspectionType $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Inspection type deleted.');
    }

    private function validateType(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sequence' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'has_diagnostic_media' => ['nullable', 'boolean'],
        ]) + [
            // Unchecked switches are absent from the POST body, so read them
            // off the request rather than trusting the validated array.
            'is_active' => $request->boolean('is_active'),
            'has_diagnostic_media' => $request->boolean('has_diagnostic_media'),
        ];
    }
}
