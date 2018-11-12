<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 全Controllerに共通して利用する処理を以下にまとめる

    // リーダーのIDを取得
    public function getReaderID()
    {
        $user = Auth::user();
        return $reader_id = DB::table('communities')
            ->where('id', $user->community_id)->value('user_id');
    }

    public function getReaderIDParam($community_id)
    {
        $user = Auth::user();
        return $reader_id = DB::table('communities')
            ->where('id', $community_id)->value('user_id');
    }

    // getのパラメーター path がDBに存在する communityのpathか判定
    // true なら該当する commyunity のtable record を返す
    public function GetCommunityFromPath($request_path)
    {
        // アクセスしてきた際のpathを取得し異常な値は撥ねる
        if (!preg_match("/^[a-zA-Z0-9]+$/", $request_path)) {
            return false;
        }
        // 半角英数の path ならDB見に行って match したコミュニティを返す
        $community = DB::table('communities')->where('url_path', $request_path)->first();
        if (!$community) {
            return false;
        }
        return $community;
    }

    public function roleNameToIdChange($role_name)
    {
        return DB::table('roles')
            ->where('role', $role_name)
        ->pluck('id')->first();
    }

    public function AuthUserSeter()
    {

        if (Auth::check()) {
            $community_id = session('community_id');
            // var_dump($community_id);


            foreach (Auth::user()->community_user as $key => $value) {
                // var_dump($key);
                var_dump($value);
                // var_dump($value->community_id);

                if ($value->community_id == $community_id) {
                    var_dump('hoge');
                    // community_user.id users.id どっちを id に入れるべきか！？？
                    Auth::user()->community_user_id = $value->id;
                    Auth::user()->role = $value->role;
                    Auth::user()->community_id = $value->community_id;
                }
            }


            // var_dump(Auth::user()->community_user[0]->role);
            // var_dump(Auth::user()->community_user[0]->id);
            // var_dump(Auth::user()->community_user[1]->role);
            // var_dump(Auth::user()->community_user[1]->id);
            // var_dump(Auth::user()->community_user[2]->role);
            // var_dump(Auth::user()->community_user[2]->id);
            // こうやれば従来通りの値取れる、ミドルウェアに書けばいけるかも
            // Auth::user()->role = $hoge_role;
            // Auth::user()->role = Auth::user()->community_user[1]->role;
            // log::debug(print_r($hoge_role,1));


        }


    }

}
