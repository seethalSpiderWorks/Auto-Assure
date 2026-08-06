<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels — technician app (Pusher)
|--------------------------------------------------------------------------
|
| Authorized via api/broadcasting/auth (Sanctum). A user may only listen on
| their OWN channel, and only technicians (app users) are allowed.
|
*/

// Subscribe to `private-technician.{id}` from the app for live events.
Broadcast::channel('technician.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->isTechnician();
});
