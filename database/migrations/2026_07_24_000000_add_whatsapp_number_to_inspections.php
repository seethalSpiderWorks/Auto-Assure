<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add a WhatsApp contact number to inspections, kept alongside the customer's
 * phone/email so the technician (and the app) can reach the owner on WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'whatsapp_number')) {
                $table->string('whatsapp_number', 50)->nullable()->after('customer_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (Schema::hasColumn('inspections', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};
