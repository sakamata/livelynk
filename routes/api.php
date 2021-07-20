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

// local test環境のみ有効
if (app()->isLocal() || app()->runningUnitTests()) {
    // 天気API動作確認用route postman で確認可能だが通常はコメントアウト
    Route::get('/weather', 'API\WeatherCheckController@run');
    Route::get('/test', 'TaskController@taskDepartureCheck');
}

Route::post('stay_info/community/{communityId}', 'API\StayInfo\MailFetchController@post')->where('communityId', '[0-9]+');
