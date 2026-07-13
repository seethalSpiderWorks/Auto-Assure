<?php
use Modules\Company\Http\Controllers\CompanyController;
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

Route::group(['prefix' => 'company', 'middleware' => ['auth']], function()
{
	Route::get('/', [CompanyController::class, 'index']);
 
    Route::get('/',[CompanyController::class, 'index']);
    Route::post('add_company', [CompanyController::class, 'add_company']);
    Route::post('edit_company', [CompanyController::class, 'edit_company']);
    Route::get('/get_state', [CompanyController::class, 'get_state']);
	Route::get('/get_district', [CompanyController::class, 'get_district']);
    //Route::get('/get_city', [CompanyController::class, 'get_city']);
    Route::post('/get_datatable', [CompanyController::class, 'get_datatable']);
    Route::post('/get_details', [CompanyController::class, 'get_details']);
    Route::get('/view_company', [CompanyController::class, 'view_company']);
    Route::post('/edit_company', [CompanyController::class, 'edit_company']);
    Route::post('/delete_company', [CompanyController::class, 'delete_company']);
    Route::post('/setbranch', [CompanyController::class, 'setBranch']);
	
	Route::post('/set_branch', [CompanyController::class,'set_branch']);
});
 