<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/', function () {
  if (!Auth::check()) {
    return view('auth.login');
  }

  return view('chat.index');

})->middleware(['shield', 'remove.token.param']);


Route::prefix('rooms')->group(function () {
  Route::get('{id}', 'RoomController@gotoRoom');
});

Route::get('admin/server-stats', function () {
  return view('admin.stats');
});
