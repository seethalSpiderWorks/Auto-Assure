<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Core;

use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionTypeController;
use App\Http\Controllers\InspectionSectionController;
use App\Http\Controllers\InspectionStepController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/',[LoginController::class,'showLoginForm'])->name('/');
Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login'])->name('authenticate');

//Route::post('users/resetmyUser', [Core::class,'resetMyPassword']); 

######################### Dashboard #####################################

Route::group(['middleware' => 'auth'], function(){
	Route::get('/dashboard', [HomeController::class,'dashboard']);
	Route::get('/dashboardNew', [HomeController::class,'dashboardNew']);
	Route::post('/logout', [HomeController::class,'logout']);
	
	Route::post('users/resetmyUser', [App\Http\Controllers\Core::class,'resetMyPassword']); 
	
	Route::get('/version', [App\Http\Controllers\HomeController::class,'version']); 
	Route::get('/line_chart', [App\Http\Controllers\HomeController::class,'line_chart']); 
	Route::get('/line_chart_weekly', [App\Http\Controllers\HomeController::class,'line_chart_weekly']); 
	Route::get('/line_chart_monthly', [App\Http\Controllers\HomeController::class,'line_chart_monthly']); 
	Route::get('/coureLeadCount', [App\Http\Controllers\HomeController::class,'coureLeadCount']); 
	Route::post('/store-token', [App\Http\Controllers\NotificationSendController::class, 'updateDeviceToken'])->name('store.token');
    Route::post('/send-web-notification', [App\Http\Controllers\NotificationSendController::class, 'sendNotification'])->name('send.web-notification');
}); 



Route::group(['middleware' => 'auth'], function () {
    Route::get('inspections', [InspectionController::class, 'index'])->name('inspections.index');
    Route::get('inspections/{inspection}/edit', [InspectionController::class, 'edit'])->name('inspections.edit');
    Route::get('inspections/{inspection}/details', [InspectionController::class, 'show'])->name('inspections.show');
    Route::get('inspections/{inspection}/report', [InspectionController::class, 'report'])->name('inspections.report');
    Route::get('inspections/{inspection}/summary', [InspectionController::class, 'summary'])->name('inspections.summary');
    Route::post('inspections/{inspection}/start', [InspectionController::class, 'start'])->name('inspections.start');
    Route::put('inspections/{inspection}', [InspectionController::class, 'update'])->name('inspections.update');

    // AJAX auto-save endpoints
    Route::post('inspections/{inspection}/autosave-step', [InspectionController::class, 'autosaveStep'])->name('inspections.autosave.step');
    Route::post('inspections/{inspection}/autosave-customer', [InspectionController::class, 'autosaveCustomer'])->name('inspections.autosave.customer');
    Route::post('inspections/{inspection}/media', [InspectionController::class, 'uploadMedia'])->name('inspections.media.upload');
    Route::post('inspections/{inspection}/extra-media', [InspectionController::class, 'uploadExtraMedia'])->name('inspections.extra-media.upload');
    Route::delete('inspection-media/{media}', [InspectionController::class, 'destroyMedia'])->name('inspection-media.destroy');
    Route::post('inspection-media/{media}/label', [InspectionController::class, 'updateMediaLabel'])->name('inspection-media.label');
});

Route::group(['middleware' => 'auth', 'prefix' => 'inspection-templates'], function () {
    // Types (templates)
    Route::get('/', [InspectionTypeController::class, 'index'])->name('templates.index');
    Route::get('create', [InspectionTypeController::class, 'create'])->name('templates.create');
    Route::post('/', [InspectionTypeController::class, 'store'])->name('templates.store');
    Route::get('{template}', [InspectionTypeController::class, 'show'])->name('templates.show');
    Route::get('{template}/edit', [InspectionTypeController::class, 'edit'])->name('templates.edit');
    Route::put('{template}', [InspectionTypeController::class, 'update'])->name('templates.update');
    Route::delete('{template}', [InspectionTypeController::class, 'destroy'])->name('templates.destroy');

    // Sections
    Route::post('{template}/sections', [InspectionSectionController::class, 'store'])->name('sections.store');
    Route::put('sections/{section}', [InspectionSectionController::class, 'update'])->name('sections.update');
    Route::delete('sections/{section}', [InspectionSectionController::class, 'destroy'])->name('sections.destroy');

    // Steps
    Route::get('sections/{section}/steps/create', [InspectionStepController::class, 'create'])->name('steps.create');
    Route::post('sections/{section}/steps', [InspectionStepController::class, 'store'])->name('steps.store');
    Route::get('steps/{step}/edit', [InspectionStepController::class, 'edit'])->name('steps.edit');
    Route::put('steps/{step}', [InspectionStepController::class, 'update'])->name('steps.update');
    Route::delete('steps/{step}', [InspectionStepController::class, 'destroy'])->name('steps.destroy');
});

######################### Dashboard #####################################

######################### Coockie ######################################
Route::post('password/setcookie', [LoginController::class,'setcookie']);
Route::post('password/getCookie', [LoginController::class,'getCookie']);
######################### Coockie ######################################


########################### Search #######################################
Route::post('customer_data', [App\Http\Controllers\SearchController::class,'customer_data']);
########################### Search #######################################


########################### Cache #######################################
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
});



/*
Route::post('users/resetmyUser', [App\Http\Controllers\Core::class,'resetMyPassword']); 
Route::get('/version', [App\Http\Controllers\HomeController::class,'version']); 
Route::get('/line_chart', [App\Http\Controllers\HomeController::class,'line_chart']); 
Route::get('/line_chart_weekly', [App\Http\Controllers\HomeController::class,'line_chart_weekly']); 
Route::get('/line_chart_monthly', [App\Http\Controllers\HomeController::class,'line_chart_monthly']); 
Route::get('/coureLeadCount', [App\Http\Controllers\HomeController::class,'coureLeadCount']); 
*/
