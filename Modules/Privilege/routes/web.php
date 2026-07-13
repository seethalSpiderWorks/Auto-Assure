<?php

use Illuminate\Support\Facades\Route;
use Modules\Privilege\Http\Controllers\PrivilegeController;

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

Route::group(['prefix' => 'privileges', 'middleware' => ['auth']], function () {
    //Route::resource('privilege', PrivilegeController::class)->names('privilege');
	
	  Route::get('/', [PrivilegeController::class, 'indexAction']);
	  Route::post('addprivilege', [PrivilegeController::class, 'addPrivilegeAction']);
	  Route::post('editPrivilege', [PrivilegeController::class, 'editPrivilegeAction']);
	  Route::post('getPrivilege', [PrivilegeController::class, 'getPrivilegeAction']);
	  Route::post('deletePrivilege', [PrivilegeController::class, 'deletePrivilegeAction']);
	  Route::post('getDatatable', [PrivilegeController::class, 'getAllPrivilegeAction']);
	  Route::post('assignPrivilege', [PrivilegeController::class, 'assignPrivilege']);

});

Route::group(['prefix' => 'menuprivilege', 'middleware' => ['auth']], function () {
	
	Route::post('/', [PrivilegeController::class, 'menuIndexAction']);
	Route::post('getPrivilege', [PrivilegeController::class, 'getAllPrivilege']);
	Route::post('getmenuprivilege', [PrivilegeController::class, 'getmenuprivilege']);
	Route::post('updateRole', [PrivilegeController::class, 'updateMenuPrivilegeAction']);
	
});

