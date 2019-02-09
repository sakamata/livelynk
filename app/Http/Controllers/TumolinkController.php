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
        $tumolist->community_user_id = $request->json('community_user_id');
        $tumolist->maybe_arraival = $request->json('maybe_arraival');
        $tumolist->maybe_departure = $request->json('maybe_departure');
        $tumolist->google_home_push = $request->json('google_home_push');
        $res = $tumolist->save();
    }
}
