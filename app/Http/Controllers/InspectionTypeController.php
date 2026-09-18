<?php

namespace App\Http\Controllers;

use App\Models\InspectionType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Support\BilingualText;
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
        $validated = $this->validateType($request);
        $summaryOptions = $this->validateSummaryOptions($request);

        $type = InspectionType::create($validated);
        $this->syncSummaryOptions($type, $summaryOptions);

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
        return view('templates.edit', ['type' => $template->load('summaryOptions')]);
    }

    public function update(Request $request, InspectionType $template): RedirectResponse
    {
        $validated = $this->validateType($request);
        $summaryOptions = $this->validateSummaryOptions($request);

        $template->update($validated);
        $this->syncSummaryOptions($template, $summaryOptions);

        return redirect()->route('templates.show', $template)->with('success', 'Inspection type updated.');
    }

    public function destroy(InspectionType $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Inspection type deleted.');
    }

    /**
     * Save the template's Summary options from the form rows: existing rows are
     * renamed, new ones added, removed ones deleted, and the row order becomes
     * the display order. Notes already written against a removed option stay in
     * inspection_summaries — they simply stop being shown.
     */
    private function syncSummaryOptions(InspectionType $type, array $rows): void
    {
        $existing = $type->summaryOptions()->get()->keyBy('id');
        $kept = [];

        foreach (array_values($rows) as $i => $row) {
            $row = BilingualText::applyAll($row, ['name']);
            $values = [
                'name' => trim($row['name']),
                'name_ar' => filled($row['name_ar'] ?? null) ? trim($row['name_ar']) : null,
                'sequence' => $i + 1,
            ];

            $option = $existing->get((int) ($row['id'] ?? 0));
            if ($option) {
                $option->update($values);
            } else {
                $option = $type->summaryOptions()->create($values);
            }
            $kept[] = $option->id;
        }

        $type->summaryOptions()->whereNotIn('id', $kept)->delete();
    }

    /**
     * @return array<int, array{id?: int|null, name: string, name_ar?: string|null}>
     */
    private function validateSummaryOptions(Request $request): array
    {
        return $request->validate([
            'summary_options' => ['nullable', 'array'],
            'summary_options.*.id' => ['nullable', 'integer'],
            'summary_options.*.name' => ['required', 'string', 'max:255'],
            'summary_options.*.name_ar' => ['nullable', 'string', 'max:255'],
        ], [
            'summary_options.*.name.required' => 'Every summary option needs a title.',
        ])['summary_options'] ?? [];
    }

    private function validateType(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'sequence' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'has_diagnostic_media' => ['nullable', 'boolean'],
            'has_calculated_verdict' => ['nullable', 'boolean'],
        ]) + [
            // Unchecked switches are absent from the POST body, so read them
            // off the request rather than trusting the validated array.
            'is_active' => $request->boolean('is_active'),
            'has_diagnostic_media' => $request->boolean('has_diagnostic_media'),
            'has_calculated_verdict' => $request->boolean('has_calculated_verdict'),
        ];

        // "Name[ar]الاسم" typed into the English box fills the Arabic one, the
        // same shorthand the legacy report form accepts for its comments.
        return BilingualText::applyAll($validated, ['name', 'description']);
    }
}
