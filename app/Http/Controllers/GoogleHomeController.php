<?php

namespace App\Http\Controllers;

use DB;
use App\AuthUser;
use App\Http\Controllers\IndexController;
use App\TalkMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// 現状200文字以上ではGoogleHomeにしゃべらせることはできない
class GoogleHomeController extends Controller
{
    public function temporaryTakingRecorder(Request $request, IndexController $indexController)
    {
        $request->validate([
            'community_user_id' => 'required|integer',
            'router_id'         => 'required|integer',
            'talking_message'   => 'required|max:140',
        ]);
        $user = AuthUser::find($request->community_user_id);
        if (is_null($user->name_reading)) {
            $name = $user->name;
        } else {
            $name = $user->name_reading;
        }
        // name max 30
        // 23文字 ライブリンクより、、さんからのメッセージです。
        $message = 'ライブリンクより、' . $name . '、さんからのメッセージです。 ' . $request->talking_message;

        $model = new TalkMessage();
        $model->router_id           = $request->router_id;
        $model->talking_message     = $message;
        $model->save();

        return redirect('/')->with('message', 'メッセージを受け付けました。');
    }

    public function GoogleHomeMessageWelcomeMaker($google_talk_trigger, $community, $push_users)
    {
        // "Aさん Bさん ..."の連結文作成
        $users_name_str = "";
        $users_name_only_str = "";
        $count = 0;
        foreach ((array)$push_users as $user) {
            $user['name_reading'] ? $user_name = $user['name_reading'] : $user_name = $user['name'];

            $users_name_str .= $user_name . "さん。";
            $users_name_only_str .= $user_name . "。";
            $count++;
        }
        if ($community->service_name_reading) {
            $service_name = $community->service_name_reading;
        } else {
            $service_name = $community->service_name;
        }
        $greeting = $this->greetingMessageMaker();
        switch ($google_talk_trigger) {
            case 'new_comer':
                if ($count == 1) {
                    $message = 'ワイファイに接続された方。' . $service_name . '。へようこそ。私は滞在者確認サービス、ライブリンクです。詳しくはパンプレットをご覧ください。';
                    // 私は滞在者確認アプリ。スマホでここにいる人がわかるサービスです。よかったらキューアールコードを読み取り、画面の仮ユーザー名。' . $users_name_only_str .'　を、クリックして登録をお願いします。';
                }
                // 複数端末同時接続時の挨拶
                if ($count > 1 || mb_strlen($message) > 200) {
                    $message = 'ワイファイに接続された方。' . $service_name . '。へようこそ。私は滞在者確認サービス、ライブリンクです。詳しくはパンプレットをご覧ください。';
                    // $message = 'いまワイファイに接続された皆さん、ようこそ。' . $service_name . 'へ。　私は滞在者確認アプリ。スマホでここにいる人がわかるサービスです。よかったらキューアールコードを読み取って、画面をご覧ください。次回お越しの際、お一人のみで接続された際は、簡単に登録が可能です';
                }
            break;

            case 'users_arraival':
                $frank_talk = array(
                    "実は地味に時間別で挨拶ができる様になりました。",
                    "ライブリンクがにツモリンク機能がつきましたよ、是非使ってみてください",
                    "ライブリンクの画面からログインすれば、皆さんにここに行くつもりや、帰るつもりをお知らせできるようになりますよ。",
                    "ライブリンクのプロフィール編集画面で、ふりがなを入れてもらうと、お名前を正しく言える様になりました。",
                    "実は常連さんへの挨拶は8種類です。みなさんで増やしたり変更したりできる様になるかもしれません",
                    "実はお名前を正しく言える様になりました。ライブリンクのプロフィール編集画面で、ふりがなを入れてくださいね。",
                    "打ち合わせ中でしたらすみません。そろそろ空気が読めるようになりたいです。",
                    "そのうちどなたかの伝言などをお伝えできるようになりたいです。",
                );
                $i = rand(0,7);
                //  $frank_talk[$i] 廃止
                $message = $greeting . $users_name_str;
                if (mb_strlen($message) > 200) {
                    $message = $greeting . "みなさん。一度にたくさんの方がいらっしゃったみたいでちょっとびっくりです。" . $frank_talk;
                }
            break;

            default:
                $message = $greeting . 'どなたかがいらっしゃったみたいですね。';
            break;
        }
        return $message;
    }

    public function GoogleHomeMessageTumolinkMaker($trigger, $user_name, $time)
    {
        $message = 'ライブリンクよりお知らせです。';
        $time = $time->format('G時i分');
        switch ($trigger) {
            case 'maybe_arraival':
                $message .= $user_name . 'さんが、' . $time . 'くらいに来るつもりみたいですよ。';
                break;

            case 'maybe_departure':
                $message .= $user_name . 'さんが、' . $time . 'くらいに帰るつもりみたいですよ。';
                break;

            case 're_maybe_arraival':
                $message .= $user_name . 'さんが、やっぱり' . $time . 'くらいに来るつもりみたいですよ。';
                break;

            case 're_maybe_departure':
                $message .= $user_name . 'さんが、やっぱり' . $time . 'くらいに帰るつもりみたいですよ。';
                break;

            case 'cancel_arraival':
                $message .= $user_name . 'さんが、来るのをやめたみたいです。';
                break;

            case 're_stay':
                $message .= $user_name . 'さんは、もう少しいるつもりみたいです。';
                break;

            default:
                $message .= 'ツモリンクの調子が悪いみたいです。開発者に伝えてもらえると嬉しいです。';
                break;
        }
        return $message;
    }

    public function greetingMessageMaker()
    {
        $hour = date("G");
        if ($hour >= 5 && $hour < 8) {
            $message = 'お早うございます、早起きですね、';
        } elseif ($hour >= 8 && $hour < 10) {
            $message = 'お早うございます、';
        } elseif ($hour >= 10 && $hour < 18) {
            $message = 'こんにちは、';
        } elseif ($hour >= 18 && $hour < 23) {
            $message = 'こんばんは、';
        } elseif ($hour >= 23 || $hour < 5) {
            $message = 'こんな遅くに、こんばんは、';
        } else {
            $message = "こんにちは、";
        }
        return $message;
    }
}
