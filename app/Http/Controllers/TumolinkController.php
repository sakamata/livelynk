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
            log::debug(print_r('hoge',1));
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
        // return bool
        $update = $this->tumolink_service->existsTodayPost($request->community_user_id, $column);
        if ($update) {
            // 同日中のpostであれば該当recordを更新する
            $this->tumolink_service->updateTime($request->community_user_id, $column, $time, $request->google_home_push);
        } else {
            // 新規ならrecord追加
            $res = $tumolist->save();
        }

        // 必要ならGoogleHome通知
        $community = DB::table('communities')
        ->where('id', Auth::user()->community_id)->first();
        if ( $request->google_home_push == true && $community->google_home_enable == true ) {
            // ***ToDo*** Google Home push method
        }

        if (!$request->isJson()) {
            // post form 処理
            return redirect('/')->with('message', 'ツモリ宣言をしました。');
        } else {
            // APIの場合のresponse処理
        }

    }
}
