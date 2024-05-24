<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::get('login', 'App\Http\Controllers\AuthController@login')->name('login');
Route::post('logout', 'App\Http\Controllers\AuthController@logout');


Route::get('profile', 'App\Http\Controllers\ProfileController@getProfile');

Route::middleware('is_admin')->group(function () {

    Route::get('allUser', 'App\Http\Controllers\UserController@AllUser');
    Route::get('oneUser/{id}', 'App\Http\Controllers\UserController@oneUser');
    Route::post('updateUser', 'App\Http\Controllers\UserController@update');
    Route::delete('deleteUser/{id}', 'App\Http\Controllers\UserController@destroy');
});




Route::middleware('usercheck')->group(function () {
    Route::get('posts', 'App\Http\Controllers\PostController@allPosts');
    Route::get('onePost/{id}', 'App\Http\Controllers\PostController@OnePost');
    Route::post('createPost', 'App\Http\Controllers\PostController@createPost');
    Route::post('updatePost', 'App\Http\Controllers\PostController@update');
    Route::delete('deletePost/{id}', 'App\Http\Controllers\PostController@destroy');
});
