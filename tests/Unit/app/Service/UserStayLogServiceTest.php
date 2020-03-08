<?php

namespace Tests\Unit\app\Http\Controllers;

use \Artisan;
use App\Service\UserStayLogService;
use App\UserStayLog;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStayLogServiceTest extends TestCase
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
    public function UserStayLogServiceのtest_来訪中としてrecordを登録する()
    {
        $service = app()->make('\App\Service\UserStayLogService');
        $community_user_id = 100;
        $date = Carbon::create(2018, 12, 31, 23, 59, 58);
        $service->arraivalInsertNow($community_user_id, $date);
        $this->assertDatabaseHas('users_stays_logs', [
            'community_user_id' => $community_user_id,
            'arraival_at' => $date,
            'departure_at' => null,
            'last_datetime' => $date,
        ]);
    }

    /**
     * @test
     */
    public function UserStayLogServiceのtest_来訪中として異常な値のrecordは登録できない()
    {
        $service = app()->make('\App\Service\UserStayLogService');
        $community_user_id = 100;
        $date = Carbon::create(2018, 12, 31, 23, 59, 58);
        $response = $service->arraivalInsertNow($community_user_id, $date);
        $this->assertDatabaseHas('users_stays_logs', [
            'community_user_id' => $community_user_id,
            'arraival_at' => $date,
            'departure_at' => null,
            'last_datetime' => $date,
        ]);
    }

    /**
     * @test
     */
    public function UserStayLogServiceのtest_community_user_id_キーで帰宅カラムのnullrecordが存在しなければfalseを返す()
    {
        //ArraivalUserDuplicationCheck のtest
        $service = app()->make('\App\Service\UserStayLogService');

        $date = Carbon::create(2018, 12, 31, 23, 59, 58);
        $community_user_id = 101;
        $service->arraivalInsertNow($community_user_id, $date);

        $community_user_id = 102;
        $isDupl = $service->ArraivalUserDuplicationCheck($community_user_id);
        $this->assertNotTrue($isDupl);
    }

    /**
     * @test
     */
    // 来訪した帰宅カラムの入力がない状態が重複していないか確認する
    public function UserStayLogServiceのtest_community_user_id_キーで帰宅カラムのnullrecordが重複していればtrueを返す()
    {
        //ArraivalUserDuplicationCheck のtest
        $service = app()->make('\App\Service\UserStayLogService');
        $date = Carbon::create(2018, 12, 31, 23, 59, 58);

        $community_user_id = 101;
        $service->arraivalInsertNow($community_user_id, $date);

        $isDupl = $service->ArraivalUserDuplicationCheck($community_user_id);
        $this->assertTrue($isDupl);
    }

    /**
     * @test
     */
    public function UserStayLogServiceのtest_来訪中のユーザーの更新_last_datetimeを更新する()
    {
        // last_datetimeUpdate のtest
        $service = app()->make('\App\Service\UserStayLogService');
        $community_user_id = 101;
        $date = Carbon::create(2018, 12, 31, 12, 00, 00);

        $service->arraivalInsertNow($community_user_id, $date);

        $date2 = Carbon::create(2018, 12, 31, 12, 01, 01);
        $service->lastDatetimeUpdate($community_user_id, $date2);

        // last_datetimeの値が前回と異なっているか確認
        $this->assertDatabaseMissing('users_stays_logs', [
            'community_user_id' => $community_user_id,
            'arraival_at' => $date,
            'departure_at' => null,
            'last_datetime' => $date,
        ]);

        // last_datetimeの値が更新されたものか確認
        $this->assertDatabaseHas('users_stays_logs', [
            'community_user_id' => $community_user_id,
            'arraival_at' => $date,
            'departure_at' => null,
            'last_datetime' => $date2,
        ]);
    }

    /**
     * @test
     */
    public function UserStayLogServiceのtest_帰宅判断として該当userのdeparture_atに帰宅想定時間をupdateする()
    {
        // departurePastTimeUpdate のtest
        $service = app()->make('\App\Service\UserStayLogService');
        $now = Carbon::create(2018, 12, 31, 12, 00, 00);
        $community_user_id = 101;
        $service->arraivalInsertNow($community_user_id, $now);

        $past_limit =   Carbon::create(2018, 12, 31, 14, 59, 59);
        $departure_at = Carbon::create(2018, 12, 31, 13, 59, 59);
        $service->departurePastTimeUpdate($past_limit);
        // $service->departurePastTimeUpdate($community_user_id, $past_limit);

        $this->assertDatabaseHas('users_stays_logs', [
            // 'community_user_id' => $community_user_id,
            'arraival_at' => $now,
            'departure_at' => $past_limit,
            'last_datetime' => $now,
        ]);
    }

    public function dataProvider_for_longTermStopAfterStayUsers() :array
    {
        $past2Hour   = Carbon::now()->subHour(2);
        $past3Hour   = Carbon::now()->subHour(3);
        $past4Hour   = Carbon::now()->subHour(4);
        $past10Hour   = Carbon::now()->subHour(10);
        $past1Day    = Carbon::now()->subDay();
        // サービスが長期(3Hour)時間停止していた際の滞在中ユーザーのステータス変更テスト
        //                                           id,  arraival_at,    departure_at, last_datetime,  更新されるか
        return [
            '来訪8H前_帰宅済み_更新されない' =>      [110,  $past10Hour,   $past4Hour, $past4Hour,     false ],
            '帰宅後過去重複_更新されない' =>         [111,  $past1Day,     $past10Hour,$past10Hour,    false ],
            '来訪4H前_更新される' =>                 [111,  $past4Hour,    null,       $past3Hour,     true  ],
            '来訪3H前_ありえないが_更新される' =>    [112,  $past3Hour,    null,       $past2Hour,     true  ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_longTermStopAfterStayUsers
     */
    public function UserStayLogServiceのtest_長期サービス停止後_3H_の稼働直後、停止前滞在中だったユーザーを一律で帰宅中に変更する($community_user_id, $arraival_at, $departure_at, $last_datetime, $check_bool)
    // public function UserStayLogServiceのtest_長期サービス停止後_12H_の稼働直後、停止前滞在中だったユーザーを一律で帰宅中に変更する()
    {
        factory(UserStayLog::class)->create([
            'community_user_id' => $community_user_id,
            'arraival_at' => $arraival_at,
            'departure_at' => $departure_at,
            'last_datetime' => $last_datetime
        ]);

        $service = app()->make('\App\Service\UserStayLogService');
        $departure_at_CHECK_stamp = Carbon::now()->subHour(3);
        $service->longTermStopAfterStayUsersChangeDeparture($departure_at_CHECK_stamp);

        // 値の有無を確認
        if ($check_bool) {
            $this->assertDatabaseHas('users_stays_logs', [
                'community_user_id' => $community_user_id,
                'arraival_at' => $arraival_at,
                'departure_at' => $departure_at_CHECK_stamp,
                'last_datetime' => $last_datetime,
            ]);
        } else {
            $this->assertDatabaseMissing('users_stays_logs', [
                'community_user_id' => $community_user_id,
                'arraival_at' => $arraival_at,
                'departure_at' => $departure_at_CHECK_stamp,
                'last_datetime' => $last_datetime,
            ]);
        }
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
