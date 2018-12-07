<?php

namespace App\Http\Controllers;

use DB;
use App\Community;
use App\Service\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// normal userはrouter web.php 設定で閲覧不可となっている
class AdminCommunityController extends Controller
{
    private $call_user;

    public function __construct(UserService $call_user) {
        $this->call_user = $call_user;
    }

    public function index(Request $request)
    {
        $items = 'App\Community'::paginate(25);
        return view('admin_community.index', [
            'items' => $items,
        ]);
    }

    public function add(Request $request)
    {
        $url_path = str_random(32);
        $secret = str_random(32);
        return view('admin_community.add', [
            'url_path' => $url_path,
            'secret' => $secret,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|min:3|max:30',
            'email' => 'nullable|string|email|max:170',
            'password' => 'required|string|min:6|max:100|confirmed',
            'unique_name' => ['required', 'string', 'min:6', 'max:40', 'regex:/^[a-zA-Z0-9@_\-.]{6,40}$/u', 'unique:users'],
            'name' => 'required|string|alpha_dash|min:3|max:32|unique:communities',
            'service_name' => 'required|string|min:3|max:32',
            'url_path' => 'required|string|max:32|unique:communities',
            'hash_key' => 'required|alpha_num|min:4|max:32',
            'ifttt_event_name' => 'nullable|string|max:191',
            'ifttt_webhook_key' => 'nullable|string|max:191',
            'google_home_name' => 'nullable|string|max:100',
            'google_home_mac_address' => ['nullable', 'string', 'max:20', 'regex:/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/']
        ]);
        $now = Carbon::now();
        // user_id は users tabelにinsert後に再度挿入する
        $param_community = [
            'enable' => true,
            'user_id' => null,
            'name' => $request->name,
            'service_name' => $request->service_name,
            'hash_key' => $request->hash_key,
            'url_path' => $request->url_path,
            'ifttt_event_name' => $request->ifttt_event_name,
            'ifttt_webhooks_key' => $request->ifttt_webhooks_key,
            'google_home_name' => $request->google_home_name,
            'google_home_mac_address' => $request->google_home_mac_address,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        DB::beginTransaction();
        try {
            $community_id = DB::table('communities')->insertGetId($param_community);
            // role_id "readerAdmin" = 3 に固定
            $user_id = $this->call_user->UserCreate(
                (string)$request->user_name,
                (string)$request->unique_name,
                (string)$request->email,
                (bool)$provisional = false,
                (string)$request->password,
                (int)$community_id,
                (int)$role_id = 3,
                (string)$action = 'AdminCommunityCreate'
            );
            DB::table('communities')->where('id', $community_id)
                ->update(['user_id' => $user_id]);
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
            return redirect()->back()->with('message', 'コミュニティを作成できませんでした。もう一度試してみてください。');
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

        $item = 'App\Community'::where('id', $request->id)->first();
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
            'name' => 'required|string|alpha_dash|min:3|max:32',
            'service_name' => 'required|string|min:3|max:32',
            'url_path' => 'required|string|max:32',
            'hash_key' => 'required|alpha_num|min:4|max:32',
            'ifttt_event_name' => 'nullable|string|max:191',
            'ifttt_webhooks_key' => 'nullable|string|max:191',
            'google_home_name' => 'nullable|string|max:100',
            'google_home_mac_address' => ['nullable', 'string', 'max:20', 'regex:/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/']
        ]);
        $now = Carbon::now();
        $param = [
            'enable' => $request->enable,
            'name' => $request->name,
            'service_name' => $request->service_name,
            'url_path' => $request->url_path,
            'hash_key' => $request->hash_key,
            'ifttt_event_name' => $request->ifttt_event_name,
            'ifttt_webhooks_key' => $request->ifttt_webhooks_key,
            'google_home_name' => $request->google_home_name,
            'google_home_mac_address' => $request->google_home_mac_address,
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
