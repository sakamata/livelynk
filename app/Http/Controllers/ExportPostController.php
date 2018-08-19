<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExportPostController extends Controller
{
    // 滞在中のおおよその人数を抽出
    // IFTTTに来訪者通知をPOSTする
    // $push_users = array( "id" => $user->id, "name" => $user->name)
    public function push_ifttt_arraival($push_users)
    {
        // 管理user以外の既存user滞在者数
        $existing_count = DB::table('mac_addresses')
            ->distinct()->select('user_id')
            ->where([
                ['user_id','<>', 1],
                ['current_stay', 1],
                ['hide', 0],
            ])->get();
        $existing_count = $existing_count->count();

        // 管理user id=1 に紐づいた滞在中のデバイス数
        $unknown_count = DB::table('mac_addresses')
            ->where([
                ['current_stay', 1],
                ['hide', 0],
                ['user_id', 1],
            ])
            ->count();
        // 想定される最大滞在者数
        $about_max = $existing_count + $unknown_count;
        // "〇～〇" 名の文字列作成
        if ($unknown_count > 0) {
            $users_count_str = $existing_count . "～" . $about_max;
        } else {
            $users_count_str = $existing_count;
        }
        $value1 = "";
        foreach ((array)$push_users as $user) {
            $value1 .= "(ID:". $user['id'] . ")『" . $user['name'] . "』さん ";
        }

        // IFTTTへPOST処理
        $url1 = 'https://maker.ifttt.com/trigger/';
        $event_name = env("IFTTT_WEB_HOOKS_EVENT_ARRAIVAL");
        $url2 = '/with/key/';
        $url = $url1 . $event_name . $url2;
        $key = env("IFTTT_WEB_HOOKS_KEY");
        $client = new \GuzzleHttp\Client([
            'base_uri' => $url,
        ]);
        $responce = $client->request('POST', $key, [
            'json' => [
                'value1' => $value1,
                'value2' => $users_count_str,
                'value3' => "",
            ]
        ]);
    }

    // ***ToDo*** vendorが未登録なら MACアドレスから スクレイピングでメーカー名を自動登録させる処理を書く

}
