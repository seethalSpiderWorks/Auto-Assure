<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\VehicleLookups;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dropdown options for the inspection's vehicle-detail fields.
 *
 * The `name` of an option is the value to send back on
 * PUT /api/inspections/{inspection}/customer — that field stores the name,
 * not the lookup id.
 */
class VehicleLookupController extends Controller
{
    /**
     * Every vehicle dropdown in one call.
     *
     * GET /api/vehicle-lookups
     */
    public function index(): JsonResponse
    {
        return response()->json(['data' => VehicleLookups::all()]);
    }

    /**
     * One dropdown's options.
     *
     * GET /api/vehicle-lookups/{field}   e.g. /api/vehicle-lookups/gearbox
     *
     * For related lookups like car_model, pass query params to filter:
     *   GET /api/vehicle-lookups/car-model?make_id=5
     */
    public function show(Request $request, string $field): JsonResponse
    {
        // Accept the hyphenated form too (gearbox, steering-side, steering_side).
        $field = str_replace('-', '_', $field);

        if (! VehicleLookups::supports($field)) {
            return response()->json([
                'message' => "Unknown vehicle lookup [{$field}].",
                'supported' => VehicleLookups::fields(),
            ], 404);
        }

        // Build extra filters from query parameters.
        // For car_model, the frontend sends ?make_id=5 to filter by make.
        $extraWhere = [];
        if ($request->filled('make_id')) {
            // tbl_model.model_make is the foreign key to tbl_make.make_id
            $extraWhere['model_make'] = (int) $request->input('make_id');
        }

        return response()->json([
            'field' => $field,
            'data' => VehicleLookups::options($field, $extraWhere),
        ]);
    }
}
