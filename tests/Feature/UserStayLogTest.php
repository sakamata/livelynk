<?php

namespace Tests\Feature;

use \Artisan;
use App\UserTable;
use App\Community;
use App\CommunityUser;
use App\CommunityUserStatus;
use App\UserStayLog;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Generator as Faker;

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

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function insert_record_来訪のダミーデータが入るか検証()
    {
        $params = [
            'community_user_id' => 10,
            'arraival_at'       => $this->now,
            'departure_at'      => null,
            'last_datetime'     => $this->now,
            'created_at'        => $this->now,
            'updated_at'        => $this->now,
        ];
        factory(UserStayLog::class)->create($params);

        // 今登録したレコードがあるかを確認
        $this->assertDatabaseHas('users_stays_logs', $params);

    }

    /**
     * @test
     */
    public function insert_record_更新のダミーデータが入るか検証()
    {
        $params = [
            'community_user_id' => 10,
            'arraival_at'       => $this->now,
            'departure_at'      => null,
            'last_datetime'     => $this->now,
            'created_at'        => $this->now,
            'updated_at'        => $this->now,
        ];
        factory(UserStayLog::class)->create($params);

        // 今登録したレコードがあるかを確認
        $this->assertDatabaseHas('users_stays_logs', $params);

    }

    // 来訪状態になったユーザーを確認する

    // 仮ユーザーを作り端末を作る
    // 来訪と判断しarraival_at,last_datetimeに値が入る

    // 滞在中の仮ユーザーを作る
    // 来訪中と判断しlast_datetimeが更新される

    // 帰宅した仮ユーザーと端末,stay_logを作る
    // 帰宅と判断し departure_at に値が入る


}
