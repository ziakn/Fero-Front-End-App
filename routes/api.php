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
Route::post('/login', 'UserController@login');
Route::post('/register', 'UserController@register');
Route::get('/logout', 'UserController@logout');
Route::prefix('/')->group(function () {
    Route::resource('/route', 'API\RouteController')->middleware('auth:api');
});

Route::get('/test', 'API\RouteController@test')->middleware('auth:api');
Route::get('/copy', 'API\RouteController@copy');
