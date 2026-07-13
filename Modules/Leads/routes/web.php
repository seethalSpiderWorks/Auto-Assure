<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;
use Modules\Leads\Http\Controllers\FollowupController;

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
/**
Route::group([], function () {
    Route::resource('leads', LeadsController::class)->names('leads');
}); **/

Route::group(['prefix' => 'leads', 'middleware' => ['auth']], function()
{	
	Route::get('/',[LeadsController::class,'index']);	
	Route::get('insert_basic_info',[LeadsController::class,'basicRegistrtaion'])->name('insert_basic_info'); // insert
	Route::post('get-list',[LeadsController::class,'datatable'])->name('get-list'); // datatable
	Route::get('getModel',[LeadsController::class,'getModel'])->name('getModel'); // getModel
	
	Route::get('searchRegistration',[LeadsController::class,'searchRegistration'])->name('searchRegistration');
	Route::get('get_branch',[LeadsController::class,'get_branch']);
	Route::post('getstaff',[LeadsController::class,'getStaffAction']);
	
	Route::get('view/{id}',[LeadsController::class,'viewLead'])->name('view_lead'); // read-only lead detail page
	Route::post('update-lead-inspection',[LeadsController::class,'updateLeadInspection'])->name('update_lead_inspection'); // edit-lead: inspection template/staff/date
	Route::post('add-note',[LeadsController::class,'addNote'])->name('add_note'); // add lead note
	Route::post('update-note',[LeadsController::class,'updateNote'])->name('update_note'); // edit lead note
	Route::post('delete-note',[LeadsController::class,'deleteNote'])->name('delete_note'); // delete lead note
	Route::get('getleadsdata/{id}',[LeadsController::class,'getleadsdata'])->name('getleadsdata'); // edit
	Route::get('update_basic_info',[LeadsController::class,'update_basic_info'])->name('update_basic_info'); // update
	Route::get('delete',[LeadsController::class,'leadDelete'])->name('delete'); // delete
	Route::post('assignenquerydata',[LeadsController::class,'assignEnqueryDataAction']); // Assign
	Route::post('assign_leads',[LeadsController::class,'assignLeadAction'])->name('assign_leads'); // Assign multiple leads
	Route::post('assign_leads_delete',[LeadsController::class,'assignLeadActiondelete']); // Delete multiple leads

	
	Route::get('exportLeads', [LeadsController::class,'export']);
	 
	Route::get('set_lead_session',[LeadsController::class,'set_lead_session'])->name('set_lead_session');
	Route::get('set_lead_session_followtable',[LeadsController::class,'set_lead_session_followtable'])->name('set_lead_session_followtable');
	Route::get('set_lead_session_followtables',[LeadsController::class,'set_lead_session_followtable'])->name('set_lead_session_followtables');
	
	/*************** assisn / reassign *****************/
	Route::get('followup_type_assign',[LeadsController::class,'followup_type_assign']);
	Route::get('followup_type_reassign',[LeadsController::class,'followup_type_reassign']);
	
	/**** followup ****/
	Route::get('/followup',[LeadsController::class,'set_lead_session']);
	Route::post('add_followup',[FollowupController::class,'add_followupAction']);	
	
	
	Route::post('get-followup', [FollowupController::class,'datatableFollowup']);
	
	/************** filter routes **********************/
	Route::post('filter',[LeadsController::class,'filterAction'])->name('filter'); // filter source
	Route::post('setFilterStaff',[LeadsController::class,'setFilterStaff'])->name('setFilterStaff');
    Route::get('setFilterStatus',[LeadsController::class,'setFilterStatus'])->name('setFilterStatus');
	
});

Route::group(['prefix' => 'myleads', 'middleware' => ['auth']], function()
{
    Route::get('/',[LeadsController::class,'view_all_index']);
    Route::post('get-list',[LeadsController::class,'datatable'])->name('get-list');
});

/*************** Leads — duplicate of My Leads ***************/
Route::group(['prefix' => 'leadslist', 'middleware' => ['auth']], function()
{
    Route::get('/',[LeadsController::class,'leadsListIndex']);
    Route::post('get-list',[LeadsController::class,'datatable'])->name('leadslist-get-list');
});

 

Route::group(['prefix' => 'myfollowup', 'middleware' => ['auth']], function()
{
    Route::get('/',[FollowupController::class,'index']);
    Route::post('get-list',[FollowupController::class,'datatable'])->name('get-todaylist');
	Route::post('get-list-all',[FollowupController::class,'datatable_all'])->name('get-todaylist-all');
});