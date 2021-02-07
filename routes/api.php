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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Auth'], function () {
    Route::post('/login', 'LoginController@login')->name('login');
    Route::post('/register', 'RegisterController@register');
    Route::post('/logout', 'LoginController@logout')->middleware('auth:sanctum');
});
Route::resource('products', 'ProductController');

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::resource('customers', 'CustomerController');
    Route::post('orders/{id}/change-status', 'OrderController@changeStatus');
    Route::post('orders/{id}/add-note', 'OrderController@addNote');
    Route::resource('orders', 'OrderController');
});