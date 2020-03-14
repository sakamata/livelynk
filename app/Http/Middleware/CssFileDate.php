<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class CssFileDate
{
    /**
     * ファイルpathから更新日を出力、pathにパラメータを入れcssや画像のキャッシュクリアに利用
     * @url http://doop-web.com/blog/archives/1182 Thanks!!
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $files = [
            'app_js'        => '/js/app.js',
            'livelynk_js'   => '/js/livelynk.js',
            'app_css'       => '/css/app.css',
            'livelynk_css'  => '/css/livelynk.css',
            'tumolink_css'  => '/css/tumolink_.css',
        ];
        $path = public_path();
        $file_date = [];
        foreach ($files as $key => $file) {
            $filename = $path . $file;
            if (file_exists($filename)) {
                $file_date[$key] = date('YmdHis', filemtime($filename));
            } else {
                $file_date =  str_random(6);
            }
            View::share('file_date', $file_date);
        }
        return $next($request);
    }
}
