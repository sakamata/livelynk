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
}
