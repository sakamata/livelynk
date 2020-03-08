<?php

namespace Tests\Unit\app\Http\Controllers;

use \Artisan;
use App\UserStayLog;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Service\SystemSettingService;

class SystemSettingServiceTest extends TestCase
{
    use RefreshDatabase;
    protected static $db_inited = false;
    public $now;

    protected static function initDB()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    public function setup(): void
    {
        parent::setUp();

        $this->now = Carbon::now();

        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }
    }

    /**
     * @test
     */
    public function SystemSettingServiceのset_keyが無ければ作成しvalueを挿入_あればset_valueを更新する()
    {
        $service = app()->make('\App\Service\SystemSettingService');
        $set_key = 'test_key_name';
        $set_value = Carbon::now();
        $set = $service->CreateKeyOrUpdate($set_key, $set_value);
        $this->assertEquals($set_key, $set->set_key);
        $this->assertEquals($set_value, $set->set_value);
    }

    /**
     * @test
     */
    public function SystemSettingServiceのtest_値を更新して取得できるか確認()
    {
        $service = app()->make('\App\Service\SystemSettingService');
        $set_key = 'test_key_name';
        $set_value = Carbon::now();
        $service->updateValue($set_key, $set_value);
        $get_value = $service->getValue($set_key);
        $this->assertEquals($set_value, $get_value);
    }

    /**
     * @test
     */
    public function SystemSettingServiceのtest_固有設定値last_log_check_datetimeが日時の型か確認()
    {
        $service = app()->make('\App\Service\SystemSettingService');
        $res = $service->getValue('last_log_check_datetime');
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($res)), $res);
    }
}
