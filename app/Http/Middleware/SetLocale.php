<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    /**
     * Carbon の曜日表示を日本語対応する為に設定
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        return $next($request);
    }
}
