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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::resource('route', 'API\UserController')->middleware('auth:api');

// Route::get('/getroute', 'API\RouteController@getTodayRoute')->middleware('auth:api');
// Route::put('/updateroute/{id}', 'API\RouteController@updateTodayRoute')->middleware('auth:api');

Route::post('/hotelregister', 'API\HotelController@register');
Route::post('/userregister', 'UserController@register');
Route::post('/hotellogin', 'API\HotelController@login');
Route::post('/login', 'UserController@login');

Route::get('/logout', 'UserController@logout')->middleware('auth:api');



Route::prefix('/')->group(function () {
    Route::resource('/hotel', 'API\HotelController')->middleware('auth:api');
});


Route::post('/hotelupdate', 'API\HotelController@hotelupdate')->middleware('auth:api');
Route::post('/hotelprofile', 'API\HotelController@profile')->middleware('auth:api');
Route::post('/hotelchangepassword', 'API\HotelController@changePass')->middleware('auth:api');





Route::prefix('/')->group(function () {
    Route::resource('/user', 'APIUSER\UserController')->middleware('auth:api');
});




Route::get('/test', 'UserController@test')->middleware('auth:api');
Route::get('/copy', 'UserController@copy');
