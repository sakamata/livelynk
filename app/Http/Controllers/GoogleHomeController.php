<?php

namespace App\Http\Controllers;

use DB;
use App\Community;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleHomeController extends Controller
{

    public function GetGoogleHomeTalk($google_talk_trigger, $community)
    {
        switch ($google_talk_trigger) {
            case 'new_comer':
                $message = 'ワイファイに初接続された方、ようこそ' . $community->service_name . 'へ。　私は滞在者確認サービスのライブリンクです。よかったらアプリへの登録をお願いします。';
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
