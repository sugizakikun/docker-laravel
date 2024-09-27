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

Route::get('/auth/google', "App\Http\Controllers\Auth\GoogleAuthController@redirectToGoogle")->name('auth.google');
Route::get('/auth/google/callback', "App\Http\Controllers\Auth\GoogleAuthController@handleOAuthCallback")->name('auth.google.callback');

Route::get('/home', "App\Http\Controllers\HomeController@index")->name('home');

Route::group(['namespace' => 'App\Http\Controllers\Post'], function () {
    Route::post('/posts', "PostController@create")->name('post.create');
    Route::put('/posts/{postId}', "PostController@update")->name('post.edit');
    Route::delete('/posts/{postId}', "PostController@destroy")->name('post.destroy');
});

Route::group(['namespace' => 'App\Http\Controllers\Profile'], function () {
    Route::get('/profile', "ProfileController@index")->name('profile');
    Route::put('/profile', "ProfileController@update")->name('profile.edit');
    Route::delete('/profile', "ProfileController@destroy")->name('profile.destroy');

    Route::put('/profile_image', "ProfileImageController@update")->name('profile_image.edit');
    Route::delete('/profile_image', "ProfileImageController@destroy")->name('profile_image.delete');
});

Route::group(['namespace' => 'App\Http\Controllers\User'], function () {
    Route::get('/user/{userId}', "UserController@show")->name('user.show');

    Route::put('/user/{userId}/follow', "FollowController@create")->name('follow');
    Route::delete('/user/{userId}/follow', "FollowController@destroy")->name('unfollow');
});
