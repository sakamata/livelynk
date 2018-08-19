<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExportPostController extends Controller
{
    // IFTTTに来訪者通知をPOSTする
    public function push_ifttt_arraival($push_users, $users_count_str)
    {
        $value1 = "";
        foreach ((array)$push_users as $user) {
            $value1 .= "(ID:". $user['id'] . ")『" . $user['name'] . "』さん ";
        }
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
