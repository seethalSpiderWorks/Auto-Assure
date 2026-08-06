<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Broadcasting auth endpoint at api/broadcasting/auth, guarded by Sanctum
        // — the technician app authorizes private channels with its bearer token.
        Broadcast::routes(['prefix' => 'api', 'middleware' => ['auth:sanctum']]);

        require base_path('routes/channels.php');
    }
}
