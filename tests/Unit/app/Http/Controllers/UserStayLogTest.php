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

    public function setup(): void
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
            ['DB_has' => true, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'deper' => null, 'posted' => $s60Sec ],
            'true_来訪1M前_前回チェックが60Min前' =>
            ['DB_has' => true, 'last_log_check' => $s60Min, 'c_u_id' => 1, 'deper' => null, 'posted' => $s60Sec ],
            'true_来訪1M前_前回チェックが90Min前' =>
            ['DB_has' => true, 'last_log_check' => $s90Min, 'c_u_id' => 1, 'deper' => null, 'posted' => $s60Sec ],
            'true_来訪1M前_前回チェックが100Min前' =>
            ['DB_has' => true, 'last_log_check' => $s100Min, 'c_u_id' => 1, 'deper' => null, 'posted' => $s60Sec ],

            'false_来訪1H前_前回チェックが60Sec前_基準値より前に来訪は判定しない' =>
            ['DB_has' => false, 'last_log_check' => $s60Sec, 'c_u_id' => 1, 'deper' => null, 'posted' => $s60Min ],

        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_stayCheck_test_arraival
     */
    public function stayCheck_のtest_arraivalの際_正しくlogが記録されるか($DB_has, $last_log_check, $c_u_id, $deper, $posted)
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
            'current_stay' => 1,
            'posted_at' => $posted,
        ]);

        $controller = app()->make('\App\Http\Controllers\UserStayLogController');
        $controller->stayCheck();

        $this->assertDatabaseHas('mac_addresses', [
            'community_user_id' => $c_u_id,
            'current_stay' => 1,
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
        $now          = new Carbon('2018-12-31');
        $s59Sec       = $now->copy()->subSecond(59)->toDateTimeString();
        $s60Sec       = $now->copy()->subSecond(60)->toDateTimeString();
        $s61Sec       = $now->copy()->subSecond(61)->toDateTimeString();
        $s60Min       = $now->copy()->subMinutes(60)->toDateTimeString();
        $s89Min       = $now->copy()->subMinutes(89)->toDateTimeString();
        $s89M59s      = $now->copy()->subMinutes(89)->subSeconds(50)->toDateTimeString();
        $s90Min       = $now->copy()->subMinutes(90)->toDateTimeString();
        $s90Min1sec   = $now->copy()->subMinutes(90)->subSeconds(1)->toDateTimeString();
        $s100Min      = $now->copy()->subMinutes(100)->toDateTimeString();
        $s120Min      = $now->copy()->subMinutes(120)->toDateTimeString();

        // 入力値と出力値を設定
        // .env.testing JUDGE_STAY_LOGS_DEPARTURE_SECOND=5400 （90分）基準で帰宅判定

        // assertDatabaseHas(テスト結果), last_log_check_datetime(設定table前回チェック時間)
        // mac.c_u_id,  mac.cur_stay, mac.arr_ar, mac.dep_at, mac.postes_at posted_at
        // log.
        return [

        ];
    }


    /**
     * 帰宅判定のtest指定時間以上で帰宅カラムに値が入るか確認する
     * @test
     */
    public function 帰宅判定のtest_指定時間以上で帰宅カラムに値が入るか確認するTest()
    {
        // システム内のlog確認時間を正常な1分前でセット
        $systemSetting = app()->make('\App\Service\SystemSettingService');
        $set = Carbon::now()->subSeconds(60)->toDateTimeString();
        $systemSetting->updateValue('last_log_check_datetime', $set);

        // 検証するロジック内で使用される帰宅時間の生成のロジック。testの境界となる時間を定義
        $limit = Carbon::now()->subSeconds(config("env.judge_stay_logs_departure_second"));

        // 来訪は4時間前を想定 （カラムnotnullなので値が必要）
        $arraival = $limit->copy()->subHours(4)->toDateTimeString();
        // 帰宅判定時間以上の最終更新時間を定義する
        $last = $limit->copy()->subSecond()->toDateTimeString();

        // 検証用データを作る
        factory(UserStayLog::class)->create([
            'community_user_id' => 2,
            'arraival_at' => $arraival,
            'last_datetime' => $last,
            // 検証カラム
            'departure_at' => null,
        ]);

        // メソッド実行 帰宅の判定がされるか？
        $controller = app()->make('\App\Http\Controllers\UserStayLogController');
        $controller->stayCheck();

        // 検証用のデータに帰宅時間が挿入されているか確認する
        $this->assertDatabaseHas('users_stays_logs', [
            'community_user_id' => 2,
            'arraival_at' => $arraival,
            'last_datetime' => $last,
            // 検証カラム
            'departure_at' => $limit->copy()->toDateTimeString(),
        ]);
    }

    /**
     * @test
     */
    public function 帰宅判定のtest_指定時間以内なら帰宅カラムに値が入らない事を確認するTest()
    {
        // システム内のlog確認時間を正常な1分前でセット
        $systemSetting = app()->make('\App\Service\SystemSettingService');
        $set = Carbon::now()->subSeconds(60)->toDateTimeString();
        $systemSetting->updateValue('last_log_check_datetime', $set);

        // 検証するロジック内で使用される帰宅時間の生成のロジック。testの境界となる時間を定義
        $limit = Carbon::now()->subSeconds(config("env.judge_stay_logs_departure_second"));

        // 来訪は4時間前を想定 （カラムnotnullなので値が必要）
        $arraival = $limit->copy()->subHours(4)->toDateTimeString();

        // 帰宅判定時間未満の最終更新時間を定義する
        $last = $limit->copy()->addSecond()->toDateTimeString();

        // 検証用データを作る
        factory(UserStayLog::class)->create([
            'community_user_id' => 2,
            'arraival_at' => $arraival,
            'last_datetime' => $last,
            // 検証カラム
            'departure_at' => null,
        ]);

        // メソッド実行 帰宅の判定はされないままか？
        $controller = app()->make('\App\Http\Controllers\UserStayLogController');
        $controller->stayCheck();

        // 検証用のデータに帰宅時間が挿入されていないことを確認する
        $this->assertDatabaseHas('users_stays_logs', [
            'community_user_id' => 2,
            'arraival_at' => $arraival,
            'last_datetime' => $last,
            // 検証カラム null のままか？
            'departure_at' => null,
        ]);
    }
}
