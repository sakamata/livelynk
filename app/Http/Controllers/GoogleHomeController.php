<?php

namespace App\Http\Controllers;

use DB;
use App\Community;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleHomeController extends Controller
{

    public function GetGoogleHomeTalk($google_talk_trigger, $community, $push_users)
    {
        switch ($google_talk_trigger) {
            case 'new_comer':
                $message = 'ワイファイに初接続された方、ようこそ' . $community->service_name . 'へ。　私は滞在者確認サービスのライブリンクです。よかったらアプリへの登録をお願いします。';
            break;

            case 'users_arraival':
                // "Aさん Bさん ..."の連結文作成
                $users_name_str = "";
                foreach ((array)$push_users as $user) {
                    $users_name_str .=  $user['name'] . "さん。";
                }
                $frank_talk = array(
                    'ライブリンクが挨拶できるようになりましたよ',
                    'いつ以来の来訪なのか、そのうちお知らせできるようにしますね',
                    '今は朝ですか？昼ですか？夜ですか？挨拶をちゃんとするようにしますね',
                    'おひとりですか？それともどなたかと一緒でしょうか？察する事ができるようになりますね',
                    'そのうち挨拶がご迷惑にならないように空気が読めるようになりますね',
                    'そのうち今日のお天気などお伝えしても良いですかね？',
                    'お名前の読み方は正しかったでしょうか？失礼のないように正しくお名前を言えるようになりますね',
                    'これからもっと気の利いたことが言える、できる秘書になりたいです',
                );
                $i = rand(0,7);
                $message = 'こんにちは' . $users_name_str . $frank_talk[$i];
            break;

            default:
                $message = 'ライブリンクへの送信を確認しました。';
            break;
        }
        return array(
            'MAC' => $community->google_home_mac_address,
            'name' => $community->google_home_name,
            'message' => $message,
        );
    }
}
