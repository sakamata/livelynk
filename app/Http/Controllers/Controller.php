<?php

namespace App\Http\Controllers;

use DB;
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

    /**
     * ランダム文字列生成 (英数字)
     * $length: 生成する文字数
     */

    // リーダーのIDを取得
    public function getReaderID()
    {
        $user = Auth::user();
        return $reader_id = DB::table('communities')
            ->where('user_id', $user->community_id)->value('user_id');
    }
}
