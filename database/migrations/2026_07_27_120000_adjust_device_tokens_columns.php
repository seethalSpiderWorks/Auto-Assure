<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Align device_tokens with the app's register payload: device_type + device_name
 * (replacing the earlier `platform`), and a composite (user_id, token) unique so
 * updateOrCreate(user_id, token) is reliable even when a device switches accounts.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('device_tokens', 'device_type')) {
                $table->string('device_type', 20)->nullable()->after('token');
            }
            if (! Schema::hasColumn('device_tokens', 'device_name')) {
                $table->string('device_name')->nullable()->after('device_type');
            }
        });

        // Swap the global token unique for a composite (user_id, token) unique.
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->dropUnique(['token']);
        });

        Schema::table('device_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('device_tokens', 'platform')) {
                $table->dropColumn('platform');
            }
            $table->unique(['user_id', 'token'], 'device_tokens_user_token_unique');
        });
    }

    public function down(): void
    {
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->dropUnique('device_tokens_user_token_unique');
            $table->string('platform', 20)->nullable();
            $table->dropColumn(['device_type', 'device_name']);
            $table->unique('token');
        });
    }
};
