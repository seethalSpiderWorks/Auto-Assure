<?php

use Illuminate\Support\Facades\Route;
use Modules\InspectionReport\Http\Controllers\InspectionReportController;
use Modules\InspectionReport\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::group([], function () {
    Route::resource('inspectionreport', InspectionReportController::class)->names('inspectionreport');
}); */
  
Route::group(['prefix' => 'inspectionreport','middleware' => 'auth'], function () 
{
	Route::get('/',[InspectionReportController::class,'index']);	
	Route::post('addGeneralInfo',[InspectionReportController::class,'addGeneralInfo']);    // Add Basic info
	Route::post('addVehicleInfo',[InspectionReportController::class,'add_vehicleInfo']);   
	Route::post('addAdditionalSpec',[InspectionReportController::class,'add_additionalSpec']);
	Route::post('addWarrantyServices',[InspectionReportController::class,'add_warrantyServices']);
	Route::post('addInspectionSummary',[InspectionReportController::class,'add_inspectionSummary']);
	Route::post('addVehicleSpecification',[InspectionReportController::class,'add_vehicleSpecification']);
	Route::post('addInspectionChecklist',[InspectionReportController::class,'add_inspectionChecklist']);
	Route::post('addInspectionGallery',[InspectionReportController::class,'add_inspectionGallery']);
	Route::post('addDamages',[InspectionReportController::class,'addDamages']);   
	Route::post('addReportsfile',[InspectionReportController::class,'addReportsfile']);
	Route::post('addVehicleOverview',[InspectionReportController::class,'addVehicleOverview']); // Vehicle Overview
	
	Route::post('viewInspectionReport',[InspectionReportController::class,'viewInspectionReport']);   // View Report
	Route::post('getDivisions',[InspectionReportController::class,'getDivisionAction']);              // get data
	Route::post('deleteInspectionReport',[InspectionReportController::class,'deleteInspectionReport']);  // Delete report
	Route::post('getDatatable',[InspectionReportController::class,'getDatatable']);  // Report datatable
	
	Route::post('getstatus',[InspectionReportController::class,'getStatusAction']); // Get Status
	// 10/06/2025
	Route::get('SummaryDes',[InspectionReportController::class,'SummaryDes'])->name('SummaryDes'); // SummaryDes

});
 
########################## View Inspection Reports  ##########################
Route::group(['prefix' => 'viewInspectionreports','middleware' => 'auth'], function () 
{
	Route::get('/',[InspectionReportController::class,'viewReportsIndex']);   // View all reports 
	Route::post('viewReportDatatable',[InspectionReportController::class,'viewReportDatatable']); 
});	
######################## View Inspection Reports End ########################
 