<?php

namespace App\Http\Controllers;

use App\Models\InspectionSection;
use App\Models\InspectionStep;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InspectionStepController extends Controller
{
    public function create(InspectionSection $section): View
    {
        return view('templates.steps.create', [
            'section' => $section,
            'step' => new InspectionStep(['photos' => 'not_required', 'videos' => 'not_required']),
        ]);
    }

    public function store(Request $request, InspectionSection $section): RedirectResponse
    {
        $data = $this->validateStep($request);
        $data['sequence'] = $data['sequence'] ?? (($section->steps()->max('sequence') ?? 0) + 1);

        $section->steps()->create($data);

        return redirect()->route('templates.show', $section->inspection_type_id)
            ->with('success', 'Step added.');
    }

    public function edit(InspectionStep $step): View
    {
        return view('templates.steps.edit', [
            'section' => $step->section,
            'step' => $step,
        ]);
    }

    public function update(Request $request, InspectionStep $step): RedirectResponse
    {
        $step->update($this->validateStep($request));

        return redirect()->route('templates.show', $step->section->inspection_type_id)
            ->with('success', 'Step updated.');
    }

    public function destroy(InspectionStep $step): RedirectResponse
    {
        $typeId = $step->section->inspection_type_id;
        $step->delete();

        return redirect()->route('templates.show', $typeId)->with('success', 'Step deleted.');
    }

    private function validateStep(Request $request): array
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'question_ar' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'sequence' => ['nullable', 'integer', 'min:0'],
            'show_rating' => ['nullable', 'boolean'],
            'show_text_answer' => ['nullable', 'boolean'],
            'show_multiple_choice' => ['nullable', 'boolean'],
            // Comma separated in the form; mandatory once "Multiple choice" is selected.
            'multiple_choice_options' => ['nullable', 'required_if:show_multiple_choice,1', 'string', 'max:1000'],
            'show_remedial_suggestions' => ['nullable', 'boolean'],
            'photos' => ['required', 'in:not_required,optional,mandatory'],
            'videos' => ['required', 'in:not_required,optional,mandatory'],
        ], [
            'multiple_choice_options.required_if' => 'Add at least one choice option (comma separated) when "Multiple choice" is selected.',
        ]);

        // Normalise booleans + parse comma-separated choices into an array.
        foreach (['show_rating', 'show_text_answer', 'show_multiple_choice', 'show_remedial_suggestions'] as $flag) {
            $validated[$flag] = $request->boolean($flag);
        }

        // At least one answer input must be selected.
        if (! $validated['show_rating'] && ! $validated['show_text_answer'] && ! $validated['show_multiple_choice']) {
            throw ValidationException::withMessages([
                'answer_inputs' => 'Select at least one answer input: Rating, Text answer, or Multiple choice.',
            ]);
        }

        $options = collect(explode(',', (string) $request->input('multiple_choice_options')))
            ->map(fn ($o) => trim($o))
            ->filter()
            ->values()
            ->all();

        // Guard against whitespace-only input (e.g. ", ,") slipping past required_if.
        if ($validated['show_multiple_choice'] && empty($options)) {
            throw ValidationException::withMessages([
                'multiple_choice_options' => 'Add at least one choice option (comma separated) when "Multiple choice" is selected.',
            ]);
        }

        $validated['multiple_choice_options'] = $validated['show_multiple_choice'] && $options
            ? $options
            : null;

        return $validated;
    }
}
