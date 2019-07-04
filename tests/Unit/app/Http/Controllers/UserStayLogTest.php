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
use App\Service\SystemSettingService;

class UserStayLogTest extends TestCase
{
    use RefreshDatabase;
    // protected static $db_inited = false;
    public $now;

    // protected static function initDB()
    // {
    //     Artisan::call('migrate:refresh');
    //     Artisan::call('db:seed');
    // } 

    public function setup()
    {
        parent::setUp();
        $this->now = Carbon::now();
        // if (!static::$db_inited) {
        //     static::$db_inited = true;
        //     static::initDB();
        // }
    }

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

    public function dataProvider_for_stayCheck_test_arraival() :array
    {
        $now          = Carbon::create(2018, 12, 31, 12, 00, 00);
        $s59Sec       = Carbon::create(2018, 12, 31, 11, 59, 01);
        $s60Sec       = Carbon::create(2018, 12, 31, 11, 59, 00);
        $s61Sec       = Carbon::create(2018, 12, 31, 11, 58, 59);
        $s60Min       = Carbon::create(2018, 12, 31, 11, 00, 00);
        $s89M59s      = Carbon::create(2018, 12, 31, 10, 30, 01);
        $s90Min       = Carbon::create(2018, 12, 31, 10, 30, 00);
        $s90Min1sec   = Carbon::create(2018, 12, 31, 10, 29, 59);
        $s100Min      = Carbon::create(2018, 12, 31, 10, 20, 00);

        // 入力値と出力値を設定する
        // last_log_check_datetime
        // mac.c_u_id,  mac.cur_stay, mac.arr_ar, mac.dep_at, mac.postes_at posted_at
        // log.
        return [
            'true_来訪1M前_前回チェックが60Sec前' =>
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Sec, 'deper' => null, 'posted' => $s60Sec ],
            'true_来訪1M前_前回チェックが60Min前' =>
            ['DB_has' => true, 'last_log_check' => $s60Min, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Sec, 'deper' => null, 'posted' => $s60Sec ],
            'true_来訪1M前_前回チェックが90Min前' =>
            ['DB_has' => true, 'last_log_check' => $s90Min, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Sec, 'deper' => null, 'posted' => $s60Sec ],
            'true_来訪1M前_前回チェックが100Min前' =>
            ['DB_has' => true, 'last_log_check' => $s100Min, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Sec, 'deper' => null, 'posted' => $s60Sec ],

            'false_来訪1H前_前回チェックが60Sec前_基準値より前に来訪は判定しない' =>
            ['DB_has' => false, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Min, 'deper' => null, 'posted' => $s60Min ],

        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_stayCheck_test_arraival
     */
    public function stayCheck_のtest_arraivalの際_正しくlogが記録されるか($DB_has, $last_log_check, $c_u_id, $c_stay, $arrai, $deper, $posted)
    {
        // 前回記録時間のセット
        $systemSetting = app()->make('\App\Service\SystemSettingService');
        $systemSetting->updateValue('last_log_check_datetime', $last_log_check);
        $this->assertDatabaseHas('systems_settings', [
            'set_key' => 'last_log_check_datetime',
            'set_value' => $last_log_check,
        ]);

        factory(MacAddress::class)->create([
            'community_user_id' => $c_u_id,
            'current_stay' => $c_stay,
            'posted_at' => $posted,
        ]);

        $controller = app()->make('\App\Http\Controllers\UserStayLogController');
        $controller->stayCheck();

        $this->assertDatabaseHas('mac_addresses', [
            'community_user_id' => $c_u_id,
            'current_stay' => $c_stay,
            'posted_at' => $posted,
        ]);

        if ($DB_has) {
            $this->assertDatabaseHas('users_stays_logs', [
                'community_user_id' => $c_u_id,
                'arraival_at' => Carbon::now(),
                'departure_at' => $deper,
                'last_datetime' => Carbon::now(),
            ]);
        } else {
            $this->assertDatabaseMissing('users_stays_logs', [
                'community_user_id' => $c_u_id,
                'arraival_at' => Carbon::now(),
                'departure_at' => $deper,
                'last_datetime' => Carbon::now(),
                ]);
        }
    }


    public function dataProvider_for_stayCheck_test_update() :array
    {
        $now          = Carbon::now()->format('Y-m-d H:i:s');
        $s59Sec       = Carbon::create(2018, 12, 31, 11, 59, 01);
        $s60Sec       = Carbon::now()->subSecond(60)->format('Y-m-d H:i:s');
        $s61Sec       = Carbon::now()->subSecond(61)->format('Y-m-d H:i:s');
        $s60Min       = Carbon::now()->subMinutes(60)->format('Y-m-d H:i:s');
        $s89Min      = Carbon::now()->subMinutes(89)->format('Y-m-d H:i:s');
        $s89M59s      = Carbon::now()->subMinutes(89)->subSeconds(50)->format('Y-m-d H:i:s');
        $s90Min       = Carbon::now()->subMinutes(90)->format('Y-m-d H:i:s');
        $s90Min1sec   = Carbon::now()->subMinutes(90)->subSeconds(1)->format('Y-m-d H:i:s');
        $s100Min      = Carbon::now()->subMinutes(100)->format('Y-m-d H:i:s');
        $s120Min      = Carbon::now()->subMinutes(120)->format('Y-m-d H:i:s');

        
        // 入力値と出力値を設定
        // .env.testing JUDGE_STAY_LOGS_DEPARTURE_SECOND=5400 （90分）基準で帰宅判定
 
        // assertDatabaseHas(テスト結果), last_log_check_datetime(設定table前回チェック時間)
        // mac.c_u_id,  mac.cur_stay, mac.arr_ar, mac.dep_at, mac.postes_at posted_at
        // log.
        return [
            'true_更新1M前_前回チェックが60Sec前' =>
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Min, 'deper' => null, 'posted' => $s61Sec ],
            'true_更新1M前_前回チェックが60Min前' =>
            ['DB_has' => true, 'last_log_check' => $s60Min, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Min, 'deper' => null, 'posted' => $s61Sec ],
            'true_更新1M前_前回チェックが100Min前' =>
            ['DB_has' => true, 'last_log_check' => $s100Min, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s60Min, 'deper' => null, 'posted' => $s61Sec ],
            'true_更新89M_前回チェックが60Sec前' =>
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s100Min, 'deper' => null, 'posted' => $s89Min ],
            'true_更新89M59s_前回チェックが60Sec前' =>
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s100Min, 'deper' => null, 'posted' => $s89M59s ],

