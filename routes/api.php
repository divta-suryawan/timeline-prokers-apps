<?php

use App\Http\Controllers\API\LeadershipController;
use App\Http\Controllers\API\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->controller(LeadershipController::class)->group(function () {
    Route::get('/leadership', 'getAllData');
    Route::post('/leadership/create', 'createData');
    Route::get('/leadership/get/{id}', 'getDataById');
    Route::post('/leadership/update/{id}', 'updateDataById');
    Route::delete('/leadership/delete/{id}', 'deleteDataById');
});

Route::prefix('v2')->controller(UsersController::class)->group(function () {
    Route::get('/users', 'getAllData');
    Route::post('/users/create', 'createData');
    Route::get('/users/get/{id}', 'getDataById');
    Route::post('/users/update/{id}', 'updateDataById');
    Route::delete('/users/delete/{id}', 'deleteDataById');
});
