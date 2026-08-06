<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\InspectionTypeController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\VehicleLookupController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\NotificationController;

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

// Public vehicle lookups (car makes and models) — used by both the mobile app
// and the web-based Leads form via AJAX. Lookup data is not sensitive, so the
// endpoints live outside auth:sanctum for cross-context access.
Route::get('/vehicle-lookups/car-make', [VehicleLookupController::class, 'show']);
Route::get('/vehicle-lookups/car-model', [VehicleLookupController::class, 'show']);

// --- Protected endpoints (require a valid Sanctum token) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserAuthController::class, 'getUser'])->name('app.user');
    Route::get('get-user', [UserAuthController::class, 'getUser'])->name('app.get-user');
    Route::post('logout', [UserAuthController::class, 'logout'])->name('app.logout');

    // Push notification (FCM) device tokens.
    Route::post('/device-token', [DeviceTokenController::class, 'store']);
    Route::delete('/device-token', [DeviceTokenController::class, 'destroy']);

    // In-app notifications (stored in app_notifications table).
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/delete', [NotificationController::class, 'destroy']);
    
    Route::get('/inspection-types', [InspectionTypeController::class, 'index']);
    Route::get('/inspection-types/{inspectionType}', [InspectionTypeController::class, 'show']);

    // List inspections for the authenticated technician: today's jobs first (in
    // slot order), then upcoming days latest-first, then overdue, then unscheduled.
    //   ?status=pending          filter by status (default: everything but completed)
    //   ?date=today              only jobs scheduled today
    //   ?date=2026-07-28         only jobs scheduled on that day
    Route::get('/inspections', [InspectionController::class, 'index']);

    // Full technician history — in progress, completed and cancelled jobs.
    //   ?status=in_progress | completed | cancelled
    Route::get('/technician/history', [InspectionController::class, 'history']);

    // One history record, including the cancellation reason/date when cancelled.
    Route::get('/technician/history/{inspection}', [InspectionController::class, 'historyDetail']);

    // Inspection type (template + sections/steps) used by a specific inspection.
    Route::get('/inspections/{inspection}/type', [InspectionTypeController::class, 'forInspection']);

    Route::get('/inspections/{inspection}', [InspectionController::class, 'show']);

    // Customer & vehicle details. The POST alias exists because PHP does not
    // parse multipart/form-data bodies on PUT — send the vehicle_image file to
    // POST (or POST to the PUT url with _method=PUT); JSON-only updates can
    // still use PUT.
    Route::put('/inspections/{inspection}/customer', [InspectionController::class, 'updateCustomer']);
    Route::post('/inspections/{inspection}/customer', [InspectionController::class, 'updateCustomer']);

    Route::post('/inspections/{inspection}/answers', [InspectionController::class, 'saveAnswers']);       // Screen 4/5
    Route::post('/inspections/{inspection}/media', [InspectionController::class, 'uploadMedia']);
    Route::delete('/media/{media}', [InspectionController::class, 'deleteMedia']);

    // Additional media for the inspection as a whole (not tied to a step or a
    // section) — the same bucket as "Additional media" on the web edit screen.
    //   POST body: files[] (+ optional labels[]), or a single file (+ label).
    Route::get('/inspections/{inspection}/extra-media', [InspectionController::class, 'extraMedia']);
    Route::post('/inspections/{inspection}/extra-media', [InspectionController::class, 'uploadExtraMedia']);
    //   DELETE .../{media}  removes one; DELETE the collection with media_ids[] removes several.
    Route::delete('/inspections/{inspection}/extra-media/{media}', [InspectionController::class, 'deleteExtraMedia']);
    Route::delete('/inspections/{inspection}/extra-media', [InspectionController::class, 'deleteExtraMediaBulk']);

    // Summary areas (Exterior, Engine, Brakes, …) from tbl_summary_type for a
    // given inspection, with the inspection details and any saved note per area.
    Route::get('/inspections/{inspection}/summary/list', [InspectionController::class, 'summaryTypeList']);

    // Save per-area summary notes for an inspection (a note is required for every area).
    Route::post('/inspections/{inspection}/summaries', [InspectionController::class, 'saveSummaries']);

    Route::post('/inspections/{inspection}/submit', [InspectionController::class, 'submit']);

    // Cancel an inspection. An admin may cancel any, a technician only their own
    // (enforced in the controller). Body: cancel_reason (required, 5–500 chars).
    Route::post('/inspections/{inspection}/cancel', [InspectionController::class, 'cancel']);
    
    Route::get('/inspections/{inspection}/summary', [InspectionController::class, 'summary']);

    // Vehicle lookup dropdowns (exterior colours, fuel types, gearboxes, steering sides).
    Route::get('/vehicle-lookups', [VehicleLookupController::class, 'index']);
    Route::get('/vehicle-lookups/{field}', [VehicleLookupController::class, 'show']);


});
