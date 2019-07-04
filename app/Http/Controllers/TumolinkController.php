<?php

namespace App\Http\Controllers;

use DB;
use App\Community;
use App\TalkMessage;
use App\Http\Requests\TumolinkPost;
use App\Service\CommunityUserService;
use App\Service\TumolinkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TumolinkController extends Controller
{
    private $tumolink_service;
    private $call_community_user;

    public function __construct(
        CommunityUserService $call_community_user,
        TumolinkService $tumolink_service
        )
    {
        $this->call_community_user = $call_community_user;
        $this->tumolink_service = $tumolink_service;
    }

    public function index(Request $request)
    {
        $request->validate([
            'community_id' => 'required|integer|exists:communities,id',
        ]);
        $res = $this->tumolink_service->tumolistGet($request->community_id);
        return response()->json($res);
    }

    public function post(TumolinkPost $request)
    {
        if (!$request->community_user_id) {
            return response()->json([], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $tumolist = new \App\Tumolink();
        $tumolist->community_user_id = $request->community_user_id;
        $tumolist->google_home_push = $request->google_home_push;
        $hour = intval($request->hour);
        $minute = intval($request->minute);
        $time = Carbon::now()->addHour($hour)->addSecond($minute * 60);
        $direction = $request->direction;

        if ($direction == 'arriving') {
            $tumolist->maybe_arraival = $time;
            $column = 'maybe_arraival';
        } else {  //   == 'leaving'
            $tumolist->maybe_departure = $time;
            $column = 'maybe_departure';
        }

        $tumoli_again = false;
        // cancelなら処理先移行
        if ($request->action == 'cancel') {
            $this->cancel($request, $column);
            $message = 'ツモリをキャンセルしました。';
        } else {
            // tumolink tableは 1ユーザーにつき1日1recordとなる仕様
            $exists = $this->tumolink_service->existsTodayPost($request->community_user_id);
            if ($exists) {
                // 既に来訪中で本日中の行くツモリなら画面にメッセージを出すのみ
                $current_stay = $this->call_community_user->IsCurrentStay($request->community_user_id);
                if (
                    $current_stay &&
                    $time < Carbon::tomorrow() &&
                    $direction == 'arriving'
                ) {
                    return redirect('/')->with('message', 'もう来ているみたいですよ');
                }
                // 「やっぱり」のメッセージ付与判定 既に宣言したか?の bool
                $tumoli_again = $this->tumolink_service->isAgainTumoli($request->community_user_id);
                // 同日中のpostであれば該当recordを更新する
                $this->tumolink_service->updateTime($request->community_user_id, $column, $time, $request->google_home_push);
            } else {
            // 新規ならrecord追加
                $tumolist->save();
            }
            $message = 'ツモリ宣言をしました。';
        }

        // ifttt 通知文作成
        $community = DB::table('communities')
            ->where('id', Auth::user()->community_id)->first();
        $messages = $this->tumoli_message_maker(
            $column,
            $time,
            $request->action,
            $tumoli_again,
            $community->service_name
        );

        // GoogleHome通知作成と保存
        if ($request->google_home_push == true && $community->google_home_enable == true) {
            // よみがな優先で発話
            Auth::user()->name_reading ? $user_name = Auth::user()->name_reading : $user_name = Auth::user()->name; 
            $talking_message = (new GoogleHomeController)->GoogleHomeMessageTumolinkMaker($messages['trigger'], $user_name, $time);
            // DBに入れる
            $talkMessage = new \App\TalkMessage();
            // ひままずcommunityの最初のrouterに紐づいたGoogleHomeを対象にする
            $router = 'App\Community'::find($community->id)->router()->orderBy('id')->first();
            $talkMessage->router_id       = $router->id;
            $talkMessage->talking_message = $talking_message;
            $talkMessage->save();
        }

        // POSTで固まるので処理後半で ifttt 通知
        (new ExportPostController)->push_ifttt($messages['title'], $messages['message'], $community);
        if (!$request->isJson()) {
        // post form 処理
            return redirect('/')->with('message', $message);
        } else {
        // APIの場合の追加response処理があれば記載
        }
    }

    public function cancel($request, $column)
    {
        // recordがあるか確認
        $exists = $this->tumolink_service->existsTodayPost($request->community_user_id);
        if (!$exists) {
            // 無ければ処理終了、例外処理 form api それぞれで処理
            Log::warning(print_r('tumolink no record cancel method run!!', 1));
            if (!$request->isJson()) {
                return redirect('/')->with('message', 'ツモリのキャンセルが出来ませんでした。');
            } else {
                return response()->json([], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        // 既存のrecordを取得
        $existing = $this->tumolink_service->getTodayRecord($request->community_user_id);
        // 現状の宣言が片方のみなら、recordを削除
        if ($existing->maybe_arraival == null || $existing->maybe_departure == null ) {
            $this->tumolink_service->remove($existing->id);
        } elseif ($existing->maybe_arraival && $existing->maybe_departure) {
            // 両方ある場合は指定した日時をnullにする
            $this->tumolink_service->updateTimeNull($existing->id, $column);
            // ***ToDo*** キャンセルのGoogleHome通知、DBにキャンセルの通知フラグ必要
        }
    }

    public function tumoli_message_maker($column, $time, $action, $tumoli_again, $service_name)
    {
        $name = Auth::user()->name;
        $time = $time->format('G:i');
        $mess = array();
        $mess['title'] = "お知らせツモリンク";
        $mess['message'] = "";
        $mess['trigger'] = "";
        // 2*2*2=6種類のメッセージ分岐なのでネストせず判定
        if ($column == 'maybe_arraival' && $tumoli_again == false) {
            $mess['message'] = $name . "「" . $time . "頃に、" . $service_name . "に行くツモリンク！」";
            $mess['trigger'] = 'maybe_arraival';
        }
        if ($column == 'maybe_departure' && $tumoli_again == false) {
            $mess['message'] = $name . "「" . $time . "頃に、" . $service_name . "から帰るツモリンク！」";
            $mess['trigger'] = 'maybe_departure';
        }
        if ($column == 'maybe_arraival' && $tumoli_again) {
            $mess['message'] = $name . "「やっぱり" . $time . "頃に、" . $service_name . "に行くツモリンク！」";
            $mess['trigger'] = 're_maybe_arraival';
        }
        if ($column == 'maybe_departure' && $tumoli_again) {
            $mess['message'] = $name . "「やっぱり" . $time . "頃に、" . $service_name . "から帰るツモリンク！」";
            $mess['trigger'] = 're_maybe_departure';
        }
        if ($action == 'cancel' && $column == 'maybe_arraival') {
            $mess['message'] = $name . "「やっぱり". $service_name . "に行くのをやめる」";
            $mess['trigger'] = 'cancel_arraival';
        }
        if ($action == 'cancel' && $column == 'maybe_departure') {
            $mess['message'] = $name . "「やっぱり" . $service_name . "にもうしばらくいるツモリ！」";
            $mess['trigger'] = 're_stay';
        }
        return $mess;
    }

    // Laravel\app\Console\Kernel.php でスケジューラーで深夜0:01以降に実行する
    // Ver2 でlogが残せるようにする際は廃止する
    public function auto_remove_before_today()
    {
        $res = DB::table('tumolink')
            ->where('updated_at', '<', Carbon::today())->delete();
        log::debug(print_r('Schedule method Tumolink@auto_remove_before_today run! delete record count>>> ' . $res , 1));
    }
}
