<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Additional media may now be a PDF as well as a photo or a video, so the
 * inspection_media.type enum needs a third value. Existing rows are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE inspection_media MODIFY COLUMN type ENUM('photo','video','document') NOT NULL");
    }

    public function down(): void
    {
        // Documents have no place in the old two-value enum — fold them into
        // 'photo' first, otherwise MySQL would blank them out on the way down.
        DB::table('inspection_media')->where('type', 'document')->update(['type' => 'photo']);

        DB::statement("ALTER TABLE inspection_media MODIFY COLUMN type ENUM('photo','video') NOT NULL");
    }
};
