<?php

namespace App\Http\Controllers;

use App\Models\InspectionSection;
use App\Models\InspectionStep;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InspectionStepController extends Controller
{
    public function create(InspectionSection $section): View
    {
        $section->load(['steps' => fn ($q) => $q->orderBy('sequence')->orderBy('id')]);

        return view('templates.steps.create', [
            'section' => $section,
            'step' => new InspectionStep([
                'photos' => 'not_required',
                'videos' => 'not_required',
                // Every new question is a choice question and starts with the
                // standard option set, so the builder needs no answer-input picker.
                'show_multiple_choice' => true,
                'multiple_choice_options' => InspectionStep::CHOICE_OPTIONS,
            ]),
        ]);
    }

    public function store(Request $request, InspectionSection $section): RedirectResponse
    {
        $data = $this->validateStep($request);
        $data['sequence'] = $data['sequence'] ?? (($section->steps()->max('sequence') ?? 0) + 1);

        $step = $section->steps()->create($data);

        // Back to the same form so questions can be added one after another; the
        // section's questions are listed underneath it.
        return redirect()->route('steps.create', $section)
            ->with('success', 'Question added: '.$step->question);
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
            'multiple_choice_options' => ['nullable', 'string', 'max:1000'],
            'show_remedial_suggestions' => ['nullable', 'boolean'],
            'photos' => ['nullable', 'in:not_required,optional,mandatory'],
            'videos' => ['nullable', 'in:not_required,optional,mandatory'],
        ]);

        // Multiple choice is the only answer input a question offers, so it is
        // always on and rating / text answer are always off. The builder no
        // longer renders those controls at all.
        $validated['show_multiple_choice'] = true;
        $validated['show_rating'] = false;
        $validated['show_text_answer'] = false;
        $validated['show_remedial_suggestions'] = $request->boolean('show_remedial_suggestions');

        // Media requirements are not editable in the builder; keep whatever the
        // question already carries.
        $validated['photos'] = $validated['photos'] ?? InspectionStep::MEDIA_NOT_REQUIRED;
        $validated['videos'] = $validated['videos'] ?? InspectionStep::MEDIA_NOT_REQUIRED;

        $options = collect(explode(',', (string) $request->input('multiple_choice_options')))
            ->map(fn ($o) => trim($o))
            ->filter()
            ->values()
            ->all();

        // A question always ends up with choices — its own, or the standard set.
        $validated['multiple_choice_options'] = $options ?: InspectionStep::CHOICE_OPTIONS;

        return $validated;
    }
}
