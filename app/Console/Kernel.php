<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // withoutOverlappingをつけることで多重実行を防ぐ
        $schedule
            ->call('App\Http\Controllers\TaskController@auto_provisional_user_remove')
            // ->withoutOverlapping()
            // ->everyMinute();
            ->daily();

        // 未使用となった古い発話メッセージを削除する
        $schedule
            ->call('App\Http\Controllers\TaskController@noUseTalksMessageRemove')
            // ->withoutOverlapping()
            ->dailyAt('3:00');

        // 一定時間以上(30分)POSTの無いコミュニティの帰宅者判断を行う
        $schedule
            ->call('App\Http\Controllers\TaskController@taskDepartureCheck')
            ->everyThirtyMinutes();

        $schedule
            ->call('App\Http\Controllers\TumolinkController@auto_remove_before_today')
            // ->withoutOverlapping()
            ->dailyAt('0:01');

        $schedule
            ->call('App\Http\Controllers\UserStayLogController@stayCheck')
            // ->withoutOverlapping()
            ->everyMinute();

        // 天気APIの実行 深夜は止める
        $schedule
            ->call('App\Http\Controllers\API\WeatherCheckController@run')
            ->unlessBetween('0:00', '7:00')
            ->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
