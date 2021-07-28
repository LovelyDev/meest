<?php

use App\User;
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

//Route::group(['prefix' => 'auth'], function () {
//  Route::post('login', 'Auth\AuthController@login')->name('login');
//  Route::post('register', 'Auth\AuthController@register');
//  Route::group(['middleware' => 'auth:api'], function () {
//    Route::get('logout', 'Auth\AuthController@logout');
//    Route::get('user', 'Auth\AuthController@user');
//  });
//});
Route::get('settings', function () {
  return \App\Setting::first();
});

Route::get('get-token', function () {
  return response()->json(session('chat_user_token'));
});

Route::group(['prefix' => 'auth'], function () {
  Route::post('login', 'AuthController@login');
  Route::post('signup', 'AuthController@signup');

  Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
    Route::post('users/{id}', 'AuthController@update');
  });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:api')->prefix('room')->group(function () {
  Route::get('types', 'RoomController@getRoomTypes');
  Route::get('accesses', 'RoomController@getRoomAccesses');
  Route::get('youtube-accesses', 'RoomController@getRoomYoutubeAccesses');
});

Route::middleware('auth:api')->prefix('rooms')->group(function () {
  Route::get('', 'RoomController@getAll');
  Route::get('{id}', 'RoomController@findById');
  Route::post('', 'RoomController@save');
  Route::put('{id}', 'RoomController@save');
  Route::delete('{id}', 'RoomController@delete');
  Route::post('{id}/tenants', 'RoomController@addTenant');
  Route::delete('{id}/tenants/{aliasId}', 'RoomController@deleteTenant');
  Route::delete('tenants/{aliasId}', 'RoomController@deleteTenantInRooms');
  Route::get('{id}/users', 'RoomController@getUsersByRoomId');
  Route::get('users/{id}/host', 'RoomController@getRoomsCreatedByUser');
});

/**
 * Routes for alias
 */
Route::middleware('auth:api')->prefix('alias')->group(function () {
    Route::get('{id}', 'AliasController@findById');
    Route::get('', 'AliasController@find');
    Route::post('', 'AliasController@create');
    Route::put('{id}', 'AliasController@update');
    Route::delete('{id}', 'AliasController@delete');
});

Route::middleware('auth:api')->prefix('avatar')->group(function () {
    Route::get('defaults', 'AvatarDefaultController@getDefaults');
});

//Route::prefix('rooms')->group(function () {
//  Route::get('', 'RoomController@getAll');
//  Route::get('{id}', 'RoomController@findById');
//  Route::post('', 'RoomController@save');
//  Route::put('{id}', 'RoomController@save');
//  Route::delete('{id}', 'RoomController@delete');
//  Route::post('{id}/tenants', 'RoomController@addTenant');
//  Route::delete('{id}/tenants/{userId}', 'RoomController@deleteTenant');
//  Route::get('{id}/users', 'RoomController@getUsersByRoomId');
//});


