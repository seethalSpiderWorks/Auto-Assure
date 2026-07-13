<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\UsersController;

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

//Route::group([], function () {
    //Route::resource('users', UsersController::class)->names('users');
//});


Route::group(['prefix' => 'users', 'middleware' => ['auth']], function(){
	Route::get('/',[UsersController::class,'index']);
    Route::post('getuserDatatable',[UsersController::class,'getuserDatatable']);
	Route::get('user_view',[UsersController::class,'user_view']);
	Route::post('add_users',[UsersController::class,'add_users']);
	Route::post('getUsers',[UsersController::class,'get_user']); 	 
	Route::post('edit_user',[UsersController::class,'editUserAction']); 	  
	Route::post('deleteUser',[UsersController::class,'deleteUserAction']); 
	Route::post('get_branch',[UsersController::class,'get_allbranch']);     
	Route::get('/profile/{id}',[UsersController::class,'profile1']);
	
	Route::get('/dashboard',[UsersDashboardController::class,'index']);
	
	Route::post('/resetpswd', [UsersController::class,'resetMyPassword_user']); 
	Route::post('getActivity',[UsersController::class,'getActivityDatatable']);
	
});


