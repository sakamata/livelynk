<?php

namespace App\Service\API;

use Illuminate\Support\Facades\Log;

/**
 * 天気予報APIのダミー値の定義
 */
class WeatherCheckDummyService
{
    /**
     * 雨状態のダミー
     * @test@dataProvider rainyResponseJsonDummy
     *
     * @return void
     */
    public function rainyResponseDummy()
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
                                    "Rainfall" => 2.63,
                                ],
                                1 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291240",
                                    "Rainfall" => 3.63,
                                ],
                                2 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291250",
                                    "Rainfall" => 4.13,
                                ],
                                3 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291300",
                                    "Rainfall" => 5.25,
                                ],
                                4 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291310",
                                    "Rainfall" => 6.25,
                                ],
                                5 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291320",
                                    "Rainfall" => 6.75,
                                ],
                                6 => [
                                    "Type" => "forecast",
                                    "Date" => "202003291330",
                                    "Rainfall" => 3.13,
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
