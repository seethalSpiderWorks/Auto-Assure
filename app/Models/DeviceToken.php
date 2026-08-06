<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A push-notification device token (FCM) registered by a user's app install.
 * A user may have several (multiple devices); tokens are unique per device.
 */
class DeviceToken extends Model
{
    protected $fillable = ['user_id', 'token', 'device_type', 'device_name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
