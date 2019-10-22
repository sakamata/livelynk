<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
// use App\Http\Controllers\API\WeatherCheckController;

class WeatherCheckAPI extends Command
{
    // private $weatherCheck;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:weather';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'APIで天気を取得';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        // WeatherCheckController $weatherCheck
        )
    {
        parent::__construct();
        // $this->weatherCheck = $weatherCheck;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $this->weatherCheckAPI->run();
    }
}
