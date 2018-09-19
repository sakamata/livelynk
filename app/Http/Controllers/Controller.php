<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 全Controllerに共通して利用する処理を以下にまとめる

    /**
     * ランダム文字列生成 (英数字)
     * $length: 生成する文字数
     */
    // Thanks! https://ameblo.jp/linking/entry-10289895826.html
    public function makeRandStr($length)
    {
        $r_str = "";
        $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z"'));
        for ($i = 0; $i < $length; $i++) {
            $r_str .= $str[rand(0, count($str)-1)];
        }
        return $r_str;
    }

}
