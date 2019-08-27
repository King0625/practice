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
Route::get('user', 'UsersController@index')->middleware('check.user');
Route::get('user/{id}', 'UsersController@show')->middleware('check.user');
Route::put('user/{id}', 'UsersController@update')->middleware('check.user');
Route::delete('user/{id}', 'UsersController@destroy')->middleware('check.user');

Route::get('product/search/', 'SearchController@searchProducts');

Route::get('product' , 'ProductsController@index');
Route::get('product/{id}' , 'ProductsController@show');
Route::get('user/{user}/product', 'ProductsController@userProducts');
Route::post('product', 'ProductsController@store')->middleware('check.user');
Route::put('product/{id}', 'ProductsController@update')->middleware('check.user');
Route::delete('product/{id}', 'ProductsController@destory')->middleware('check.user');


Route::get('product/{product}/rating', 'RatingController@productRatings');
Route::post('product/{product}/rating', 'RatingController@rateProduct')->middleware('check.user');

Route::get('transaction/{user}/history', 'TransactionController@index')->middleware('check.user');
Route::post('transaction/{product}', 'TransactionController@store')->middleware('check.user');