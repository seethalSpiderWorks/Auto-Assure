<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores FCM push-notification device tokens per user. One user can have many
 * tokens (multiple devices); each token is unique and removed when it becomes
 * invalid (UNREGISTERED) or on logout.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('device_tokens')) {
            return;
        }

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('token', 512)->unique();
            $table->string('platform', 20)->nullable();   // android | ios | web
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
