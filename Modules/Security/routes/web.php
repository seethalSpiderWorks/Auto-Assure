<?php

use Illuminate\Support\Facades\Route;
use Modules\Security\Http\Controllers\SecurityController;
use Modules\Security\Http\Controllers\UserLogController;

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

Route::prefix('blockip')->group(function() {
    Route::get('/',[SecurityController::class,'indexAction']);
    Route::post('/getDatatable',[SecurityController::class,'getAllBlockedipsAction']);
    Route::post('/unblockIp',[SecurityController::class,'unblockIpAction']);
});

Route::prefix('blockuser')->group(function() {
    Route::get('/',[SecurityController::class,'indexUserAction']);
    Route::post('/getDatatable',[SecurityController::class,'getAllBlockedUsersAction']);
    //Route::post('/statusChange',[SecurityController::class,'statusChangeAction']);
	Route::post('/block_orunblock',[SecurityController::class,'statusChangeAction']);
	
});

Route::prefix('userlog')->group(function() {
    Route::get('/',[UserLogController::class,'indexAction']);
    Route::post('/getDatatable',[UserLogController::class,'getAllBranchAction']);
	Route::post('filter',[UserLogController::class,'filterAction'])->name('filter');
});
