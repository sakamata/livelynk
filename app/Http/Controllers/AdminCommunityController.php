<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\AdminCommunity;

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
        $hash = $this->makeRandStr(32);
        return view('admin_community.add', [
            'hash' => $hash,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'community_name' => 'required|string|min:3|max:32',
            'service_name' => 'required|string|min:5|max:32',
            'url_path' => 'required|string|max:32',
            'ifttt_event_name' => 'string|max:191',
            'ifttt_webhook_key' => 'string|max:191',
        ]);
        $now = Carbon::now();
        // user_id は users tabelにinsert後に再度挿入する
        $param_community = [
            'enable' => true,
            'user_id' => null,
            'name' => $request['community_name'],
            'service_name' => $request['service_name'],
            'url_path' => $request['url_path'],
            'ifttt_event_name' => $request['ifttt_event_name'],
            'ifttt_webhooks_key' => $request['ifttt_webhooks_key'],
            'created_at' => $now,
            'updated_at' => $now,
        ];
        DB::beginTransaction();
        try {
            $community_id = DB::table('communities')->insertGetId($param_community);
            $param_user = [
                'name' => '未登録',
                'email' => $request['email'],
                'role' => 'readerAdmin',
                'password' => Hash::make($request['password']),
                'community_id' => $community_id,
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
            return redirect('/admin_community');
        }
    }

    public function edit(Request $request)
    {
        // code...
    }

    public function update(Request $request)
    {
        // code...
    }
}
