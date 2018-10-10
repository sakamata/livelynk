<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class UrlPath
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
        // ヘッダーメニューのリンクに利用する community path 情報 どの画面でも共通利用可能
        if (Auth::check()) {
            $user = Auth::user();
            $url_path = DB::table('communities')
            ->where('id', $user->community_id)->value('url_path');
        } else {
            // アクセスしてきた際のpathを取得し異常な値は撥ねる
            if (!preg_match("/^[a-zA-Z0-9]+$/", $request->path)) {
                $url_path = "";
            }
            // 半角英数の path ならDB見に行って match したコミュニティを返す
            $url_path = DB::table('communities')->where('url_path', $request->path)->value('url_path');
            if (!$url_path) {
                $url_path = "";
            }
        }
        View::share('url_path', $url_path);
        return $next($request);
    }
}
