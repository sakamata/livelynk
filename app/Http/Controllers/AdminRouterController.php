<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Router;
use App\Community;

// normal userはrouter web.php 設定で閲覧不可となっている
class AdminRouterController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // 一般管理者の場合は自コミュニティの端末のみを表示
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') {
            $items = 'App\Router'::Mycommunity($user->community_id)->paginate(25);
        }
        // superAdminは全て表示
        if ($user->role == 'superAdmin') {
            $items = 'App\Router'::paginate(25);
        }
        return view('admin_router.index',[
            'items' => $items,
        ]);
    }

    public function add(Request $request)
    {
        $communities = DB::table('communities')->orderBy('id', 'desc')->get();
        $hash = str_random(32);
        $user = Auth::user();
        return view('admin_router.add', [
            'communities' => $communities,
            'user' => $user,
            'hash' => $hash,
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ( $user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でrouter addを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        $request->validate([
            'community_id' => 'required|integer',
            'name' => 'required|string|max:32',
            'google_home_name' => 'nullable|string|max:100',
            'google_home_mac_address' => ['nullable', 'string', 'max:20', 'regex:/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/'],
        ]);
        $now = Carbon::now();
        $param = [
            'community_id' => $request->community_id,
            'name' => $request->name,
            'google_home_name' => $request->google_home_name,
            'google_home_mac_address' => $request->google_home_mac_address,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        DB::table('routers')->insert($param);
        return redirect('/admin_router')->with('message', 'ルーター設定を追加しました。');
    }

    public function edit(Request $request)
    {
        // 不正なrequestは403へ飛ばす
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $item = 'App\Router'::where('id', $request->id)->first();
        if (!$item) {
            return view('errors.403');
        }

        $user = Auth::user();
        // reader,normal管理者で自分のコミュニティと異なるrouterページ閲覧は撥ねる
        if (
            ( $user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) && $item->community_id != $user->community_id
        ) {
            return view('errors.403');
        }
        $google_home_enable = 'App\Community'::where('id', $item->community_id)->pluck('google_home_enable')->first();

        $communities = DB::table('communities')->orderBy('id', 'desc')->get();
        $user = Auth::user();
        return view('admin_router.edit', [
            'item' => $item,
            'user' => $user,
            'communities' => $communities,
            'google_home_enable' => $google_home_enable,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ( $user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でrouter updateを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        $request->validate([
            'community_id' => 'required|integer',
            'name' => 'required|string|max:32',
            'google_home_name' => 'nullable|string|max:100',
            'google_home_mac_address' => ['nullable', 'string', 'max:20', 'regex:/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/']
        ]);
        $now = Carbon::now();
        $param = [
            'community_id' => $request->community_id,
            'name' => $request->name,
            'google_home_name' => $request->google_home_name,
            'google_home_mac_address' => $request->google_home_mac_address,
            'updated_at' => $now,
        ];
        DB::table('routers')->where('id', $request->id)->update($param);
        return redirect('/admin_router')->with('message', 'ルーター設定を編集しました。');
    }
}
