<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\VehicleLookups;
use Illuminate\Http\JsonResponse;

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
     */
    public function show(string $field): JsonResponse
    {
        // Accept the hyphenated form too (gearbox, steering-side, steering_side).
        $field = str_replace('-', '_', $field);

        if (! VehicleLookups::supports($field)) {
            return response()->json([
                'message' => "Unknown vehicle lookup [{$field}].",
                'supported' => VehicleLookups::fields(),
            ], 404);
        }

        return response()->json([
            'field' => $field,
            'data' => VehicleLookups::options($field),
        ]);
    }
}
