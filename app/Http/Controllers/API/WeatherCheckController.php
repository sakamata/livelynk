<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Service\Api\WeatherCheckService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class WeatherCheckController extends Controller
{
    private $weatherCheckService;

    public function __construct(
        WeatherCheckService     $weatherCheckService
    ) {
        $this->weatherCheckService  = $weatherCheckService;
    }

    /**
     * 天気APIを実行し response を commyunityIdと紐づけて配列化して、降雨判定処理に送る
     */
    public function run()
    {
        // ひとまずは、コミュ数 * n分 / 日中 = で制限の5000回未満になるよう調整
        // TODO 1APIで10地点まで登録可能だが、逼迫したら対応、現状は1コミュ１回のAPIで飛ばす
        // APIの時間当たりの連続リクエスト数に注意
        // 余裕があればリクエスト上限で1コミュあたりのインターバルを算出した処理に変更

        // 天気通知有効のコミュニティのAPI URLを生成
        $weatherUrlArr = $this->weatherCheckService->urlMaker();
        $client = new Client();
        $i = 0;
        $status = "";
        $resArr = [];
        foreach ($weatherUrlArr as  $weatherUrl) {
            // API実行で天気データを取得
            $responseData = $client->request("GET", $weatherUrl['url']);
            // CORS対応 Route::get() に書いた際必要？
            //    ->middleware(\Barryvdh\Cors\HandleCors::class);
            $responseBody = json_decode($responseData->getBody()->getContents(), true);
            // 返却値の判定
            if (!isset($responseBody['ResultInfo'])) {
                Log::debug(print_r('weatherAPI ERROR!!! not key ResultInfo >>>', 1));
                Log::debug(print_r($responseBody, 1));
                $status = 404;
                return;
            }
            if ($responseBody['ResultInfo']['Status'] != 200) {
                Log::debug(print_r('weatherAPI ERROR!!!  Status not 200 >>>', 1));
                Log::debug(print_r($responseBody, 1));
                $status = 404;
            }
            $resArr[$i] = [
                'communityId'   => $weatherUrl['communityId'],
                'body'          => $responseBody,
            ];
            $i++;
        }
        // コミュニティIDと紐付けて雨通知の判定と発話の登録を行う
        $result = $this->weatherCheckService->responseRainJudging($resArr);
        if (!$result) {
            $status = 500;
        } else {
            $status = 200;
        }
        log::debug($result);

        return response()->json([
            'datetime'  => Carbon::now(),
            'result'    => $result
        ], $status);
    }
}
