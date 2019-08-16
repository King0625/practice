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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('user', 'UsersController@index')->middleware('check.user');
Route::get('user', 'UsersController@index')->middleware('check.user');
Route::get('user/{id}', 'UsersController@show')->middleware('check.user');
Route::post('user', 'UsersController@store');
Route::put('user/{id}', 'UsersController@update')->middleware('check.user');
Route::delete('user/{id}', 'UsersController@destroy')->middleware('check.user');