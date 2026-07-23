<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Align `inspections` with the agreed vehicle-detail field set:
 * the columns listed on /viewInspectionreports (Reference, Name, Date of
 * Inspection, Plate No, Current Status, Expired Status) plus Make, Model,
 * Year, Manufacturing Year, VIN/Chassis No, Odometer, Region, Exterior Colour,
 * Transmission, Fuel Type, Body Type, Keys, With Service History and Last
 * Service Date.
 *
 * Relation keys (lead_id, branch_id, technician_id, inspection_type_id), the
 * status workflow and the report fields are deliberately left in place — the
 * inspection module depends on them.
 *
 * Existing equivalents kept under their current names: Name = customer_name,
 * Plate No = registration_number, Current Status = status,
 * Exterior Colour = color, Keys = number_of_keys.
 */
return new class extends Migration
{
    /** Columns replaced by the agreed field set. */
    private array $dropped = [
        'variant',
        'vehicle_type',
        'manufacturer_name',
        'country_of_origin',
        'country_of_export',
        'motor_power_kw',
        'cylinders_cc',
        'passengers',
        'fuel_economy',
    ];

    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            if (! Schema::hasColumn('inspections', 'reference_no')) {
                $table->string('reference_no', 100)->nullable()->after('id');
            }
            if (! Schema::hasColumn('inspections', 'date_of_inspection')) {
                $table->date('date_of_inspection')->nullable()->after('customer_phone');
            }
            if (! Schema::hasColumn('inspections', 'expired_status')) {
                $table->string('expired_status', 80)->nullable()->after('status');
            }
            if (! Schema::hasColumn('inspections', 'manufacturing_year')) {
                $table->smallInteger('manufacturing_year')->unsigned()->nullable()->after('car_year');
            }
            if (! Schema::hasColumn('inspections', 'region')) {
                $table->string('region', 100)->nullable()->after('color');
            }
            if (! Schema::hasColumn('inspections', 'with_service_history')) {
                $table->boolean('with_service_history')->nullable()->after('number_of_keys');
            }
            if (! Schema::hasColumn('inspections', 'last_service_date')) {
                $table->date('last_service_date')->nullable()->after('with_service_history');
            }
        });

        $drop = array_filter(
            $this->dropped,
            fn ($column) => Schema::hasColumn('inspections', $column)
        );

        if ($drop !== []) {
            Schema::table('inspections', function (Blueprint $table) use ($drop) {
                $table->dropColumn(array_values($drop));
            });
        }
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->string('variant', 100)->nullable();
            $table->string('vehicle_type', 50)->nullable();
            $table->string('manufacturer_name', 100)->nullable();
            $table->string('country_of_origin', 100)->nullable();
            $table->string('country_of_export', 100)->nullable();
            $table->integer('motor_power_kw')->unsigned()->nullable();
            $table->string('cylinders_cc', 50)->nullable();
            $table->smallInteger('passengers')->unsigned()->nullable();
            $table->string('fuel_economy', 30)->nullable();

            $table->dropColumn([
                'reference_no',
                'date_of_inspection',
                'expired_status',
                'manufacturing_year',
                'region',
                'with_service_history',
                'last_service_date',
            ]);
        });
    }
};
