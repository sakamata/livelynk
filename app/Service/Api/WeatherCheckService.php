<?php

namespace App\Service\Api;

use DB;
use Carbon\Carbon;
use App\Community;
use Illuminate\Support\Facades\Log;
use App\Repository\WeatherCheckRepository;
use App\Http\Controllers\GoogleHomeController;


/**
 *
 */
class WeatherCheckService
{
    private $community;
    private $googleHomeController;
    private $weatherCheckRepository;

    public function __construct(
        Community               $community,
        GoogleHomeController    $googleHomeController,
        WeatherCheckRepository  $weatherCheckRepository
        )
    {
        $this->community                = $community;
        $this->googleHomeController     = $googleHomeController;
        $this->weatherCheckRepository   = $weatherCheckRepository;
    }
    /**
     * 天気通知有効のコミュニティのAPI URLを生成して配列で返却する
     */
    public function urlMaker()
    {
        $url = "https://map.yahooapis.jp/weather/V1/place?";
        $url .= "&appid=" . env("YHOO_API_ID");
        $url .= "&output=json";
        // 天気APIが有効で滞在者のいるコミュニティの緯度経度とIDを配列で取得する
        $locations = $this->weatherCheckRepository->stayUserCommunitiesLocations();
        $i = 0;
        $weatherUrlArr = [];
        foreach ($locations as $location) {
            $coordinates = "&coordinates=" . $location->longitude . ',' . $location->latitude;
            $weatherUrl = $url . $coordinates;
            $weatherUrlArr[$i] = [
                'communityId' => $location->community_id,
                'url'         => $weatherUrl
            ];
            $i++;
        }
        return $weatherUrlArr;
    }

    /**
     * 降雨の判定処理を行う、降雨が観測された時間の記録を行う
     */
    public function responseRainJudging(array $resArr)
    {
        $i = 0;
        $response = [];
        foreach ($resArr as $res) {
            // APIステータスの抽出
            $response[$i]['resourceAPIStatus'] =  $res['body']['ResultInfo']['Status'];

            $communityId = $res['communityId'];
            $weatherArr =  $res['body']['Feature'][0]['Property']['WeatherList']['Weather'];
            // APIの天気部分を抽出
            // 10分毎の雨量の合計・最初・最後を取得
            $rain = $this->rainTotalize($weatherArr);
            $first = $rain['first'];
            $last  = $rain['last'];
            $total = $rain['total'];

            $community = $this->community::find($communityId);
            $response[$i]['communityId']   = $community->id;
            $response[$i]['communityName'] = $community->service_name;
            $response[$i]['lastRainyDatetime'] = $community->last_rainy_datetime;
            $response[$i]['geometry']      = $res['body']['Feature'][0]['Geometry']['Coordinates'];
            $response[$i]['firstRain']     = $rain['first'];
            $response[$i]['lastRain']      = $rain['last'];
            $response[$i]['totalRain']     = $rain['total'];

            $message = "";
            // **雨が降りそう判定**
            if ($first == 0 && $total > 0) {
                // 最終時間を調べて、一定期間以上なら通知を行う
                if ($community->last_rainy_datetime < Carbon::now()->subHour(2)) {
                    // 発話メッセージの作成
                    $message = $this->googleHomeController
                                ->weatherRainNotification($community, $total);
                    $response[$i]['reslut'] = '**雨予報あり通知**';
                }
            }

            // **雨が止みそう判定**
            // 最後は0 かつ 全体の降雨量が 0より多くnより小さい
            if ($last == 0 && ( 0 < $total  &&  $total <= 1 )) {
                // 最終時間を調べて、一定期間以上なら通知を行う
                if ($community->last_rainy_datetime < Carbon::now()->subMinutes(5)) {
                    // 発話メッセージの作成
                    Log::debug('止みそう');
                    $message = $this->googleHomeController
                                ->weatherStopRainingNotification($community, $total);
                    $response[$i]['reslut'] = '**雨止みそう通知**';
                }
            }

            // 発話をDBに入れる
            if ($message != null) {
                $this->weatherCheckRepository->talkMessageSave($message, $communityId);
            }

            // 雨が観測された場合は雨確認時間を更新
            if ($total > 0) {
                $community->last_rainy_datetime = Carbon::now();
                $community->save();
                log::debug(print_r('last_rainy_datetime update community service_name >>>' . $community->service_name,1));
                $response[$i]['update'] = '**雨確認時間の更新**';
            }

            // **雨が振っていない場合**
            if ($total == 0) {
                $response[$i]['reslut'] = '雨予報無し';
            }

            $i++;
        } // foreach end
        return $response;
    }

    /**
     * APIの10分毎の雨量を合計、初回と、最後の雨量の値を取得して返却する
     */
    public function rainTotalize(array $weatherArr)
    {
        $total = 0;
        // 10分毎の雨量の合計・最初・最後を取得
        foreach ($weatherArr as $weather) {
            $val = floatval($weather['Rainfall']);
            $total = $total + $val;
            if ($weather === reset($weatherArr)) {
                // 最初
                $first = $val;
            }
            if ($weather === end($weatherArr)) {
                // 最後
                $last = $val;
            }
        }
        $res = [
            'first' => $first,
            'last'  => $last,
            'total' => $total
        ];
        return $res;
    }
}
