<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\UserTable;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    // 一般ユーザーのメイン画面、滞在者の一覧を表示する
    public function index(Request $request)
    {
        $items = 'App\UserTable'::orderBy('last_access', 'desc')->get();
        return view('index.index', [
            'items' => $items,
        ]);
    }

    public function index2(Request $request)
    {
        $items = DB::table('users')
        ->join('mac_addresses', function($join){
            $join->on('users.id', '=', 'mac_addresses.user_id')
            ->where([
                ['hide', false],
                ['current_stay', true],
            ]);

        })->get();

        Log::debug(print_r($items, 1));


        return view('index.index2', [
            'items' => $items,
        ]);

    }

}
