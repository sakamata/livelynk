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

    public function setup()
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
        $community_user_id = 10;
        $date = Carbon::create(2018, 12, 31, 23, 59, 58);
        $service->arraivalInsertNow($community_user_id, $date);
        $this->assertDatabaseHas('users_stays_logs',[
            'community_user_id' => $community_user_id,
            'arraival_at' => $date,
            'departure_at' => null,
            'last_datetime' => $date,
        ]);
    }

    public function dataProvider_for_minute_validate() : array
    {
        return [
            '正常_0' => [0, 200],
            '正常_10' => [10, 200],
            '正常_50' => [50, 200],
            '異常_マイナス値' => [-10, 422],
            '異常_1' => [1, 422],
            '異常_小数値' => [1.5, 422],
            '異常_15' => [15, 422],
            '異常_51' => [51, 422],
            '異常_60' => [60, 422],
            '異常_int' => [99, 422],
            '異常_null' => ['', 422],
            '異常_文字列' => ['AAA', 422],
        ];
    }

    // * @dataProvider dataProvider_for_minute_validate

    /**
     * @test
     */
    public function UserStayLogServiceのtest_来訪中として異常な値のrecordは登録できない()
    {
        $service = app()->make('\App\Service\UserStayLogService');
        $community_user_id = 10;
        $date = Carbon::create(2018, 12, 31, 23, 59, 58);
        $response = $service->arraivalInsertNow($community_user_id, $date);
        $this->assertDatabaseHas('users_stays_logs',[
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
        $community_user_id = 11;
        $service->arraivalInsertNow($community_user_id, $date);
 
        $community_user_id = 12;
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

        $community_user_id = 11;
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
        $community_user_id = 11;
        $date = Carbon::create(2018, 12, 31, 12, 00, 00);
        
        $service->arraivalInsertNow($community_user_id, $date);
        
        $date2 = Carbon::create(2018, 12, 31, 12, 01, 01);
        $service->last_datetimeUpdate($community_user_id, $date2);

        // last_datetimeの値が前回と異なっているか確認
        $this->assertDatabaseMissing('users_stays_logs',[
            'community_user_id' => $community_user_id,
            'arraival_at' => $date,
            'departure_at' => null,
            'last_datetime' => $date,
        ]);

        // last_datetimeの値が更新されたものか確認
        $this->assertDatabaseHas('users_stays_logs',[
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
        $community_user_id = 11;
        $service->arraivalInsertNow($community_user_id, $now);
        
        $now2 =         Carbon::create(2018, 12, 31, 14, 59, 59);
        $departure_at = Carbon::create(2018, 12, 31, 13, 59, 59);
        $service->departurePastTimeUpdate($community_user_id, $now2, $departure_at);

        $this->assertDatabaseHas('users_stays_logs',[
            'community_user_id' => $community_user_id,
            'arraival_at' => $now,
            'departure_at' => $departure_at,
            'last_datetime' => $now2,
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
