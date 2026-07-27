<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * In-app notification inbox for the technician app. Each row is one notification
 * shown in the app's notification list; the same event also triggers an FCM push.
 * App-only (consumed via the Sanctum API), not surfaced on the web CRM.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('app_notifications')) {
            return;
        }

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();   // recipient (technician)
            $table->string('type', 60)->nullable();           // e.g. inspection_assigned
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();                 // extra payload (ids, deep-link)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
