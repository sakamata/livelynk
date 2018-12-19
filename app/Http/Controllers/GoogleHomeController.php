<?php

namespace App\Http\Controllers;

use DB;
use App\Community;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleHomeController extends Controller
{
    public function GetGoogleHomeTalk($google_talk_trigger, $community, $push_users, $router_id)
    {
        // "Aさん Bさん ..."の連結文作成
        $users_name_str = "";
        $users_name_only_str = "";
        $count = 0;
        foreach ((array)$push_users as $user) {
            $users_name_str .= $user['name'] . "さん。";
            $users_name_only_str .= $user['name_only'] . "。";
            $count++;
        }
        if ($community->service_name_reading) {
            $service_name = $community->service_name_reading;
        } else {
            $service_name = $community->service_name;
        }
        switch ($google_talk_trigger) {
            case 'new_comer':
                if ($count == 1) {
                    $message = 'いまワイファイに初接続された方、ようこそ' . $service_name . 'へ。　私は滞在者確認アプリ。スマホでここにいる人がわかるサービスです。よかったらキューアールコードを読み取り、画面の仮ユーザー名。' . $users_name_only_str .'　を、クリックして登録をお願いします。';
                }
                // 複数端末同時接続時の挨拶
                if ($count > 1 || mb_strlen($message) > 200) {
                    $message = 'いまワイファイに初接続された皆さん、ようこそ。' . $service_name . 'へ。　私は滞在者確認アプリ。スマホでここにいる人がわかるサービスです。よかったらキューアールコードを読み取って、画面をご覧ください。次回お越しの際、お一人のみで接続された際は、簡単に登録が可能です';
                }
            break;

            case 'users_arraival':
                $frank_talk = array(
                    'ライブリンクが挨拶できるようになりましたよ',
                    'いつ以来の来訪なのか、そのうちお知らせできるようにしますね',
                    '今は朝ですか？昼ですか？夜ですか？挨拶をちゃんとするようにしますね',
                    '今日はおひとりでの来訪でしょうか？それともどなたかとご一緒での来訪でしょうか？そういうこともわかるようになりたいです',
                    'そのうち挨拶がご迷惑にならないように空気が読めるようになりますね',
                    'そのうち今日のお天気などお伝えしても良いですかね？',
                    'お名前の読み方は正しかったでしょうか？失礼のないように正しくお名前を言えるようになりますね',
                    'これからもっと気の利いたことが言える、できる秘書になりたいです',
                );
                $i = rand(0,7);
                $message = 'こんにちは' . $users_name_str . $frank_talk[$i];
                if (mb_strlen($message) > 200) {
                    $message = 'こんにちは、みなさん。一度にたくさんの方がいらっしゃったみたいでちょっとびっくりです。' . $frank_talk;
                }
            break;

            default:
                $message = 'ライブリンクへの送信を確認しました。';
            break;
        }
        $router = DB::table('routers')->where('id', $router_id)->first();
        return array(
            'MAC' => $router->google_home_mac_address,
            'name' => $router->google_home_name,
            'message' => $message,
        );
    }
}
