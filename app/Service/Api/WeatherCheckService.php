<?php

namespace App\Service\API;

use DB;
use Carbon\Carbon;
use App\Community;
use Illuminate\Support\Facades\Log;
use App\Repository\Api\WeatherCheckRepository;
use App\Http\Controllers\ExportPostController;
use App\Http\Controllers\GoogleHomeController;

/**
 *
 */
class WeatherCheckService
{
    private $community;
    private $exportPostController;
    private $googleHomeController;
    private $weatherCheckRepository;

    public function __construct(
        Community               $community,
        ExportPostController    $exportPostController,
        GoogleHomeController    $googleHomeController,
        WeatherCheckRepository  $weatherCheckRepository
    ) {
        $this->community                = $community;
        $this->exportPostController     = $exportPostController;
        $this->googleHomeController     = $googleHomeController;
        $this->weatherCheckRepository   = $weatherCheckRepository;
    }
    /**
     * 天気通知有効のコミュニティのAPI URLを生成して配列で返却する
     */
    public function urlMaker()
    {
        $url = "https://map.yahooapis.jp/weather/V1/place?";
        $url .= "&appid=" . config("env.yahoo_api_id");
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
            $response[$i]['resourceAPIStatus'] = $res['body']['ResultInfo']['Status'];

            $communityId = $res['communityId'];
            $weatherArr  = $res['body']['Feature'][0]['Property']['WeatherList']['Weather'];
            // APIの天気部分を抽出
            // 10分毎の雨量の合計・最初・最後を取得
            $rain       = $this->rainTotalize($weatherArr);
            $total  = $rain['total'];

            $community  = $this->community::find($communityId);
            // 雨ステータスの更新
            $response[$i]['updateStatus'] = $this->rainStatusUpdater($rain, $community);

            $response[$i]['communityId']        = $community->id;
            $response[$i]['communityName']      = $community->service_name;
            $response[$i]['lastRainyDatetime']  = $community->last_rainy_datetime;
            $response[$i]['geometry']           = $res['body']['Feature'][0]['Geometry']['Coordinates'];
            $response[$i]['nowRain']            = $rain['now'];
            $response[$i]['futuresRain']        = $rain['futures'];
            $response[$i]['mostFuture']         = $rain['mostFuture'];
            $response[$i]['totalRain']          = $rain['total'];

            $message = "";
            $weatherStatus = "";

            // **雨が止みそう判定**
            // こっち を雨予報より上の行に書くことで、重複条件の際は雨振り通知を上書きさせて優先判断させる
            // 最後は0  かつ 全体の降雨量が 0より多くnより小さい
            // if ($rain['future'] == 0 && (0 < $total  &&  $total <= 1)) {
            //     //  最終時間を調べて、一定期間以上なら通知を行う
            //     if ($community->last_sunny_datetime < Carbon::now()->subHour(1)) {
            //         $response[$i]['result'] = '**雨止みそう通知**';
            //         // 降雨量文言の生成
            //         $rainfall = $this->rainfallLangMaker($total);
            //         // 発話メッセージの作成
            //         $message = $this->googleHomeController
            //                     ->weatherStopRainingNotification($community, $rainfall);
            //         $weatherStatus = 'StopRain';
            //     }
            // }

            // **雨が降りそう判定**
            if ($rain['now'] == 0 && $total > 0) {
                // 最終時間を調べて、一定期間以上なら通知を行う
                if ($community->last_rainy_datetime < Carbon::now()->subHour(2)) {
                    $response[$i]['result'] = '**雨予報あり通知**';
                    // 降雨量文言の生成
                    $rainfall = $this->rainfallLangMaker($total);
                    // 発話メッセージの作成
                    $message = $this->googleHomeController
                                ->weatherRainNotification($community, $rainfall);
                    $weatherStatus = 'forRain';
                }
            }

            // 雨振り予報通知タイミング
            // 現在雨予報が出ている
            // 五月雨通知防止 前回の振るかもフラグが立った日時から一定以上の時間が経過　例：2時間
            // かつ雨フラグからある程度の時間が経過している場合　例:4時間
            if (
                $rain['now'] > 0 &&
                // 雨予報が15分前よりも最近なら
                $community->last_maybe_rainy_datetime > Carbon::now()->subMinutes(15)
                ) {
                $response[$i]['result'] = '**雨降り始め通知**';
                // 降雨量文言の生成
                $nowRainfall = $this->rainfallLangMaker($rain['now']);
                $rainfall = $this->rainfallLangMaker($total);
                // 発話メッセージの作成
                $message = $this->googleHomeController
                                ->weatherNowRainNotification($nowRainfall, $rainfall);
                $weatherStatus = 'nowRainIn';
            }


            // **雨が振っていない場合**
            if ($total == 0) {
                // 現在のステータスが雨なら晴れました通知を行う
                if ($community->last_rainy_datetime > $community->last_sunny_datetime) {
                    $response[$i]['result'] = '**雨止み通知**';
                    // 降雨量文言の生成
                    // $rainfall = $this->rainfallLangMaker($total);
                    // 発話メッセージの作成
                    // TODO ひとまずコメントアウトで雨止み通知を停止する
                    // $message = $this->googleHomeController
                    //             ->weatherStopRainingNotification();
                    // $weatherStatus = 'StopRain';
                }

                // 晴れステータスを更新
                $response[$i]['result'] = '**雨予報無し・晴れ確認時間の更新**';
                $community->last_sunny_datetime = Carbon::now();
                $community->save();
            }

            // 発話をDBに入れる
            if ($message) {
                $this->weatherCheckRepository->talkMessageSave($message, $communityId);
            }
            // 通知機能へ
            if ($weatherStatus) {
                $this->exportPostController->weatherMassageMaker($community, $weatherStatus, $rainfall);
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
        $futures = 0;
        // 10分毎の雨量の合計・最初・最後を取得
        foreach ($weatherArr as $weather) {
            $val = floatval($weather['Rainfall']);
            $total = $total + $val;
            if ($weather === reset($weatherArr)) {
                // 最初
                $now = $val;
            } else {
                // 最初以外
                $futures += $val;
            }
            if ($weather === end($weatherArr)) {
                // 最 後
                $mostFuture = $val;
            }
        }
        $res = [
            'now'           => $now,
            'futures'       => $futures,
            'mostFuture'    => $mostFuture,
            'total'         => $total
        ];
        return $res;
    }

    /**
     * 降雨量の発話・文言を生成する
     */
    public function rainfallLangMaker(float $total)
    {
        $rainfall = round($total);
        if ($rainfall < 1) {
            $rainfall = '1ミリ未満';
        } else {
            $rainfall = $rainfall . 'ミリ程';
        }
        return $rainfall;
    }

    /**
     * コミュニティ単位で雨情報のステータスを変更する
     *
     * @param array $rain
     * @param \App\Community $community
     * @return string
     */
    public function rainStatusUpdater(array $rain, Community $community)
    {
        $message = '';
        // 晴れの更新
        if ($rain['total'] == 0) {
            $community->last_sunny_datetime = Carbon::now();
            $community->save();
            $message = '**晴れ確認時間の更新**';
            logger()->debug('快晴時間の更新 community>>> id:'. $community->id . ' ' .$community->service_name);
        }

        // 雨の更新
        if ($rain['now'] > 0) {
            $community->last_rainy_datetime = Carbon::now();
            $community->save();
            $message = '**雨確認時間の更新**';
            logger()->debug('現在雨の更新 community>>> id:'. $community->id . ' ' .$community->service_name);
        }

        // 雨振るかも の更新
        // 現在は振っていないが未来に降雨予報がある　かつ
        // 最後の雨の観測から一定時間が経過している
        $lastRain = new Carbon($community->last_rainy_datetime);
        if (
            $rain['futures'] > 0 &&
            $lastRain < Carbon::now()->subHours(2)
        ) {
            // 現在振っている 　更新しない
            // 雨が観測されない 　更新しない
            if (!($rain['now'] > 0 || $rain['total'] === 0)) {
                $community->last_maybe_rainy_datetime = Carbon::now();
                $community->save();
                $message = '**雨予報あり時間の更新**';
                logger()->debug('雨降りそうの更新 community>>> id:'. $community->id . ' ' .$community->service_name);
            }
        }
        return $message;

        // 雨振り予報通知タイミング
        // 前回の振るかもフラグが立った日時から一定以上の時間が経過　例：2時間
        // かつ雨フラグからある程度の時間が経過している場合　例:4時間
    }
}
