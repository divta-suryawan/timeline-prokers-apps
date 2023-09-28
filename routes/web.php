<?php

use App\Http\Controllers\API\LeadershipController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ProkersController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

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


Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('Auth.login');
    })->name('login');

    Route::post('/loginproses', [AuthController::class, 'login']);
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard');
    });
    Route::get('/users', function () {
        return view('pages.users');
    })->middleware('role:admin');
    Route::get('/leadership', function () {
        return view('pages.leadership');
    });
    Route::get('/prokers', function () {
        return view('pages.prokers');
    });
    Route::get('/prokers/pending', function () {
        return view('pages.details.pending');
    });
    Route::get('/prokers/onprogress', function () {
        return view('pages.details.on-progress');
    });
    Route::get('/prokers/finish', function () {
        return view('pages.details.finish');
    });
    Route::get('/prokers/notfinish', function () {
        return view('pages.details.not-finish');
    });
    Route::get('/prokers/byleadership/{id}', function ($id) {
        return view('pages.prokersByPeriode');
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

    Route::prefix('v3')->controller(ProkersController::class)->group(function () {
        Route::get('/prokers', 'getAllData');
        Route::post('/prokers/create', 'createData');
        Route::get('/prokers/get/{id}', 'getDataById');
        Route::post('/prokers/update/{id}', 'updateDataById');
        Route::delete('/prokers/delete/{id}', 'deleteDataById');
        Route::post('/prokers/start/{id}', 'startProker');
        Route::post('/prokers/finish/{id}', 'finishProker');
        Route::post('/prokers/notfinish/{id}', 'markProkerNotFinished');
        Route::get('/prokers/detail/{status}', 'detail');
        Route::get('/prokers/byleadership/{id}', 'getDataByLeadership');
    });


    Route::post('/logout', [AuthController::class, 'logout']);
});
