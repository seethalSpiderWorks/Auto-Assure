<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * The vehicle-detail dropdown sources shared by the inspection edit screen and
 * the API. These are the same lookup tables (and the same status / publish
 * filters) the legacy /inspectionreport form reads, so both screens always
 * offer an identical set of choices.
 *
 * `inspections` stores the option NAME, not the lookup id — so `name` is the
 * value clients post back to PUT /api/inspections/{inspection}/customer. The
 * id is exposed only so a client can key/sort on something stable.
 */
class VehicleLookups
{
    /**
     * field name => how to read its options.
     *
     * @var array<string, array{table:string, id:string, name:string, where:array<string,int>, order:?string}>
     */
    private const SOURCES = [
        'exterior_color' => [
            'table' => 'tbl_exterior_color',
            'id' => 'exte_color_id',
            'name' => 'exte_color_name',
            'where' => ['exte_color_status' => 0, 'exte_color_publish_status' => 1],
            'order' => 'exte_color_name',
        ],
        'fuel_type' => [
            'table' => 'tbl_fuel_type',
            'id' => 'fuel_type_id',
            'name' => 'fuel_type_name',
            'where' => ['fuel_type_status' => 0],
            'order' => null,
        ],
        'gearbox' => [
            'table' => 'tbl_gearbox_type',
            'id' => 'gearbox_type_id',
            'name' => 'gearbox_type_name',
            'where' => ['gearbox_type_status' => 0],
            'order' => null,
        ],
        'steering_side' => [
            'table' => 'tbl_steering_side',
            'id' => 'steering_side_id',
            'name' => 'steering_side_name',
            'where' => ['steering_side_status' => 0],
            'order' => null,
        ],
        // Car makes — used by the Add Leads form and the inspectio edit screen.
        'car_make' => [
            'table' => 'tbl_make',
            'id' => 'make_id',
            'name' => 'make_name',
            'where' => ['make_status' => 0, 'make_publish_status' => 1],
            'order' => 'make_name',
        ],
        // Car models — filtered by make_id via the show() controller.
        'car_model' => [
            'table' => 'tbl_model',
            'id' => 'model_id',
            'name' => 'model_name',
            'where' => ['model_status' => 0, 'model_publish_status' => 1],
            'order' => 'model_name',
        ],
    ];

    /**
     * The field names this class can resolve.
     *
     * @return array<int, string>
     */
    public static function fields(): array
    {
        return array_keys(self::SOURCES);
    }

    public static function supports(string $field): bool
    {
        return isset(self::SOURCES[$field]);
    }

    /**
     * Options for one field as [['id' => int, 'name' => string], ...].
     *
     * Accepts optional extra where conditions (e.g. ['model_make' => 5])
     * to filter related lookups like car_model by make_id.
     *
     * @param  array<string, mixed>  $extraWhere  Column => value pairs.
     * @return array<int, array{id:int, name:string}>
     */
    public static function options(string $field, array $extraWhere = []): array
    {
        $source = self::SOURCES[$field] ?? null;
        if (! $source) {
            return [];
        }

        $query = DB::table($source['table'])->select([
            $source['id'].' as id',
            $source['name'].' as name',
        ]);

        foreach ($source['where'] as $column => $value) {
            $query->where($column, $value);
        }

        // Apply dynamic filters (e.g. model_make for car_model).
        foreach ($extraWhere as $column => $value) {
            $query->where($column, $value);
        }

        if ($source['order']) {
            $query->orderBy($source['order']);
        }

        return $query->get()
            ->map(fn ($row) => ['id' => (int) $row->id, 'name' => (string) $row->name])
            ->all();
    }

    /**
     * Just the option names for one field — what the Blade selects render.
     *
     * @return array<int, string>
     */
    public static function names(string $field): array
    {
        return array_column(self::options($field), 'name');
    }

    /**
     * Every field's options, keyed by field name.
     *
     * @return array<string, array<int, array{id:int, name:string}>>
     */
    public static function all(): array
    {
        $out = [];
        foreach (self::fields() as $field) {
            $out[$field] = self::options($field);
        }

        return $out;
    }
}
