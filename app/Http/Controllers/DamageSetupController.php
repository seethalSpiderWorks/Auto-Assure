<?php

namespace App\Http\Controllers;

use App\Models\DamageColour;
use App\Models\DamageDiagram;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Damage setup: the body diagrams and the colour palette the inspection screen
 * draws with. Both were hard-coded in the Blade view until this module.
 */
class DamageSetupController extends Controller
{
    public function index(): View
    {
        $diagrams = DamageDiagram::ordered()->get();

        return view('damage-setup.index', [
            'diagrams' => $diagrams,
            // Grouped so each diagram's palette is listed under its own heading;
            // the "" bucket holds colours that apply to every diagram.
            'colourGroups' => DamageColour::ordered()->get()
                ->groupBy(fn ($c) => $c->damage_diagram_id ?: ''),
            'colours' => DamageColour::ordered()->get(),
        ]);
    }

    // ---------------------------------------------------------------- diagrams

    public function storeDiagram(Request $request): RedirectResponse
    {
        $data = $this->validateDiagram($request);

        $data['image'] = $request->file('image')->store('damage-diagrams', 'public');
        $data['is_active'] = $request->boolean('is_active');

        DamageDiagram::create($data);

        return back()->with('success', 'Diagram added.');
    }

    public function updateDiagram(Request $request, DamageDiagram $diagram): RedirectResponse
    {
        $data = $this->validateDiagram($request, $diagram);
        $data['is_active'] = $request->boolean('is_active');

        $previous = null;

        if ($request->hasFile('image')) {
            $previous = $diagram->image;
            $data['image'] = $request->file('image')->store('damage-diagrams', 'public');
        }

        $diagram->update($data);

        if ($previous && $previous !== $diagram->image) {
            Storage::disk('public')->delete($previous);
        }

        return back()->with('success', 'Diagram updated.');
    }

    public function destroyDiagram(DamageDiagram $diagram): RedirectResponse
    {
        // The file goes, but inspections that were drawn on this diagram keep
        // their saved mark-up — those PNGs are separate files under the
        // inspection, and the key stays in their damage_images JSON.
        $path = $diagram->image;
        $diagram->delete();

        if ($path) {
            Storage::disk('public')->delete($path);
        }

        return back()->with('success', 'Diagram removed. Mark-ups already saved on inspections are untouched.');
    }

    private function validateDiagram(Request $request, ?DamageDiagram $diagram = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            // Lower-case, underscore-separated, and unique: it keys the JSON that
            // holds every saved mark-up, so it has to stay stable and collision-free.
            'key' => [
                'required', 'string', 'max:60', 'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('damage_diagrams', 'key')->ignore($diagram?->id),
            ],
            'image' => [$diagram ? 'nullable' : 'required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:8192'],
            'sequence' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'key.regex' => 'The key must be lower-case letters, digits and underscores, starting with a letter (e.g. full_body).',
            'image.max' => 'The diagram image must be 8 MB or smaller.',
        ]);
    }

    // ----------------------------------------------------------------- colours

    public function storeColour(Request $request): RedirectResponse
    {
        $data = $this->validateColour($request);
        $data['is_active'] = $request->boolean('is_active');

        DamageColour::create($data);

        return back()->with('success', 'Colour added.');
    }

    public function updateColour(Request $request, DamageColour $colour): RedirectResponse
    {
        $data = $this->validateColour($request);
        $data['is_active'] = $request->boolean('is_active');

        $colour->update($data);

        return back()->with('success', 'Colour updated.');
    }

    public function destroyColour(DamageColour $colour): RedirectResponse
    {
        $colour->delete();

        return back()->with('success', 'Colour removed. Dots already drawn in it keep their colour.');
    }

    private function validateColour(Request $request): array
    {
        $data = $request->validate([
            // Null means the colour applies to every diagram.
            'damage_diagram_id' => ['nullable', 'integer', 'exists:damage_diagrams,id'],
            'label' => ['required', 'string', 'max:80'],
            'colour' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'description' => ['nullable', 'string', 'max:160'],
            'sequence' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'colour.regex' => 'Pick a colour, or type a six-digit hex value like #ff9800.',
        ]);

        $data['damage_diagram_id'] = $data['damage_diagram_id'] ?? null;

        // Stored lower-case so a mark's colour can be compared exactly.
        $data['colour'] = Str::lower($data['colour']);

        return $data;
    }
}
