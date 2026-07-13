<?php

use Illuminate\Support\Facades\Route;
use Modules\Manage\Http\Controllers\ManageController;
use Modules\Manage\Http\Controllers\MakeController;
use Modules\Manage\Http\Controllers\ModelController;
use Modules\Manage\Http\Controllers\InteriorColorController;
use Modules\Manage\Http\Controllers\ExteriorColorController;
use Modules\Manage\Http\Controllers\SummaryDescController;

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
    Route::resource('manage', ManageController::class)->names('manage');
});*/

Route::group(['prefix' => 'manage','middleware' => 'auth'], function () 
{
	
});
 
Route::group(['prefix' => 'make','middleware' => 'auth'], function () 
{
	Route::get('/', [MakeController::class,'index']);
    Route::post('add',[MakeController::class,'createAction']);
    Route::post('get-list',[MakeController::class,'datatable']);
    Route::post('getDivisions',[MakeController::class,'getDivisionAction']);
    Route::post('editDivision',[MakeController::class,'editDivisionAction']);  
    Route::post('deleteDivision',[MakeController::class,'deleteDivisionAction']);  
    Route::post('clientsstatus',[MakeController::class,'status']);
});

Route::group(['prefix' => 'model','middleware' => 'auth'], function () 
{	
	Route::get('/', [ModelController::class,'index']);
    Route::post('add_model',[ModelController::class,'createAction']);
    Route::post('get-list_model',[ModelController::class,'datatable']);
    Route::post('getDivisions_model',[ModelController::class,'getDivisionAction']);
    Route::post('editDivision_model',[ModelController::class,'editDivisionAction']);  
    Route::post('deleteDivision_model',[ModelController::class,'deleteDivisionAction']);  
    Route::post('status_model',[ModelController::class,'status']) ;
});
 
Route::group(['prefix' => 'interiorColor','middleware' => 'auth'], function () 
{
	Route::get('/', [InteriorColorController::class,'index']);
    Route::post('add',[InteriorColorController::class,'createAction']);
    Route::post('get-list',[InteriorColorController::class,'datatable']);
    Route::post('getDivisions',[InteriorColorController::class,'getDivisionAction']);
    Route::post('editDivision',[InteriorColorController::class,'editDivisionAction']);  
    Route::post('deleteDivision',[InteriorColorController::class,'deleteDivisionAction']);  
    Route::post('status',[InteriorColorController::class,'status']);
});

Route::group(['prefix' => 'exteriorColor','middleware' => 'auth'], function () 
{
	Route::get('/', [ExteriorColorController::class,'index']);
    Route::post('add',[ExteriorColorController::class,'createAction']);
    Route::post('get-list',[ExteriorColorController::class,'datatable']);
    Route::post('getDivisions',[ExteriorColorController::class,'getDivisionAction']);
    Route::post('editDivision',[ExteriorColorController::class,'editDivisionAction']);  
    Route::post('deleteDivision',[ExteriorColorController::class,'deleteDivisionAction']);  
    Route::post('status',[ExteriorColorController::class,'status']);
});

Route::group(['prefix' => 'summary_description','middleware' => 'auth'], function () 
{	
	Route::get('/', [SummaryDescController::class,'index']);
    Route::post('add_SummaryDesc',[SummaryDescController::class,'createAction']);
    Route::post('get-list_SummaryDesc',[SummaryDescController::class,'datatable']);
    Route::post('getDivisions_SummaryDesc',[SummaryDescController::class,'getDivisionAction']);
    Route::post('editDivision_SummaryDesc',[SummaryDescController::class,'editDivisionAction']);  
    Route::post('deleteDivision_SummaryDesc',[SummaryDescController::class,'deleteDivisionAction']);  
});
