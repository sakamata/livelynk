<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\AdminRouter;

// normal userはrouter web.php 設定で閲覧不可となっている
class AdminRouterController extends Controller
{
    // ***ToDo*** communitiesIDでデフォルトフィルタリング
    public function index(Request $request)
    {
        $items = 'App\AdminRouter'::get();
        return view('admin_router.index',[
            'items' => $items,
        ]);
    }

    public function add(Request $request)
    {
        $communities = DB::table('communities')->orderBy('id', 'desc')->get();
        $hash = str_random(32);
        return view('admin_router.add', [
            'communities' => $communities,
            'hash' => $hash,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'community_id' => 'required|integer',
            'name' => 'required|string|max:100',
            'hash_key' => 'required|alpha_num|max:100',
        ]);
        $now = Carbon::now();
        $param = [
            'community_id' => $request->community_id,
            'name' => $request->name,
            'hash_key' => $request->hash_key,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        DB::table('routers')->insert($param);
        return redirect('/admin_router');
    }

    public function edit(Request $request)
    {
        // 不正なrequestはひとまず /へ飛ばす
        if (!$request->id || !ctype_digit($request->id)) {
            return redirect('/');
        }
        $item = 'App\AdminRouter'::where('id', $request->id)->first();
        if (!$item) {
            return redirect('/');
        }
        $communities = DB::table('communities')->orderBy('id', 'desc')->get();

        return view('admin_router.edit', [
            'communities' => $communities,
            'item' => $item,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'community_id' => 'required|integer',
            'name' => 'required|string|max:100',
            'hash_key' => 'required|alpha_num|max:100',
        ]);
        $now = Carbon::now();
        $param = [
            'community_id' => $request->community_id,
            'name' => $request->name,
            'hash_key' => $request->hash_key,
            'updated_at' => $now,
        ];
        DB::table('routers')->where('id', $request->id)->update($param);
        return redirect('/admin_router');
    }
}
