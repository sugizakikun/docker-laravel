<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect(route('login'));
});

Auth::routes();

Route::get("/register", "App\Http\Controllers\Auth\RegisterController@showRegistrationForm")->name('auth.register_form');
Route::post("/register", "App\Http\Controllers\Auth\RegisterController@register")->name('auth.register');

Route::get('/home', "App\Http\Controllers\HomeController@index")->name('home');

Route::get('/profile', "App\Http\Controllers\ProfileController@index")->name('profile');
Route::put('/profile', "App\Http\Controllers\ProfileController@update")->name('profile.edit');
Route::delete('/profile', "App\Http\Controllers\ProfileController@destroy")->name('profile.delete');

Route::delete('/withdraw', "App\Http\Controllers\WithDrawController@destroy")->name('withdraw');
Route::get('/withdraw/completed', "App\Http\Controllers\WithDrawController@completed")->name('withdraw.completed');
