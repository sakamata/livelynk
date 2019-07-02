<?php

namespace Tests\Unit\app\Http\Controllers;

use \Artisan;
use App\UserStayLog;
use App\MacAddress;
use App\Http\Controllers\UserStayLogController;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Service\UserStayLogService;
use App\Service\MacAddressService;

class UserStayLogTest extends TestCase
{
    use RefreshDatabase;
    protected static $db_inited = false;
    public $now;

    protected static function initDB()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    } 

    public function setup()
    {
        parent::setUp();

        $this->now = Carbon::now();

        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }

    }

    // /**
    //  * @test
    //  */
    // public function 滞在中ユーザーの確認_objectが返る()
    // {
    //     $res = MacAddressService::GetCurrentStayCommunityUsers();
    //     $this->assertIsObject($res);
    // }

    // /**
    //  * @test
    //  */
    // public function 滞在中ユーザーの確認seederのid1が取得できる()
    // {
    //     $mac_address = factory(App\MacAddress::class)->make([
    //         'current_stay' => 1,
    //     ]);
    //     $res = MacAddressService::GetCurrentStayCommunityUsers();
    //     $this->assertContains($mac_address->current_stay, $res);
    // }

    // /**
    //  * @test
    //  */
    // public function 不在ユーザーの確認seederのid10は存在しない()
    // {
    //     $res = MacAddressService::GetCurrentStayCommunityUsers();
    //     $this->assertNotContains(10, $res);
    // }


    /**
     * @test
     */
    public function Controllerのtest_固有設定値last_log_check_datetimeに値を挿入できるか確認()
    {
        $controller = app()->make('\App\Http\Controllers\UserStayLogController');
        $now = Carbon::now();
        $controller->updateLastLogCheckDatetime();
        $this->assertDatabaseHas('systems_settings', [
            'set_key' => 'last_log_check_datetime',
            'set_value' => $now
        ]);
    }

    /**
     * @test
     */
    public function DB_を空にするdummy_test()
    {
        // $this->artisan('migrate:refresh', ['--seed' => '']);
        Artisan::call('migrate:refresh');
        $this->assertTrue(true);
    } 


}
