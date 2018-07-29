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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// デフォルトのログイン直後画面 You are logged in!
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin_users_edit', 'AdminController@index');
