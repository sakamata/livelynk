<?php

namespace App\Http\Controllers;

use DB;
use App\AuthUser;
use App\MailBoxName;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExportPostController extends Controller
{
    // IFTTTに来訪者/帰宅者通知をPOSTするメッセージの加工を行う
    // $push_users = array( "id" => $user->id, "name" => $user->name)
    // $community_id  = int, communities table 'id' column
    public function access_message_maker($push_users, $category, $community_id)
    {
        $community = DB::table('communities')->where('id', $community_id)->first();
        // 訪問者の名前と滞在中のおおよその人数を抽出、文字列を作成する
        $res = $this->stay_users_about_count($push_users, $community);
        // $res['users_count_str'] => "〇~〇（人）",
        // $res['users_name_str'] => "Aさん Bさん ...",

        $minute = round(config("env.judge_departure_interval_second") / 60, 0);

        if ($category === 'mail_fatch_departure') {
            $minute = MailBoxName::MAX_MINUTES_JUDGE_STAY;
        }

        $now = Carbon::now();
        $time = $now->subSecond($minute * 60);
        $time = $time->format('G:i');

        if ($category === 'arraival') {
            $title = $community->service_name . 'に来訪者です';
            $message = $res['users_name_str'] . "が".
                $community->service_name . "に来ました。今たぶん" .
                $res['users_count_str'] . "名がいます。";
        }

        if ($category === 'mail_box_first_arraival') {
            $title = $community->service_name . 'に初来訪者です';
            $message = $res['users_name_str'] . "が".
                $community->service_name . "に来ました。今たぶん" .
                $res['users_count_str'] . "名がいます。";
        }

        if ($category === 'mail_box_create') {
            $title = $community->service_name . 'にユーザーが登録されました';
            $message = 'メールアカウント : '. $push_users[0]['name'];
        }

        if ($category === 'departure' || $category === 'mail_fatch_departure') {
            $title = $community->service_name . 'から帰宅者です';
            if ($res['users_count_str'] == 0) {
                $message = $res['users_name_str'] . "が".
                    $minute . "分程前(". $time .")に" .
                    $community->service_name . "から帰りました。今たぶん誰もいません。";
            } else {
                $message = $res['users_name_str'] . "が".
                    $minute . "分程前(". $time .")に" .
                    $community->service_name . "から帰りました。今たぶん" .
                    $res['users_count_str'] . "名がいます。";
            }
        }
        $this->push_ifttt($title, $message, $community);
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

        // todo これと同様の処理で合計をmail_box_names で導く
        // コミュニティの管理 user_id 以外の既存user滞在者数を求める
        // community_user_id でのgroupByはMySQLのメモリオーバーになるのでコツコツ処理する
        $macAddressCommunityUserIds = DB::table('community_user')
            ->select('community_user_id')
            ->leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_id', $community->id],
                ['user_id','<>', $community->user_id],
                ['current_stay', 1],
                ['hide', 0],
            ])
            ->get()->unique()->pluck('community_user_id');

        $mailBoxCommunityUserIds = DB::table('community_user')
            ->select('community_user_id')
            ->leftJoin('mail_box_names', 'mail_box_names.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_id', $community->id],
                ['user_id','<>', $community->user_id],
                ['current_stay', 1],
            ])
            ->get()->unique()->pluck('community_user_id');

        // 既存 user 滞在者数
        $existing_count = count($macAddressCommunityUserIds->merge($mailBoxCommunityUserIds)->unique());

        // 未登録 newcomer! の滞在数（コミュニティの管理 user_id に紐づいた滞在中のデバイス）
        $unknown_count = DB::table('community_user')
            ->leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_id', $community->id],
                ['user_id', $community->user_id],
                ['current_stay', 1],
                ['hide', 0],
            ])
        ->count();

        // 想定される最大滞在者数
        $about_max = $existing_count + $unknown_count;
        // 在籍者数の文字列作成 "n名", "n～n+ 名"
        if ($existing_count  == 0 && $unknown_count == 0) {
            $users_res = 0;
        } elseif ($existing_count  > 0 && $unknown_count == 0) {
            $users_res = $existing_count;
        } elseif ($existing_count == 0 && $unknown_count == 1) {
            $users_res = 1;
        } elseif ($existing_count == 0 && $unknown_count >  1) {
            $users_res = "1～" . $unknown_count;
        } elseif ($existing_count  > 0 && $unknown_count >  1) {
            $users_res = $existing_count . "～" . $about_max;
        } else {
            $users_res = $existing_count . "～" . $about_max;
        }
        return array(
            'users_count_str' => $users_res,
            'users_name_str' => $users_name_str,
        );
    }

    /**
     * ヒトコト機能のSlack通知のメッセージを生成する
     */
    public function temporaryTakingMessageMaker(AuthUser $user, string $message)
    {
        // 発信者が滞在中か確認する
        $isStay = $user->mac_address()->where('current_stay', 1)->exists();
        if ($isStay) {
            $stayStatus = '[滞在中]';
        } else {
            $stayStatus = '[非滞在中]';
        }

        $title = $stayStatus . $user->name . 'さんのヒトコトメッセージです';
        $community = DB::table('communities')->where('id', $user->community_id)->first();

        $this->push_ifttt($title, $message, $community);
    }

    /**
     * 雨予報通知のIFTTT送信用メッセージを作成する
     */
    public function weatherMassageMaker($community, string $weatherStatus, string $rainfall)
    {
        if (!is_null($community->service_name)) {
            if ($weatherStatus == 'forRain') {
                $title   = '雨の予報です';
                $message = $community->service_name . "周辺に雨が降りそうです。1時間以内に" . $rainfall . "の雨の予報が出ています";
            }

            if ($weatherStatus == 'StopRain') {
                $title   = '雨上がり予報です';
                $message = $community->service_name . "周辺の雨が止んだようです。";
            }

            if ($weatherStatus == 'nowRainIn') {
                $title   = '雨が降り始めたようです';
                $message = $community->service_name . "周辺に雨が降り始めたようです。現在" . $rainfall . "の雨が降っているようです。";
            }

            $this->push_ifttt($title, $message, $community);
        }
    }

    // IFTTTに通知をPOSTする
    // $community  = communities table single record object
    public function push_ifttt($title, $message, $community)
    {
        // testでは通知が飛ばない様に設定
        if (config('env.app_env') == 'testing'
            || $community->ifttt_event_name == null
            || $community->ifttt_webhooks_key == null
        ) {
            return;
        }
        // urlの生成とIFTTTへPOST(GuzzleHttpを使用)
        $url1 = 'https://maker.ifttt.com/trigger/';
        $url2 = '/with/key/';
        $event_name = $community->ifttt_event_name;
        $url = $url1 . $event_name . $url2;
        $key = $community->ifttt_webhooks_key;
        $domain = action('IndexController@index');

        // https でのヘルパ関数の動きが不明な為、ひとまずこれで環境設定切り分け
        if (config('env.app_env') == 'local') {
            // example  'http://192.168.10.10/index'
            $domain = action('IndexController@index');
        } elseif (config('env.app_env') == 'production') {
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
}
