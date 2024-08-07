<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {

    Route::get('/current_user', function () {
      return Auth::user();
    })->name('currentUser');
  
    Route::namespace('Auth')->group(function() {
      Route::post('/register', 'RegisterController@register')->name('register');
      Route::post('/login', 'LoginController@login')->name('login');
  
      Route::middleware('auth')->group(function() {
        Route::post('/logout', 'LoginController@logout')->name('logout');
      });
    });
  });
