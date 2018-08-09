<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // マイグレーション時の Syntax error対策
        // 本番環境のMySQLのバージョンが古い為これより大きなカラム数に対応できない為
        // unique email 等の長さが MAX 255 -> 191となる
        Schema::defaultStringLength(191);
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
