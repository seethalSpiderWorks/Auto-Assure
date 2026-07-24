<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\InspectionTypeController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\VehicleLookupController;

/*
|--------------------------------------------------------------------------
| Common API Routes
|--------------------------------------------------------------------------
|
| Central entry point for the application's API. Public routes (login) live
| outside the auth group; everything else is protected by Sanctum token auth.
|
*/

// --- Public endpoints ---
Route::post('login', [UserAuthController::class, 'login'])->name('app.login');

// --- Protected endpoints (require a valid Sanctum token) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserAuthController::class, 'getUser'])->name('app.user');
    Route::get('get-user', [UserAuthController::class, 'getUser'])->name('app.get-user');
    Route::post('logout', [UserAuthController::class, 'logout'])->name('app.logout');
    
    Route::get('/inspection-types', [InspectionTypeController::class, 'index']);
    Route::get('/inspection-types/{inspectionType}', [InspectionTypeController::class, 'show']);

    // List inspections (pass ?technician_id=.. to filter; falls back to the auth user).
    Route::get('/inspections', [InspectionController::class, 'index']);

    // Full technician history — every inspection with sections, steps, answers & customer details.
    Route::get('/technician/history', [InspectionController::class, 'history']);
    
    Route::get('/technician/history/{inspection}', [InspectionController::class, 'historyDetail']);


    // Inspection type (template + sections/steps) used by a specific inspection.
    Route::get('/inspections/{inspection}/type', [InspectionTypeController::class, 'forInspection']);

    Route::get('/inspections/{inspection}', [InspectionController::class, 'show']);

    Route::put('/inspections/{inspection}/customer', [InspectionController::class, 'updateCustomer']);

    Route::post('/inspections/{inspection}/answers', [InspectionController::class, 'saveAnswers']);       // Screen 4/5
    Route::post('/inspections/{inspection}/media', [InspectionController::class, 'uploadMedia']);
    Route::delete('/media/{media}', [InspectionController::class, 'deleteMedia']);

    // Master list of summary areas (Exterior, Engine, Brakes, …) from tbl_summary_type.
    Route::get('/summary/list', [InspectionController::class, 'summaryTypeList']);

    // Save per-area summary notes for an inspection (a note is required for every area).
    Route::post('/inspections/{inspection}/summaries', [InspectionController::class, 'saveSummaries']);

    Route::post('/inspections/{inspection}/submit', [InspectionController::class, 'submit']);
    
    Route::get('/inspections/{inspection}/summary', [InspectionController::class, 'summary']);

    // Vehicle lookup dropdowns (exterior colours, fuel types, gearboxes, steering sides).
    Route::get('/vehicle-lookups', [VehicleLookupController::class, 'index']);
    Route::get('/vehicle-lookups/{field}', [VehicleLookupController::class, 'show']);


});
