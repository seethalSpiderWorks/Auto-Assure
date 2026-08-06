<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Generates width-constrained, cached JPEG thumbnails for images on the local
 * "public" disk. The inspection report renders phone photos (1–3 MB each) into
 * small gallery cells and thumbnails; serving the originals makes the report/PDF
 * preview download tens of megabytes. This shrinks each to a few tens of KB.
 *
 * Thumbnails are generated on first request and cached under
 * storage/app/public/_thumbs/{width}/, then served as ordinary static files, so
 * repeat views (and the print/PDF pass) never touch PHP again. Any failure —
 * remote disk, unreadable file, unsupported format, no GD — falls back to the
 * original URL, so a report never breaks over a thumbnail.
 */
class Thumbnailer
{
    /** Formats GD can decode here. Others (gif, svg, …) are served untouched. */
    private const SUPPORTED = ['jpg', 'jpeg', 'png', 'webp'];

    public static function url(?string $path, int $width, string $disk = 'public'): ?string
    {
        if (! $path) {
            return null;
        }

        $original = self::originalUrl($path, $disk);

        // Only local public-disk images can be read and resized cheaply here.
        if ($disk !== 'public' || ! function_exists('imagecreatetruecolor')) {
            return $original;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (! in_array($ext, self::SUPPORTED, true)) {
            return $original;
        }

        try {
            $public = Storage::disk('public');
            $src = $public->path($path);
            if (! is_file($src)) {
                return $original;
            }

            // Key the cache on path + mtime so replacing a photo re-generates it.
            $cacheRel = '_thumbs/'.$width.'/'.sha1($path.'|'.filemtime($src)).'.jpg';
            $cacheAbs = $public->path($cacheRel);

            if (! is_file($cacheAbs) && ! self::generate($src, $cacheAbs, $ext, $width)) {
                return $original;
            }

            return self::originalUrl($cacheRel, 'public');
        } catch (\Throwable $e) {
            return $original;
        }
    }

    private static function originalUrl(string $path, string $disk): string
    {
        // Mirror InspectionMedia::getUrlAttribute so URLs stay host-relative on
        // the public disk and fully-qualified on remote disks.
        return $disk === 'public'
            ? url('storage/'.ltrim($path, '/'))
            : Storage::disk($disk)->url($path);
    }

    private static function generate(string $src, string $dest, string $ext, int $width): bool
    {
        $size = @getimagesize($src);
        if (! $size || $size[0] < 1 || $size[1] < 1) {
            return false;
        }
        [$w, $h] = $size;

        // Never upscale — a small original is already cheap to serve.
        $width  = min($width, $w);
        $height = max(1, (int) round($h * ($width / $w)));

        $img = match ($ext) {
            'png'  => @imagecreatefrompng($src),
            'webp' => @imagecreatefromwebp($src),
            default => @imagecreatefromjpeg($src),
        };
        if (! $img) {
            return false;
        }

        $canvas = imagecreatetruecolor($width, $height);
        // Flatten any transparency onto white — the report shows photos on white
        // cards, and JPEG has no alpha channel.
        imagefilledrectangle($canvas, 0, 0, $width, $height, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $img, 0, 0, 0, 0, $width, $height, $w, $h);

        if (! is_dir(dirname($dest))) {
            @mkdir(dirname($dest), 0755, true);
        }
        $ok = imagejpeg($canvas, $dest, 80);

        imagedestroy($img);
        imagedestroy($canvas);

        return $ok;
    }
}
