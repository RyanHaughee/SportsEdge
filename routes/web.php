<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/schedule/all', [ScheduleController::class, 'fetchAll']);
Route::get('/filter/get/{id?}', [FilterController::class, 'get']);
Route::post('/save/filter', [FilterController::class, 'save']);