<?php

use Illuminate\Http\Request;

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

/* User */
Route::get('users/', 'User\UserController@index');
Route::get('users/{id}','User\UserController@show');
Route::post('users/', 'User\UserController@store');
Route::put('users/{id}', 'User\UserController@update');
Route::delete('users/{id}', 'User\UserController@destroy');
Route::get('users/verify/{token}', 'User\UserController@verify');
Route::get('users/token/{token}','User\UserController@show_from_token');
Route::post('users/forgot_password', 'User\UserController@forgotPassword');
Route::post('users/reset_password', 'User\UserController@resetPassword');
Route::post('users/login', 'User\UserController@login');
