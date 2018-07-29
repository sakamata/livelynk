<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // view URL は admin_users_edit としている
    public function index(Request $request)
    {
        $items = DB::table('users')->get();
        return view('admin.index',[
            'items' => $items,
        ]);
    }

}
