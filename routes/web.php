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

// 管理画面 認証済みuserのみ表示
Route::get('/admin_users_edit', 'AdminUserController@index')->middleware('auth');
Route::get('/admin_mac_address', 'AdminMacAddressController@index')->middleware('auth');
Route::post('/admin_mac_address/update', 'AdminMacAddressController@update')->middleware('auth');
Route::get('/admin_mac_address/edit{id?}', 'AdminMacAddressController@edit')->middleware('auth');

// 外部からのPOST受け取り先 csrf off
Route::post('/inport_post/mac_address', 'InportPostController@MacAddress');
