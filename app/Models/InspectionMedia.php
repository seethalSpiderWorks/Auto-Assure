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
