<?php

use Illuminate\Support\Facades\Route;
use Modules\Branch\Http\Controllers\BranchController;

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
 
Route::group(['prefix' => 'branch', 'middleware' => ['auth']], function(){
	
	/**Route::get('/', [BranchController::class, 'index']);
	Route::post('add_branch', [BranchController::class, 'add_branch']);
	Route::post('getdatatable', [BranchController::class, 'get_datatable']);
	Route::post('get_details', [BranchController::class, 'get_details']);
	Route::post('edit_branch', [BranchController::class, 'edit_branch']);
	Route::post('delete_branch', [BranchController::class, 'delete_branch']);
	Route::get('view_branch', [BranchController::class, 'view_branch']); **/
	 
	Route::get('/', [BranchController::class, 'index']);
    Route::post('add_branch', [BranchController::class, 'add_branch']);
    Route::get('/get_state', [BranchController::class, 'get_state']);
	Route::get('/get_district', [BranchController::class, 'get_district']);
    Route::get('/get_city', [BranchController::class, 'get_city']);
    Route::post('/get_datatable', [BranchController::class, 'get_datatable']);
    Route::post('/get_details', [BranchController::class, 'get_details']);
    Route::get('/view_branch', [BranchController::class, 'view_branch']);
    Route::post('/edit_branch', [BranchController::class, 'edit_branch']);
    Route::post('/delete_branch', [BranchController::class, 'delete_branch']);
    Route::post('/getbranch', [BranchController::class, 'get_branch']);
	
});