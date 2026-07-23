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
     * @return array<int, array{id:int, name:string}>
     */
    public static function options(string $field): array
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
