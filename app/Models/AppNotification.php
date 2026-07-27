<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One in-app notification for the technician app's notification list.
 * Created alongside an FCM push by PushNotificationService::notifyUser().
 */
class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = ['user_id', 'type', 'title', 'body', 'data', 'read_at'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
