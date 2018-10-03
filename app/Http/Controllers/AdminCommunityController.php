<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\AdminCommunity;

// normal userはrouter web.php 設定で閲覧不可となっている
class AdminCommunityController extends Controller
{
    public function index(Request $request)
    {
        $items = 'App\AdminCommunity'::get();
        return view('admin_community.index', [
            'items' => $items,
        ]);
    }

    public function add(Request $request)
    {
        $hash = str_random(32);
        return view('admin_community.add', [
            'hash' => $hash,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:170',
            'password' => 'required|string|min:6|confirmed',
            'name_id' => 'required|string|min:3|max:32',
            'service_name' => 'required|string|min:3|max:32',
            'url_path' => 'required|string|max:32',
            'ifttt_event_name' => 'nullable|string|max:191',
            'ifttt_webhook_key' => 'nullable|string|max:191',
        ]);
        $now = Carbon::now();
        // user_id は users tabelにinsert後に再度挿入する
        $param_community = [
            'enable' => true,
            'user_id' => null,
            'name' => $request->name_id,
            'service_name' => $request->service_name,
            'url_path' => $request->url_path,
            'ifttt_event_name' => $request->ifttt_event_name,
            'ifttt_webhooks_key' => $request->ifttt_webhooks_key,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        DB::beginTransaction();
        try {
            $community_id = DB::table('communities')->insertGetId($param_community);
            $email = $request->email;
            $login_id = $email . '@' . $community_id;
            $param_user = [
                'community_id' => $community_id,
                'name' => '未登録',
                'email' => $request->email,
                'role' => 'readerAdmin',
                'login_id' => $login_id,
                'password' => Hash::make($request->password),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            // insertした管理者のusers_idを取得 今作成したcommunityに入れる
            $user_id = DB::table('users')->insertGetId($param_user);
            DB::table('communities')->where('id', $community_id)
                ->update([ 'user_id' => $user_id ]);
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }
        if ($success) {
            return redirect('/admin_community')->with('message', 'コミュニティと管理者を作成しました。');
        }
    }

    public function edit(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $user = Auth::user();
        // superAdmin以外は自分のコミュニティ以外は撥ねる
        if ($user->role != 'superAdmin') {
            if ($user->community_id != $request->id) {
                return view('errors.403');
            }
        }

        $item = 'App\AdminCommunity'::where('id', $request->id)->first();
        if (!$item) {
            return redirect('/');
        }
        return view('admin_community.edit', [
            'item' => $item,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        // superAdmin以外は自分のコミュニティ以外は撥ねる
        if ($user->role != 'superAdmin') {
            if ($user->community_id != $request->id) {
                return view('errors.403');
            }
        }

        $request->validate([
            'enable' => 'required|boolean',
            'name' => 'required|string|min:3|max:32',
            'service_name' => 'required|string|min:3|max:32',
            'url_path' => 'required|string|max:32',
            'ifttt_event_name' => 'nullable|string|max:191',
            'ifttt_webhooks_key' => 'nullable|string|max:191',
        ]);
        $now = Carbon::now();
        $param = [
            'enable' => $request->enable,
            'name' => $request->name,
            'service_name' => $request->service_name,
            'url_path' => $request->url_path,
            'ifttt_event_name' => $request->ifttt_event_name,
            'ifttt_webhooks_key' => $request->ifttt_webhooks_key,
            'updated_at' => $now,
        ];
        DB::table('communities')->where('id', $request->id)->update($param);

        if ($user->role != 'superAdmin') {
            return redirect('/admin_community/edit?id=' . $user->community_id)->with('message', 'コミュニティを編集しました。');
        } else {
            return redirect('/admin_community')->with('message', 'コミュニティを編集しました。');
        }
    }
}
