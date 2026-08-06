<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bring `inspections` in line with the field names used on the legacy
 * /inspectionreport form, and add the fields it captures that were missing:
 * Name in Arabic, Cylinders and Steering Side.
 *
 * Three existing columns are renamed to the legacy vocabulary rather than
 * duplicated — they already hold exactly this data:
 *   color               -> exterior_color
 *   registration_number -> plate_no
 *   transmission        -> gearbox   (tbl_gearbox_type is the transmission lookup)
 *
 * Values are stored as the lookup *name* (e.g. "Automatic"), not the lookup id,
 * so existing rows stay valid and the report views keep printing the column
 * directly without a join.
 */
return new class extends Migration
{
    /** old name => new name */
    private array $renames = [
        'color' => 'exterior_color',
        'registration_number' => 'plate_no',
        'transmission' => 'gearbox',
    ];

    public function up(): void
    {
        foreach ($this->renames as $from => $to) {
            if (Schema::hasColumn('inspections', $from) && ! Schema::hasColumn('inspections', $to)) {
                Schema::table('inspections', function (Blueprint $table) use ($from, $to) {
                    $table->renameColumn($from, $to);
                });
            }
        }

        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'customer_name_ar')) {
                $table->string('customer_name_ar', 255)->nullable()->after('customer_name');
            }
            if (! Schema::hasColumn('inspections', 'cylinders')) {
                $table->string('cylinders', 50)->nullable()->after('gearbox');
            }
            if (! Schema::hasColumn('inspections', 'steering_side')) {
                $table->string('steering_side', 50)->nullable()->after('cylinders');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropColumn(['customer_name_ar', 'cylinders', 'steering_side']);
        });

        foreach (array_reverse($this->renames, true) as $from => $to) {
            if (Schema::hasColumn('inspections', $to) && ! Schema::hasColumn('inspections', $from)) {
                Schema::table('inspections', function (Blueprint $table) use ($from, $to) {
                    $table->renameColumn($to, $from);
                });
            }
        }
    }
};
