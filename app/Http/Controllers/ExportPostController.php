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
    // $push_users = array( "id" => $user->id, "name" => $user->name)
    // $community  = communities table single record object
    public function push_ifttt($push_users, $category, $community)
    {
        // 訪問者の名前と滞在中のおおよその人数を抽出、文字列を作成する
        $res = $this->stay_users_about_count($push_users, $community);
        // $res['users_count_str'] => "〇~〇（人）",
        // $res['users_name_str'] => "Aさん Bさん ...",

        // 帰宅想定時間（分）
        $minute = round( env("JUDGE_DEPARTURE_INTERVAL_SECOND") / 60, 0);
        $now = Carbon::now();
        $time = $now->subSecond($minute * 60);
        $time = $time->format('G:i');

        // 通知の種別設定
        switch ($category) {
            case 'arraival':
                $title = $community->service_name . 'に来訪者です';
                $message = $res['users_name_str'] . "が". $community->service_name . "に来ました。今たぶん" . $res['users_count_str'] . "名がいます。";
                break;

            case 'departure':
                $title = $community->service_name . 'から帰宅者です';
                if ($res['users_count_str'] == 0) {
                    $message = $res['users_name_str'] . "が". $minute . "分程前(". $time .")に" . $community->service_name . "から帰りました。今たぶん誰もいません。";
                } else {
                    $message = $res['users_name_str'] . "が". $minute . "分程前(". $time .")に" . $community->service_name . "から帰りました。今たぶん" . $res['users_count_str'] . "名がいます。";
                }
                break;

            default:
                $title = "";
                $message = "";
                break;
        }

        // urlの生成とIFTTTへPOST(GuzzleHttpを使用)
        $url1 = 'https://maker.ifttt.com/trigger/';
        $url2 = '/with/key/';
        $event_name = $community->ifttt_event_name;
        $url = $url1 . $event_name . $url2;
        $key = $community->ifttt_webhooks_key;
        $domain = action('IndexController@index');

        // https でのヘルパ関数の動きが不明な為、ひとまずこれで環境設定切り分け
        if (env('APP_ENV') == 'local') {
            // example  'http://192.168.10.10/index'
            $domain = action('IndexController@index');
        } elseif (env('APP_ENV') == 'production') {
            $domain = 'https://www.livelynk.jp/index';
        } else {
            $domain = 'https://www.livelynk.jp/index';
        }

        $home_url = $domain . '/?path=' . $community->url_path;
        $client = new \GuzzleHttp\Client([
            'base_uri' => $url,
        ]);
        $responce = $client->request('POST', $key, [
            'json' => [
                'value1' => $message,
                'value2' => $title,
                'value3' => $home_url,
            ],
            ["timeout" => 10],
            ["delay" => 2000.0]
        ]);
    }

    // 訪問者の名前と滞在中のおおよその人数を抽出、文字列を作成する
    // $push_users = array( "id" => $user->id, "name" => $user->name)
    // $community  = communities table single record object
    public function stay_users_about_count($push_users, $community)
    {
        // "Aさん Bさん ..."の文字列作成
        $users_name_str = "";
        foreach ((array)$push_users as $user) {
            $users_name_str .= "『" . $user['name'] . "』さん ";
        }

        // コミュニティの管理 user_id 以外の既存user滞在者数
        $existing_count = DB::table('mac_addresses')
            ->distinct()->select('user_id')
            ->where([
                ['community_id', $community->id],
                ['user_id','<>', $community->user_id],
                ['current_stay', 1],
                ['hide', 0],
            ])->get();
        $existing_count = $existing_count->count();

        // コミュニティの管理 user_id に紐づいた滞在中のデバイス数
        $unknown_count = DB::table('mac_addresses')
            ->where([
                ['current_stay', 1],
                ['hide', 0],
                ['user_id', $community->user_id],
            ])->count();

        // 想定される最大滞在者数
        $about_max = $existing_count + $unknown_count;
        // 在籍者数の文字列作成 "n名", "n～n+ 名"
        if ($existing_count  == 0 && $unknown_count == 0 ) {
            $users_res = 0;
        } elseif ($existing_count  > 0 && $unknown_count == 0 ) {
            $users_res = $existing_count;
        } elseif ($existing_count == 0 && $unknown_count == 1 ) {
            $users_res = 1;
        } elseif ($existing_count == 0 && $unknown_count >  1 ) {
            $users_res = "1～" . $unknown_count;
        } elseif ($existing_count  > 0 && $unknown_count >  1 ) {
            $users_res = $existing_count . "～" . $about_max;
        } else {
            $users_res = $existing_count . "～" . $about_max;
        }
        return array(
            'users_count_str' => $users_res,
            'users_name_str' => $users_name_str,
        );
    }
}
