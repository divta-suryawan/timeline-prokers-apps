<?php

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

Route::get('/', function () {
    return view('pages.dashboard');
});
Route::get('/users', function () {
    return view('pages.users');
});
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
