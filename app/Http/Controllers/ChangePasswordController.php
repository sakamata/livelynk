<?php

namespace App\Http\Controllers;

use DB;
use App\CommunityUser;
use App\Role;
use App\Rules\NowPassword;
use App\Service\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    private $call_user;

    public function __construct(UserService $call_user)
    {
        $this->call_user = $call_user;
    }

    // superAdminは他人のパスワード変更ができる
    // readerAdminはコミュ内他人のパスワード変更が出来る
    // normalAdminはreaderAdmin以外の他人のパスワード変更が出来る
    public function edit(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $user = Auth::user();
        // superAdminを除き自分のコミュニティ以外は変更不可
        $taget_community_id = 'App\CommunityUser'::GetCommunityID($request->id);
        if ($user->role != 'superAdmin') {
            if ($user->community_id != $taget_community_id) {
                return view('errors.403');
            }
        }
        // normalは自分以外の変更不可
        if ($user->role == 'normal' && $user->id != $request->id) {
            return view('errors.403');
        }
        // 表示ユーザーのroleを取得
        $taget_role = $this->call_user->IDtoRoleGet($request->id);
        // normalAdminは上位の readerAdmin, superAdminは変更不可
        if ($user->role == 'normalAdmin') {
            if ($taget_role == 'readerAdmin' || $taget_role == 'superAdmin') {
                return view('errors.403');
            }
        }
        // readerAdminは上位の superAdminは変更不可
        if ($user->role == 'readerAdmin') {
            if ($taget_role == 'superAdmin') {
                return view('errors.403');
            }
        }
        $taget_role_int = 'App\Role'::where('role', $taget_role)->pluck('id')->first();
        $user_role_int = 'App\Role'::where('role', $user->role)->pluck('id')->first();
        $item = $this->call_user->PersonGet($request->id);
        return view('auth.passwords.edit', [
            'item' => $item,
            'taget_role_int' => $taget_role_int,
            'user_role_int' => $user_role_int,
        ]);
    }

    public function update(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $user = Auth::user();
        $taget_role = $this->call_user->IDtoRoleGet($request->id);
        $taget_role_int = 'App\Role'::where('role', $taget_role)->pluck('id')->first();
        $user_role_int = 'App\Role'::where('role', $user->role)->pluck('id')->first();

        if(Auth::user()->id == $request->id || $taget_role_int >= $user_role_int) {
            // 自身の変更の際は現在のpasswordが必要
            // オリジナルバリデートで、現在のPasswordが正しいか判定
            // app\Rules\NowPassword.php
            $request->validate([
                'now_password' => ['required', 'string','min:6', new NowPassword($request->id)],
                'password' => 'required|string|min:6|max:100|confirmed',
            ]);
        } else {
            // 上位管理者が更新の際は現在のpassword不要
            $request->validate([
                'password' => 'required|string|min:6|max:100|confirmed',
            ]);
        }

        $now = Carbon::now();
        // users tableの更新
        $param = [
            'password' => Hash::make($request->password),
            'updated_at' => $now,
        ];
        'App\UserTable'::where('id', $request->id)->update($param);
        return redirect("/admin_user/edit?id=". $request->id)->with('message', 'パスワードを変更しました。');
    }
}
