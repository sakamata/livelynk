<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    // view URL は admin_users_edit としている
    public function index(Request $request)
    {
        $items = DB::table('users')->get();
        return view('admin_user.index',[
            'items' => $items,
        ]);
    }

}
