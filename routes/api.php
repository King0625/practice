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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register', 'UsersController@store');
Route::post('login', 'UsersController@login');

Route::apiResource('user', 'UsersController', ['except' => ['store']])->middleware('check.user');

Route::get('product' , 'ProductsController@index');
Route::get('product/{id}' , 'ProductsController@show');
Route::get('{user}/product', 'ProductsController@userProducts');

Route::get('product/{product}/rating', 'RatingController@productRatings');
Route::post('product/{product}/rating', 'RatingController@rateProduct')->middleware('check.user');

Route::get('transaction/{user}/history', 'TransactionController@index')->middleware('check.user');
Route::post('transaction/{product}', 'TransactionController@store')->middleware('check.user');