<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop `reference_no`. The inspection's reference is the linked lead's
 * tbl_lead.lead_unq_id, exposed by Inspection::getReferenceAttribute(), so a
 * separately stored reference is redundant and could drift from the lead.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inspections', 'reference_no')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->dropColumn('reference_no');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('inspections', 'reference_no')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->string('reference_no', 100)->nullable()->after('id');
            });
        }
    }
};
