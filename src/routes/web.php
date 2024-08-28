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

Route::group(['namespace' => 'App\Http\Controllers\Auth'], function () {
    Route::get("/register", "RegisterController@showRegistrationForm")->name('auth.register_form');
    Route::post("/register", "RegisterController@register")->name('auth.register');
});

Route::get('/home', "App\Http\Controllers\HomeController@index")->name('home');

Route::group(['namespace' => 'App\Http\Controllers\Profile'], function () {
    Route::get('/profile', "ProfileController@index")->name('profile');

    Route::put('/profile_image', "ProfileImageController@update")->name('profile_image.edit');
    Route::delete('/profile_image', "ProfileImageController@destroy")->name('profile_image.delete');
});

Route::delete('/withdraw', "App\Http\Controllers\WithDrawController@destroy")->name('withdraw');
Route::get('/withdraw/completed', "App\Http\Controllers\WithDrawController@completed")->name('withdraw.completed');
