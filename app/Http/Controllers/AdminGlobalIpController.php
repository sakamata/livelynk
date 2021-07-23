<?php

namespace App\Http\Controllers;

use DB;
use App\Community;
use App\GlobalIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminGlobalIpController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // 一般管理者の場合は自コミュニティの端末のみを表示
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') {
            $items = GlobalIp::where('community_id', $user->community_id)
                ->paginate(25);
        }
        // superAdminは全て表示
        if ($user->role == 'superAdmin') {
            $items = GlobalIp::paginate(25);
        }
        return view('admin_global_ip.index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        $communities = Community::orderBy('id', 'desc')->get();
        $user = Auth::user();
        return view('admin_global_ip.create', [
            'communities' => $communities,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でrouter addを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        $request->validate([
            'community_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'global_ip' => ['required', 'ip', 'min:1', 'max:255'],
        ]);
        $now = Carbon::now();
        $param = [
            'community_id' => $request->community_id,
            'name' => $request->name,
            'global_ip' => $request->global_ip,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        DB::table('global_ips')->insert($param);
        return redirect('/admin_global_ip')->with('message', 'グローバルIP設定を追加しました。');
    }

    public function edit(Request $request)
    {
        // 不正なrequestは403へ飛ばす
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $item = GlobalIp::find($request->id);
        if (!$item) {
            return view('errors.403');
        }

        $user = Auth::user();
        // reader,normal管理者で自分のコミュニティと異なるrouterページ閲覧は撥ねる
        if (
            ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') && $item->community_id != $user->community_id
        ) {
            return view('errors.403');
        }

        $communities = DB::table('communities')->orderBy('id', 'desc')->get();
        $user = Auth::user();
        return view('admin_global_ip.edit', [
            'item' => $item,
            'user' => $user,
            'communities' => $communities,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'community_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'global_ip' => ['required', 'ip', 'min:1', 'max:255'],
        ]);

        $user = Auth::user();
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でrouter updateを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        $now = Carbon::now();
        $param = [
            'community_id' => $request->community_id,
            'name' => $request->name,
            'global_ip' => $request->global_ip,
            'updated_at' => $now,
        ];
        GlobalIp::where('id', $request->id)->update($param);
        return redirect('/admin_global_ip')->with('message', 'グローバルIP設定を編集しました。');
    }
}
