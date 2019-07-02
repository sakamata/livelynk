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
        $schedule
            ->call('App\Http\Controllers\TumolinkController@auto_remove_before_today')
            // ->withoutOverlapping()
            ->dailyAt('0:01');

        Log::warning(print_r('!!!schedule!!!UserStayLogController@stayCheck run!!', 1));
        $schedule
            ->call('App\Http\Controllers\UserStayLogController@stayCheck')
            // ->withoutOverlapping()
            ->everyMinute();

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
