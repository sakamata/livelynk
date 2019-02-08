<?php

namespace App\Http\Controllers;

use App\Tumolink;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TumolinkController extends Controller
{
    public function index()
    {
        $response = response()->json(\App\Tumolink::query()->get());
        return $response;
    }

    public function post(Request $request)
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
