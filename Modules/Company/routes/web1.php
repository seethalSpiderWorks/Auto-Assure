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
	Route::get('/', [CompanyController::class, 'indexAction']);
 
    Route::post('getDatatable',[CompanyController::class,'getAllDivisionAction']);
    Route::post('addcompany', [CompanyController::class, 'addCompanyAction']);   
    Route::post('getDivisions',[CompanyController::class, 'getDivisionAction']);   
    Route::post('editDivision',[CompanyController::class, 'editDivisionAction']);   
    Route::post('deleteDivision',[CompanyController::class, 'deleteDivisionAction']);  
    Route::post('getEnqueryData',[CompanyController::class, 'getEnqueryDataAction']);   
    Route::get('get_city',[CompanyController::class, 'get_city']);   
    Route::post('Search_product',[CompanyController::class, 'Search_product']);  
    Route::post('Search_product_each_branch_stock',[CompanyController::class, 'Search_product_each_branch_stock']);  
});
