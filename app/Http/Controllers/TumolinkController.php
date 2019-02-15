<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Requests\TumolinkPost;
use App\Service\TumolinkService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        if (!$request->json('community_user_id')) {
            return response()->json([], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $tumolist = new \App\Tumolink();
        $hour = intval($request->json('hour'));
        $minute = intval($request->json('minute'));
        $now = Carbon::now();
        $time = $now->addHour($hour)->addSecond($minute * 60);
        $direction = $request->json('direction');
        if ($direction == 'arriving') {
            $tumolist->maybe_arraival = $time;
        } elseif ($direction == 'leaving') {
            $tumolist->maybe_departure = $time;
        } else {
            // 例外処理
        }
        $tumolist->community_user_id = $request->json('community_user_id');
        $tumolist->google_home_push = $request->json('google_home_push');
        $res = $tumolist->save();
    }
}
