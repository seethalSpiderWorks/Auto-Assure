<?php

use Illuminate\Support\Facades\Route;
use Modules\Registration\Http\Controllers\RegistrationController;

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
    Route::resource('registration', RegistrationController::class)->names('registration');
});*/

Route::group(['prefix' => 'registration', 'middleware' => ['auth']], function()
{ 
    Route::get('/', [RegistrationController::class,'viewIndex']);
    Route::post('datatable', [RegistrationController::class,'datatable'])->name('datatable');
    Route::get('delete', [RegistrationController::class,'deleteRegistration']);
});