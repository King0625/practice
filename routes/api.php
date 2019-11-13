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


Route::post('register', 'UsersController@register');
Route::post('login', 'UsersController@login');
Route::post('logout', 'UsersController@logout')->middleware('check.user');
Route::get('users', 'UsersController@index')->middleware('check.user');
Route::get('users/{id}', 'UsersController@show')->middleware('check.user');
Route::put('users/{id}', 'UsersController@update')->middleware('check.user');
Route::delete('users/{id}', 'UsersController@destroy')->middleware('check.user');

Route::get('profiles/{id}', 'ProfileController@show');
Route::put('profiles/{id}', 'ProfileController@update')->middleware('check.user');

Route::get('products/search', 'SearchController@searchProducts');

Route::get('products' , 'ProductsController@index');
Route::get('products/{id}' , 'ProductsController@show');
Route::get('users/{user_id}/products', 'ProductsController@userProducts');
Route::post('products', 'ProductsController@store')->middleware('check.user');
Route::put('products/{id}', 'ProductsController@update')->middleware('check.user');
Route::delete('products/{id}', 'ProductsController@destroy')->middleware('check.user');


Route::get('products/{product}/rating', 'RatingController@productRatings');
Route::post('products/{product}/rating', 'RatingController@rateProduct')->middleware('check.user');

Route::get('transactions/{user}/history', 'TransactionController@index')->middleware('check.user');
Route::post('transactions/{product}', 'TransactionController@store')->middleware('check.user');

Route::get('users/{user}/wishlists', 'WishListController@index')->middleware('check.user');
Route::post('users/wishlists/products/{product}', 'WishListController@store')->middleware('check.user');
Route::delete('users/{user}/wishlists/{wishlist}', 'WishListController@destroy')->middleware('check.user');

Route::get('categories', 'CategoryController@index');
Route::get('categories/{id}/products', 'CategoryController@productIndex');
Route::post('categories', 'CategoryController@store')->middleware('check.user');
