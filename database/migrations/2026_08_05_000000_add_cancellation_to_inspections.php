<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Admin-only inspection cancellation: who cancelled it, when, and why.
 * The status itself stays on the existing `status` column ('cancelled').
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('inspections', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('cancelled_at');
            }
            if (! Schema::hasColumn('inspections', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancel_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            foreach (['cancelled_at', 'cancel_reason', 'cancelled_by'] as $column) {
                if (Schema::hasColumn('inspections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
