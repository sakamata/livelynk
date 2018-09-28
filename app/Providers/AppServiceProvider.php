<?php

namespace App\Providers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        // マイグレーション時の Syntax error対策
        // 本番環境のMySQLのバージョンが古い為これより大きなカラム数に対応できない為
        // unique email 等の長さが MAX 255 -> 191となる
        Schema::defaultStringLength(191);

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