            'true_更新90M_over_帰宅判定_前回チェックが60Sec前' =>
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s100Min, 'deper' => $s90Min, 'posted' => $s90Min ],
            'true_更新90M1m_over_帰宅判定_前回チェックが60Sec前' =>
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s100Min, 'deper' => $s90Min1sec, 'posted' => $s90Min1sec ],
            'true_更新100M_over_帰宅判定_前回チェックが60Sec前' =>
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'c_stay' => 1, 'arrai' => $s100Min, 'deper' => $s100Min, 'posted' => $s100Min ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_stayCheck_test_update
     */
    public function stayCheck_のtest__posted_at_投稿時間での更新と帰宅判定_90分以上で帰宅判定か？updateの際_正しくlogが記録されるか($DB_has, $last_log_check, $c_u_id, $c_stay, $arrai, $deper, $posted)
    {
        // .env.testing JUDGE_STAY_LOGS_DEPARTURE_SECOND=5400 （90分）基準で帰宅判定
        // 前回記録時間のセット
        $systemSetting = app()->make('\App\Service\SystemSettingService');
        $systemSetting->updateValue('last_log_check_datetime', $last_log_check);
        $this->assertDatabaseHas('systems_settings', [
            'set_key' => 'last_log_check_datetime',
            'set_value' => $last_log_check,
        ]);

        factory(MacAddress::class)->create([
            'community_user_id' => $c_u_id,
            'arraival_at' => $arrai,
            'current_stay' => $c_stay,
            'posted_at' => $posted,
        ]);

        factory(UserStayLog::class)->create([
            'community_user_id' => $c_u_id,
            'arraival_at' => $arrai,
            'departure_at' => $deper,
            'last_datetime' => $posted,
        ]);

        $controller = app()->make('\App\Http\Controllers\UserStayLogController');
        $controller->stayCheck();

        if ($DB_has) {
            $this->assertDatabaseHas('users_stays_logs', [
                'community_user_id' => $c_u_id,
                'arraival_at' => $arrai,
                'departure_at' => $deper,
                'last_datetime' => $posted,
            ]);
        } else {
            // 規定時間以上でも更新の判定となっていないか確認
            $this->assertDatabaseMissing('users_stays_logs', [
                'community_user_id' => $c_u_id,
                'arraival_at' => $arrai,
                'departure_at' => $deper,
                'last_datetime' => $posted,
                ]);
        }
    }

}
