<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InspectionMedia extends Model
{
    protected $table = 'inspection_media';

    protected $fillable = [
        'inspection_detail_id', 'type', 'disk', 'path', 'original_name', 'label', 'mime_type', 'size',
    ];

    protected $appends = ['url'];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(InspectionDetail::class, 'inspection_detail_id');
    }

    /** File extensions we treat as video regardless of what `type` says. */
    public const VIDEO_EXTENSIONS = ['mp4', 'mov', 'm4v', 'webm', 'mkv', 'avi', '3gp', '3g2', 'ogv', 'qt'];

    /** File extensions we treat as a document (downloadable, never rendered inline). */
    public const DOCUMENT_EXTENSIONS = ['pdf'];

    /**
     * Size cap for documents, in kilobytes. Photos and videos keep the larger
     * media limit; a PDF attachment is capped at 10 MB.
     */
    public const MAX_DOCUMENT_KB = 10240;

    /** The same cap in bytes, for client-side and byte-wise checks. */
    public const MAX_DOCUMENT_BYTES = self::MAX_DOCUMENT_KB * 1024;

    /**
     * The message shown when a document exceeds MAX_DOCUMENT_KB. Shared by the
     * API, the web endpoint and the edit screen so the wording never diverges.
     */
    public static function documentTooLargeMessage(string $name, int $bytes): string
    {
        return sprintf(
            '"%s" is %s MB — PDF files must be %d MB or smaller.',
            $name,
            number_format($bytes / 1048576, 1),
            (int) (self::MAX_DOCUMENT_KB / 1024)
        );
    }

    /**
     * Whether this file is a document (currently PDFs) rather than something that
     * can be shown in an <img> or a <video>. Checked before isVideo()/image
     * rendering everywhere, so a PDF never lands in an image tag.
     */
    public function isDocument(): bool
    {
        if ($this->type === 'document' || $this->mime_type === 'application/pdf') {
            return true;
        }

        return in_array(strtolower(pathinfo((string) $this->path, PATHINFO_EXTENSION)), self::DOCUMENT_EXTENSIONS, true);
    }

    /**
     * Whether this file should be rendered with a video player.
     *
     * `type` is set at upload time from the client's declared MIME, which the app
     * often sends as application/octet-stream — so some clips were stored as
     * 'photo' and then rendered in an <img>, showing as broken. The file itself is
     * the reliable signal, so fall back to its extension.
     */
    public function isVideo(): bool
    {
        // A document is never a video, whatever its declared type says.
        if ($this->isDocument()) {
            return false;
        }

        if ($this->type === 'video' || str_starts_with((string) $this->mime_type, 'video/')) {
            return true;
        }

        return in_array(strtolower(pathinfo((string) $this->path, PATHINFO_EXTENSION)), self::VIDEO_EXTENSIONS, true);
    }

    public function getUrlAttribute(): string
    {
        // For the local "public" disk, build the URL against the current request
        // host (e.g. http://127.0.0.1:8000) instead of APP_URL, so thumbnails work
        // on any dev port and the mobile app gets a correct absolute URL.
        if ($this->disk === 'public') {
            return url('storage/'.ltrim($this->path, '/'));
        }

        // Remote disks (e.g. S3) already return a fully-qualified URL.
        return Storage::disk($this->disk)->url($this->path);
    }
}
