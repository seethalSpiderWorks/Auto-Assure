<?php

use Illuminate\Support\Facades\Route;
use Modules\Rules\Http\Controllers\RulesController;

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

Route::group(['prefix'=>'rules','middleware' => ['auth']], function () {
   // Route::resource('rules', RulesController::class)->names('rules');
	
	Route::get('/', [RulesController::class, 'indexAction']);
	Route::post('/loadresources', [RulesController::class, 'getAllrulesAction']);
	Route::post('assignSubmenus', [RulesController::class, 'assignSubmenus']);
	Route::post('assignOptions', [RulesController::class, 'assignOptions']);
	Route::post('assignallOptions', [RulesController::class, 'assignallOptions']);
	
});
