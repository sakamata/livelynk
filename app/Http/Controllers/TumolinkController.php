<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Requests\TumolinkPost;
use App\Service\TumolinkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TumolinkController extends Controller
{
    private $tumolink_service;

    public function __construct(TumolinkService $tumolink_service)
    {
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
        $now = Carbon::now();
        $time = $now->addHour($hour)->addSecond($minute * 60);
        $direction = $request->direction;
        if ($direction == 'arriving') {
            $tumolist->maybe_arraival = $time;
            $column = 'maybe_arraival';
        } else {  //   == 'leaving'
            $tumolist->maybe_departure = $time;
            $column = 'maybe_departure';
        }
        // cancelなら処理先移行
        if ($request->action == 'cancel') {
            $this->cancel($request, $column);
            $message = 'ツモリをキャンセルしました。';
        } else {
            // tumolink tableは 1ユーザーにつき1日1recordとなる仕様
            $exists = $this->tumolink_service->existsTodayPost($request->community_user_id);
            if ($exists) {
            // 同日中のpostであれば該当recordを更新する
                $this->tumolink_service->updateTime($request->community_user_id, $column, $time, $request->google_home_push);
            } else {
            // 新規ならrecord追加
                $res = $tumolist->save();
            }
            $message = 'ツモリ宣言をしました。';

            // 必要ならGoogleHome通知
            $community = DB::table('communities')
                ->where('id', Auth::user()->community_id)->first();
            if ($request->google_home_push == true && $community->google_home_enable == true) {
            // ***ToDo*** Google Home push method
            }
        }
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

    // Laravel\app\Console\Kernel.php でスケジューラーで深夜0:01以降に実行する
    // Ver2 でlogが残せるようにする際は廃止する
    public function auto_remove_before_today()
    {
        $res = DB::table('tumolink')
        ->where(function ($query) {
            $query->where('maybe_arraival', '<', Carbon::today())
            ->orWhere('maybe_departure', '<', Carbon::today());
        })->delete();
        log::debug(print_r('Schedule method Tumolink@auto_remove_before_today run! delete record count>>> ' . $res , 1));
    }
}
