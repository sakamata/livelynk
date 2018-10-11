<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class CssFileDate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Thanks!! http://doop-web.com/blog/archives/1182
        // ファイルpathから更新日を出力、pathにパラメータを入れcssや画像のキャッシュクリアに利用
        $path = public_path();
        $filename = $path . '/css/livelynk.css';
        if (file_exists($filename)) {
            $file_date = date('YmdHis', filemtime($filename));
        } else {
            $file_date =  str_random(6);
        }
        View::share('file_date', $file_date);
        return $next($request);
    }
}
