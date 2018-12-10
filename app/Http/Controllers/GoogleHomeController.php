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
        // "Aさん Bさん ..."の連結文作成
        $users_name_str = "";
        $users_name_only_str = "";
        $count = 0;
        foreach ((array)$push_users as $user) {
            $users_name_str .= $user['name'] . "さん。";
            $users_name_only_str .= $user['name_only'] . "。";
            $count++;
        }
        switch ($google_talk_trigger) {
            case 'new_comer':
                if ($count == 1) {
                    $message = 'いまワイファイを接続された方、ようこそ' . $community->service_name . 'へ。　私は滞在者確認アプリです。どこでもここにいる人がわかるサービスです。よかったらQRコードを読み取って、画面の仮ユーザー名。' . $users_name_only_str .'　を、クリックしてください';
                }
                // 200文字以上は発話しない為の処理
                if ($count > 1 || mb_strlen($message) > 200) {
                    $message = 'ワイファイに初接続された方、ようこそ' . $community->service_name . 'へ。　私は滞在者確認アプリです。いつでもここにいる人がわかるサービスをしてます。よかったらQRコードを読み取って登録してみてください。同時に何名かのかたが接続されたようです。すみませんがご自分の端末を探してみてください';
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
