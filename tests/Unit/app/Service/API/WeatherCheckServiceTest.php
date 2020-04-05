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
        // Artisan::call('migrate:refresh');
        // Artisan::call('db:seed', ['--class' => 'CommunitiesTableSeeder']);
        // Artisan::call('db:seed', ['--class' => 'CommunityUserTableSeeder']);
        // Artisan::call('db:seed', ['--class' => 'MacAddressesTableSeeder']);
        // Artisan::call('db:seed');
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
            'sunny'         => [
                'Rainfall' => [
                    0 => 0.00,
                    1 => 0.00,
                    2 => 0.00,
                    3 => 0.00,
                    4 => 0.00,
                    5 => 0.00,
                    6 => 0.00,
                ]
            ],
            'maybeRainy'    => [
                'Rainfall' => [
                    0 => 0.00,
                    1 => 0.00,
                    2 => 0.00,
                    3 => 0.00,
                    4 => 5.00,
                    5 => 5.00,
                    6 => 5.00,
                ]
            ],
            'nowRain'    => [
                'Rainfall' => [
                    0 => 1.00,
                    1 => 1.00,
                    2 => 1.00,
                    3 => 1.00,
                    4 => 1.00,
                    5 => 1.00,
                    6 => 1.00,
                ]
            ],
            'maybeSunny'    => [
                'Rainfall' => [
                    0 => 5.00,
                    1 => 0.00,
                    2 => 0.00,
                    3 => 0.00,
                    4 => 0.00,
                    5 => 0.00,
                    6 => 0.00,
                ]
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
     * 雨状態のダミーで返却値と処理の確認をTest
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
        ]);

        // 降雨量の配列を取得
        $rainfall = $this->rainDummyPaternArray();

        // メソッド実行用のパラメーター「晴れ」を生成
        $resArr = [
            [
                'communityId'   => 1,
                'body'          => $this->rainyResponseJsonDummy($rainfall['sunny']['Rainfall']),
            ]
        ];

        // メソッド実行
        $service = app()->make('\App\Service\API\WeatherCheckService');
        $res = $service->responseRainJudging($resArr);
        // 晴れ更新のstatusメッセージが帰っているか?
        $this->assertEquals($res[0]['updateStatus'], "**晴れ確認時間の更新**");
        // 晴れ時間が現在に更新されているか
        $this->assertDatabaseHas('communities', [
            'id'                        =>1,
            'last_maybe_rainy_datetime' => $subDay,
            'last_rainy_datetime'       => $subDay,
            'last_sunny_datetime'       => Carbon::now(),
        ]);
    }
    /**
     * 雨状態のダミーで返却値と処理の確認をTest
     * 雨予報の状態が更新されるか
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
            'last_maybe_rainy_datetime' => $subDay,
            'last_rainy_datetime'       => $subDay,
            'last_sunny_datetime'       => $subMinute,
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
                'body'          => $this->rainyResponseJsonDummy($rainfall['maybeRainy']['Rainfall']),
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
}
