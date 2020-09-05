<?php

namespace Tests\Unit\app\Service\API;

use App\Community;
use App\Router;
use \Artisan;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeatherCheckServiceTest extends TestCase
{
    use RefreshDatabase;
    protected static $db_inited = false;

    protected static function initDB()
    {
        // このクラスのTest初回に設定するものがあれば記述
    }

    public function setUp(): void
    {
        parent::setUp();

        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }
    }

    /**
     * 雨数値の dataProvider 単なる配列としても使用する
     * @param void
     * @return array
     */
    public function rainDummyPaternArray()
    {
        return [
            'sunny' => [
                0 => 0.00,
                1 => 0.00,
                2 => 0.00,
                3 => 0.00,
                4 => 0.00,
                5 => 0.00,
                6 => 0.00,
            ],
            'maybeRainy' => [
                0 => 0.00,
                1 => 0.00,
                2 => 0.00,
                3 => 0.00,
                4 => 5.00,
                5 => 5.00,
                6 => 5.00,
            ],
            'nowRain' => [
                0 => 1.00,
                1 => 1.00,
                2 => 1.00,
                3 => 1.00,
                4 => 1.00,
                5 => 1.00,
                6 => 1.00,
            ],
            'maybeSunny' => [ // not use
                0 => 4.00,
                1 => 3.00,
                2 => 2.00,
                3 => 1.00,
                4 => 0.00,
                5 => 0.00,
                6 => 0.00,
            ]
        ];
    }

    /**
     * 雨状態のダミー
     *
     * @param array $rain rainDummyPaternArray() の返却値を使用
     * @return array
     */
    public function rainyResponseJsonDummy(array $rain)
    {
        return [
            "ResultInfo" => [
                "Count" => 1,
                "Total" => 1,
                "Start" => 1,
                "Status" => 200,
                "Latency" => 0.010482,
                "Description" => "",
                "Copyright" => "(C) Yahoo Japan Corporation.",
            ],
            "Feature" =>  [
                0 =>  [
                    "Id" => "202003291230_139.71302_35.645414",
                    "Name" => "地点(139.71302,35.645414)の2020年03月29日 12時30分から60分間の天気情報",
                    "Geometry" =>  [
                        "Type" => "point",
                        "Coordinates" => "139.71302,35.645414",
                    ],
                    "Property" => [
                        "WeatherAreaCode" => 4410,
                        "WeatherList" =>  [
                            "Weather" =>  [
                                0 => [
                                    "Type" => "observation",
                                    "Date" => "202003291230",
                                    "Rainfall" => $rain[0],
                                ],
                                1 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291240",
                                    "Rainfall" => $rain[1],
                                ],
                                2 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291250",
                                    "Rainfall" => $rain[2],
                                ],
                                3 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291300",
                                    "Rainfall" =>$rain[3],
                                ],
                                4 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291310",
                                    "Rainfall" => $rain[4],
                                ],
                                5 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291320",
                                    "Rainfall" => $rain[5],
                                ],
                                6 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291330",
                                    "Rainfall" => $rain[6],
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * 降雨量のダミーで返却値と処理の確認をTest
     * 晴れ状態が更新されるか
     * @test
     * @return void
     *
     */
    public function whatherAPI晴れ状態の更新Test()
    {
        $targetDt = Carbon::now();
        Carbon::setTestNow($targetDt);

        $subDay = Carbon::now()->subDay()->toDateTimeString();
        $subMinute = Carbon::now()->subMinutes(10)->toDateTimeString();

        // 昨日から晴れの状態を生成
        factory(Community::class)->create([
            'google_home_weather_enable'    => 1,
            'last_maybe_rainy_datetime' => $subDay,
            'last_rainy_datetime'       => $subDay,
            'last_sunny_datetime'       => $subMinute,
            'last_rain_stop_info_datetime'  => $subDay,
        ]);
        factory(Router::class)->create([
            'community_id'    => 1,
        ]);

        // 降雨量の配列を取得
        $rainfall = $this->rainDummyPaternArray();
        // メソッド実行用のパラメーター「晴れ」を生成
        $resArr = [
            [
                'communityId'   => 1,
                'body'          => $this->rainyResponseJsonDummy($rainfall['sunny']),
            ]
        ];

        // メソッド実行
        $service = app()->make('\App\Service\API\WeatherCheckService');
        $res = $service->responseRainJudging($resArr);
        // 晴れ更新のstatusメッセージが帰っているか?
        $this->assertEquals($res[0]['updateStatus'], "**晴れ確認時間の更新**");
        // 通知は存在しない
        $this->assertFalse(isset($res[0]['result']));
        // 晴れ時間が現在に更新されているか
        $this->assertDatabaseHas('communities', [
            'id'                        =>1,
            'last_maybe_rainy_datetime' => $subDay,
            'last_rainy_datetime'       => $subDay,
            'last_sunny_datetime'       => Carbon::now(),
        ]);
    }

    /**
     * 降雨量のダミーで返却値と処理の確認をTest
     * 雨通知発生と雨の状態が更新されるか
     * @test
     * @return void
     *
     */
    public function whatherAPI雨予報のステータス変更Test()
    {
        $targetDt = Carbon::now();
        Carbon::setTestNow($targetDt);

        $subDay = Carbon::now()->subDay()->toDateTimeString();
        $subMinute = Carbon::now()->subMinutes(10)->toDateTimeString();

        // 昨日から晴れの状態を生成
        factory(Community::class)->create([
            'google_home_weather_enable'    => 1,
            'last_maybe_rainy_datetime'     => $subDay,
            'last_rainy_datetime'           => $subDay,
            'last_sunny_datetime'           => $subMinute,
            'last_rain_stop_info_datetime'  => $subDay,
        ]);

        factory(Router::class)->create([
            'community_id'    => 1,
        ]);


        // 降雨量の配列を取得
        $rainfall = $this->rainDummyPaternArray();

        // メソッド実行用のパラメーター「雨予報」を生成
        $resArr = [
            [
                'communityId'   => 1,
                'body'          => $this->rainyResponseJsonDummy($rainfall['maybeRainy']),
            ]
        ];

        // メソッド実行
        $service = app()->make('\App\Service\API\WeatherCheckService');
        $res = $service->responseRainJudging($resArr);
        // 雨予報通知処のresultメッセージが帰っているか?
        $this->assertEquals($res[0]['updateStatus'], "**雨予報あり時間の更新**");
        $this->assertEquals($res[0]['result'], "**雨予報あり通知**");
        // 雨予報の状態が更新されているか
        $this->assertDatabaseHas('communities', [
            'id'                        =>1,
            'last_maybe_rainy_datetime' => Carbon::now()->toDateTimeString(),
            'last_rainy_datetime'       => $subDay,
            'last_sunny_datetime'       => $subMinute,
        ]);
    }

    /**
     * 降雨量のダミーで返却値と処理の確認をTest
     * 雨が降って来た状態のステータス変更と通知が行われるか
     * @test
     * @return void
     *
     */
    public function whatherAPI雨降り通知とステータス変更Test()
    {
        $targetDt = Carbon::now();
        Carbon::setTestNow($targetDt);

        $subDay     = Carbon::now()->subDay()->toDateTimeString();
        $subMinute  = Carbon::now()->subMinutes(10)->toDateTimeString();
        $subHour    = Carbon::now()->subHour()->toDateTimeString();

        // 雨予報の状態を生成
        factory(Community::class)->create([
            'google_home_weather_enable'    => 1,
            'last_maybe_rainy_datetime'     => $subMinute,
            'last_rainy_datetime'           => $subDay,
            'last_sunny_datetime'           => $subHour,
            'last_rain_stop_info_datetime'  => $subDay,
        ]);

        factory(Router::class)->create([
            'community_id'    => 1,
        ]);


        // 降雨量の配列を取得
        $rainfall = $this->rainDummyPaternArray();

        // メソッド実行用のパラメーター「雨降り状態」を生成
        $resArr = [
            [
                'communityId'   => 1,
                'body'          => $this->rainyResponseJsonDummy($rainfall['nowRain']),
            ]
        ];

        // メソッド実行
        $service = app()->make('\App\Service\API\WeatherCheckService');
        $res = $service->responseRainJudging($resArr);
        // 雨予報通知処のresultメッセージが帰っているか?
        $this->assertEquals($res[0]['updateStatus'], "**雨確認時間の更新**");
        $this->assertEquals($res[0]['result'], "**雨降り始め通知**");
        // 雨予報の状態が更新されているか
        $this->assertDatabaseHas('communities', [
            'id'                        =>1,
            'last_maybe_rainy_datetime' => $subMinute,
            'last_rainy_datetime'       => Carbon::now()->toDateTimeString(),
            'last_sunny_datetime'       => $subHour,
        ]);
    }

    /**
     * 降雨量のダミーで雨状態の更新Test
     * @test
     * @return void
     */
    public function whatherAPI雨降り状態の更新Test()
    {
        $targetDt = Carbon::now();
        Carbon::setTestNow($targetDt);

        $subDay     = Carbon::now()->subDay()->toDateTimeString();
        $subMinute  = Carbon::now()->subMinutes(10)->toDateTimeString();
        $subHour    = Carbon::now()->subHour()->toDateTimeString();

        // 雨の降り続いている状態を生成
        factory(Community::class)->create([
            'google_home_weather_enable'    => 1,
            'last_maybe_rainy_datetime'     => $subHour,
            'last_rainy_datetime'           => $subMinute,
            'last_sunny_datetime'           => $subDay,
            'last_rain_stop_info_datetime'  => $subDay,
        ]);

        factory(Router::class)->create([
            'community_id'    => 1,
        ]);

        // 降雨量の配列を取得
        $rainfall = $this->rainDummyPaternArray();

        // メソッド実行用のパラメーター「雨降り状態」を生成
        $resArr = [
            [
                'communityId'   => 1,
                'body'          => $this->rainyResponseJsonDummy($rainfall['nowRain']),
            ]
        ];

        // メソッド実行
        $service = app()->make('\App\Service\API\WeatherCheckService');
        $res = $service->responseRainJudging($resArr);
        $this->assertEquals($res[0]['updateStatus'], "**雨確認時間の更新**");
        // 通知は存在しない
        $this->assertFalse(isset($res[0]['result']));
        // 雨状態が更新されているか
        $this->assertDatabaseHas('communities', [
            'id'                        =>1,
            'last_maybe_rainy_datetime' => $subHour,
            'last_rainy_datetime'       => Carbon::now()->toDateTimeString(),
            'last_sunny_datetime'       => $subDay,
        ]);
    }

    /**
     * 降雨量のダミーで雨止み通知と更新のTest
     * @test
     * @return void
     */
    public function whatherAPI雨止み通知と更新Test()
    {
        $targetDt = Carbon::now();
        Carbon::setTestNow($targetDt);

        $subDay     = Carbon::now()->subDay()->toDateTimeString();
        $subMinute  = Carbon::now()->subMinutes(10)->toDateTimeString();
        $subHour    = Carbon::now()->subHour()->toDateTimeString();

        // 雨の止みそうな状態を生成
        factory(Community::class)->create([
            'google_home_weather_enable'    => 1,
            'last_maybe_rainy_datetime'     => $subMinute,
            'last_rainy_datetime'           => $subMinute,
            'last_sunny_datetime'           => $subDay,
            'last_rain_stop_info_datetime'  => $subDay,
        ]);

        factory(Router::class)->create([
            'community_id'    => 1,
        ]);

        // 降雨量の配列を取得
        $rainfall = $this->rainDummyPaternArray();

        // メソッド実行用のパラメーター「晴れ」を生成
        $resArr = [
            [
                'communityId'   => 1,
                'body'          => $this->rainyResponseJsonDummy($rainfall['sunny']),
            ]
        ];

        // メソッド実行
        $service = app()->make('\App\Service\API\WeatherCheckService');
        $res = $service->responseRainJudging($resArr);
        $this->assertEquals($res[0]['updateStatus'], "**晴れ確認時間の更新**");
        $this->assertEquals($res[0]['result'], "**雨止み通知**");
        // 雨状態が更新されているか
        $this->assertDatabaseHas('communities', [
            'id'                        =>1,
            'last_maybe_rainy_datetime' => $subMinute,
            'last_rainy_datetime'       => $subMinute,
            'last_sunny_datetime'       => Carbon::now()->toDateTimeString(),
        ]);
    }
}
