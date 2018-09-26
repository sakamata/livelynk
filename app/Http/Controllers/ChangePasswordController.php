<?php

namespace App\Http\Controllers;

use DB;
use App\Rules\NowPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    public function edit(Request $request)
    {
        // superAdmin以外は他人のパスワード変更はできない
        $user = Auth::user();
        if ($user->role != 'superAdmin' && $user->id != $request->id) {
            return view('errors.403');
        }
        $item = 'App\UserTable'::where('id', $request->id)->first();
        return view('auth.passwords.edit', [
            'item' => $item,
        ]);
    }

    public function update(Request $request)
    {
        // オリジナルバリデートで、現在のPasswordが正しいか判定
        // app\Rules\NowPassword.php
        $request->validate([
            'now_password' => ['required', 'string','min:6', new NowPassword($request->id)],
            'password' => 'required|string|min:6|confirmed',
        ]);
        // superAdmin以外は他人のパスワード変更はできない
        $user = Auth::user();
        if ($user->role != 'superAdmin' && $user->id != $request->id) {
            log::warning(print_r("ユーザーが不正な値でpassword editを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }
        $now = Carbon::now();
        // users tableの更新
        $param = [
            'password' => Hash::make($request->password),
            'updated_at' => $now,
        ];
        'App\UserTable'::where('id', $request->id)->update($param);
        return redirect("/admin_user/edit?id=". $request->id);
    }
}
