<?php

namespace App\Support;

use App\Models\DamageColour;
use App\Models\DamageDiagram;
use App\Models\Inspection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores marked-up damage diagrams against an inspection.
 *
 * The web edit screen and the technician app both save the same thing — one
 * PNG data URL per view plus the dots behind it — so the rules live here rather
 * than in either controller: what a valid view is, what a valid PNG is, which
 * colours a diagram accepts, and how a superseded file is retired.
 */
class DamageDiagramWriter
{
    /** A marked-up diagram larger than this is a runaway canvas, not a drawing. */
    private const MAX_BYTES = 8 * 1024 * 1024;

    /**
     * Apply a save to the inspection. The model is left dirty, not saved — the
     * caller decides when to write, so this can ride along with other changes.
     *
     * A view absent from $images keeps whatever it already had; the same is true
     * of $marks, so saving one diagram never clears another.
     *
     * @param  array<string, string>  $images  view key => PNG data URL
     * @param  array<string, array<int, array{x: mixed, y: mixed, c: mixed}>>|null  $marks
     * @return array{replaced: array<string, string|null>, views: array<int, string>}
     */
    public function apply(Inspection $inspection, array $images, ?array $marks = null): array
    {
        // Diagram keys are configured in Damage Setup, so the valid set is a
        // lookup rather than a constant. Inactive diagrams still accept a save:
        // an inspection already in progress must not lose work because an admin
        // hid the view mid-job.
        $validViews = DamageDiagram::pluck('key')->all();
        $stored = $inspection->damageImages();
        $replaced = [];

        foreach ($images as $view => $dataUrl) {
            abort_unless(in_array($view, $validViews, true), 422, "Unknown damage view: {$view}.");

            $binary = $this->decodePng($dataUrl, $view);

            $path = "inspections/{$inspection->id}/damage/{$view}-".Str::random(20).'.png';
            Storage::disk('public')->put($path, $binary);

            $replaced[$view] = $stored[$view] ?? null;
            $stored[$view] = $path;
        }

        $inspection->damage_images = $stored;

        // The two legacy columns are kept in step so anything still reading them
        // (an older deploy, a report cached mid-rollout) sees the current file.
        foreach (Inspection::LEGACY_DAMAGE_VIEWS as $key => $legacy) {
            if (array_key_exists($key, $stored)) {
                $inspection->{$legacy} = $stored[$key];
            }
        }

        if ($marks !== null) {
            $inspection->damage_marks = $this->mergeMarks($inspection, $marks, $validViews);
        }

        return ['replaced' => $replaced, 'views' => $validViews];
    }

    /**
     * Delete the files a save superseded. Call it only once the new paths are
     * committed, so a failed write never loses the old picture.
     *
     * @param  array<string, string|null>  $replaced
     */
    public function pruneReplaced(Inspection $inspection, array $replaced): void
    {
        $current = $inspection->damageImages();

        foreach ($replaced as $view => $previous) {
            if ($previous && $previous !== ($current[$view] ?? null)) {
                Storage::disk('public')->delete($previous);
            }
        }
    }

    /**
     * Public URLs for every configured view, as the save endpoints report back.
     *
     * @param  array<int, string>  $views
     * @return array<string, string|null>
     */
    public function urls(Inspection $inspection, array $views): array
    {
        $urls = [];

        foreach ($views as $view) {
            $urls[$view] = $inspection->damageDiagramUrl($view);
        }

        return $urls;
    }

    /**
     * Accept only a base64 PNG data URL — nothing else may reach the disk.
     */
    private function decodePng(string $dataUrl, string $view): string
    {
        if (! preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', $dataUrl, $m)) {
            abort(422, "{$view}: damage diagram must be a PNG data URL.");
        }

        $binary = base64_decode($m[1], true);

        if ($binary === false || $binary === '') {
            abort(422, "{$view}: damage diagram could not be decoded.");
        }

        if (strlen($binary) > self::MAX_BYTES) {
            abort(422, "{$view}: damage diagram is too large.");
        }

        // Confirm the bytes really are a PNG, not base64 of something else.
        if (substr($binary, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            abort(422, "{$view}: damage diagram is not a valid PNG.");
        }

        return $binary;
    }

    /**
     * Validate the posted dots and fold them into what the inspection holds.
     *
     * @param  array<string, mixed>  $marks
     * @param  array<int, string>  $validViews
     * @return array<string, array<int, array{x: float, y: float, c: string}>>
     */
    private function mergeMarks(Inspection $inspection, array $marks, array $validViews): array
    {
        $current = is_array($inspection->damage_marks) ? $inspection->damage_marks : [];

        // Dots already stored for this view are grandfathered in. Palettes used
        // to be shared across every diagram, so a chassis marked before they were
        // split per diagram carries body colours its palette no longer offers.
        // Both save paths re-post every dot, so rejecting those would make such
        // an inspection impossible to edit at all. New colours are still held to
        // the palette.
        foreach ($marks as $view => $list) {
            abort_unless(in_array($view, $validViews, true), 422, "Unknown damage view: {$view}.");

            $allowed = array_unique(array_merge(
                $this->palette($view),
                array_map(
                    fn ($m) => strtolower((string) ($m['c'] ?? '')),
                    (array) ($current[$view] ?? [])
                )
            ));

            foreach ((array) $list as $m) {
                $colour = strtolower((string) ($m['c'] ?? ''));
                abort_unless($colour !== '' && in_array($colour, $allowed, true), 422, "{$view}: that colour is not in this diagram's palette.");
            }

            $current[$view] = array_values(array_map(fn ($m) => [
                'x' => round((float) $m['x'], 1),
                'y' => round((float) $m['y'], 1),
                'c' => $m['c'],
            ], (array) $list));
        }

        return $current;
    }

    /**
     * A diagram's own colours plus any left unassigned, lower-cased — the same
     * hex in a different case is the same colour.
     *
     * @return array<int, string>
     */
    private function palette(string $view): array
    {
        $diagram = DamageDiagram::where('key', $view)->first();

        return $diagram
            ? array_map('strtolower', DamageColour::forDiagram($diagram->id)->pluck('colour')->all())
            : [];
    }
}
