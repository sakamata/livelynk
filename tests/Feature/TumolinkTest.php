<?php

namespace Tests\Feature;

use \Artisan;
use App\Tumolink;
use App\AuthUser;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class TumolinkTest extends TestCase
{
    protected static $db_inited = false;
    use RefreshDatabase;
    const INDEX_PATH = 'tumolink/index/?community_id=1';
    const POST_PATH = 'tumolink/post';

    protected static function initDB()
    {
        // $this->artisan('migrate:refresh', ['--seed' => '']);
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    } 

    public function setUp()
    {
        parent::setUp();
        // $this->artisan('db:seed', ['--class' => 'TumolinkTableSeeder']);
        // $this->artisan('db:seed', ['--class' => 'CommunityUserTableSeeder']);

        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }
    }

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETで200が返る()
    {
        $response = $this->get(self::INDEX_PATH);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETでJSONが返る()
    {
        $response = $this->get(self::INDEX_PATH);
        $this->assertThat($response->content(), $this->isJson());
    }

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETで返るJSONの形式が正しいか()
    {
        $response = $this->get(self::INDEX_PATH);
        $tumolists = $response->json();
        $tumolist = $tumolists[0];
        $this->assertSame([
            'id',
            'community_user_id',
            'maybe_arraival',
            'maybe_departure',
            'google_home_push',
            'created_at',
            'updated_at',
            'name',
            'name_reading',
            'provisional',
            'hide',
        ], array_keys($tumolist));
    }

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETでJSONが返る件数はSeederで作られたcoomu1の4件となる()
    {
        $response = $this->get(self::INDEX_PATH);
        $response->assertJsonCount(4);
    }

    /**
     * @test
     */
    public function 非ログインでツモリ_に値をJSON_POSTするとステータス401が返る()
    {
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => 4,
            'maybe_arraival' => $time,
            'maybe_departure' => $time,
            'google_home_push' => false
        ];
        $response = $this->postJson(self::POST_PATH, $params);
        // 401 認証が必要
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function ログイン状態でツモリ_に値をJSON_POSTするとステータス200が返る()
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => 4,
            'maybe_arraival' => $time,
            'maybe_departure' => $time,
            'google_home_push' => false
        ];
        $response = $this->postJson(self::POST_PATH, $params);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function 非ログインでツモリ_に値をJSON_POSTしても値は追加されない()
    {
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => 4,
            'maybe_arraival' => $time,
            'maybe_departure' => $time,
            'google_home_push' => false
        ];
        $this->postJson(self::POST_PATH, $params);
        $this->assertDatabaseMissing('tumolink', $params);
    }

    /**
     * @test
     */
    public function ログインでツモリ_に値をJSON_POSTするとtumolink_tableに値が追加される()
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => 4,
            'maybe_arraival' => $time,
            'maybe_departure' => $time,
            'google_home_push' => false
        ];
        $this->postJson(self::POST_PATH, $params);
        $this->assertDatabaseHas('tumolink', $params);
    }

    public function dataProvider_for_community_user_id_validate(): array
    {
        // [ $value, $error_code ],
        return [
            '正常_存在するid' => [4, 200],
            '異常_存在しないid' => [50, 422],
            '異常_null' => ['', 422],
            '異常_intではない' => [0, 422],
            '異常_intではない' => ['AAA', 422],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_community_user_id_validate
     */
    public function community_user_id_のJSON_POST_バリデートtest($value, $error_code)
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => $value,
            'maybe_arraival' => $time,
            'maybe_departure' => $time,
            'google_home_push' => false
        ];
        $response = $this->postJson(self::POST_PATH, $params);
        $response->assertStatus($error_code);
    }

    public function dataProvider_for_maybe_datetime_validate() : array
    {
        // [ $value, $error_code ],
        Carbon::setTestNow();
        return [
            '異常_1時間前'    => [Carbon::now()->subHour(1)->format('Y-m-d H:i:s'), 422],
            '異常_1秒前'    => [Carbon::now()->subSecond(1)->format('Y-m-d H:i:s'), 422],
            // test時間のタイムラグで60秒程以降で現在と認識する
            '異常_現在時刻' => [Carbon::now()->format('Y-m-d H:i:s'), 422],
            '正常_1分後'    => [Carbon::now()->addSecond(60)->format('Y-m-d H:i:s'), 200],
            '正常_1時間後'  => [Carbon::now()->addHour(1)->format('Y-m-d H:i:s'), 200],
            '正常_1日後'  => [Carbon::now()->addDay(1)->format('Y-m-d H:i:s'), 200],
            '正常_1月後'  => [Carbon::now()->addMonth(1)->format('Y-m-d H:i:s'), 200],
            '異常_int'      => [12345, 422],
            '異常_null'     => ['', 422],
            '異常_文字列'   => ['AAA', 422],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_maybe_datetime_validate
     */
    public function maybe_arraival_のJSON_POST_バリデートtest($value, $error_code)
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => 4,
            'maybe_arraival' => $value,
            'maybe_departure' => $time,
            'google_home_push' => false
        ];
        $response = $this->postJson(self::POST_PATH, $params);
        $response->assertStatus($error_code);
    }

    /**
     * @test
     * @dataProvider dataProvider_for_maybe_datetime_validate
     */
    public function maybe_departure_のJSON_POST_バリデートtest($value, $error_code)
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => 4,
            'maybe_arraival' => $time,
            'maybe_departure' => $value,
            'google_home_push' => false
        ];
        $response = $this->postJson(self::POST_PATH, $params);
        $response->assertStatus($error_code);
    }

    public function dataProvider_for_google_home_push_validate() : array
    {
        // [ $value, $error_code ],
        return [
            '正常_true' => [true, 200],
            '正常_false' => [false, 200],
            '正常_0' => [0, 200],
            '正常_1' => [1, 200],
            '正常_"0"' => ["0", 200],
            '正常_"1"' => ["1", 200],
            '異常_null' => [null, 422],
            '異常_int' => [12345, 422],
            '異常_文字列' => ['AAA', 422],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_google_home_push_validate
     */
    public function google_home_push_boolのJSON_POST_バリデートtest($value, $error_code)
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $time = Carbon::now()->addHour(5)->format('Y-m-d H:i:s');
        $params = [
            'community_user_id' => 4,
            'maybe_arraival' => $time,
            'maybe_departure' => $time,
            'google_home_push' => $value
        ];
        $response = $this->postJson(self::POST_PATH, $params);
        $response->assertStatus($error_code);
    }

    /**
     * @test
     */
    public function ツモリ_にパラメーター無しでJSON_POPSTすると422バリデーションエラーが返される()
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $params = [];
        $response = $this->postJson(self::POST_PATH, $params);
        $response->assertStatus(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function ツモリ_にcommunity_user_idのキーのみでJSON_POPSTすると422バリデーションエラーが返される()
    {
        $user = \App\AuthUser::where('id', 1)->first();
        $this->be($user);
        $params = ['community_user_id' => ''];
        $response = $this->postJson(self::POST_PATH, $params);
        $response->assertStatus(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
    }

        // post時間は指定した時間となっている
        // 0時間0分で行くPOSTすると時刻と異なるメッセージが表示される『いますぐ来るツモリ』
    // 行くツモリpost先を検証する
        // ***POST先は外部からのAPI_POSTと内部POSTで共通化が望ましい
        // API側は認証keyの検証のみして、値はそのままcontrollerのPOST先に内部的に投げる

    // 帰るツモリpostの値を検証する
        // post時間は指定した時間となっている
        // 0時間0分で帰るPOSTすると時刻と異なるメッセージが表示される『いますぐ帰るツモリ』
    // 帰るツモリpost先を検証する



    // 非ログインではツモリンク機能は表示されない
    // ログインするとメイン画面にツモリンク機能が表示される
        // 滞在していない場合は『行くツモリ』ボタンが押せる
        // 『行くツモリ』ボタンを押すと『ツモリスト』に予定時間と名前が表示される
        // 『行くツモリ』ボタンを押すと『ツモリスト』の人数がカウントされる

        // 行くツモリ宣言中にさらに『行くツモリ』ボタンが押せる
        // 滞在中に変更になると『行くツモリ』ボタンは押せない状態に変化する
        // 滞在中に変更になると『ツモリスト』から消える
        // 滞在中は行くツモリボタンは押せない
        // 帰宅するとツモリボタンが押せる状態に変化する

        // 不在中は『帰るツモリ』ボタンは押せない
        // 滞在中になると『帰るツモリ』ボタンが押せる状態に変化する
        // 滞在中は『帰るツモリ』ボタンが押せる
        // 『帰るツモリ』ボタンを押すと『帰るかもリスト』に予定時間と名前が表示される
        // 『帰るツモリ』ボタンを押すと『帰るかもリスト』の人数がカウントされる

        // 帰るツモリ宣言中にさらに『帰るツモリ』ボタンが押せる
        // 滞在から不在に変更になると『帰るツモリ』ボタンは押せない状態に変化する

        // GoogleHome通知ボタンは該当コミュニティ設定でONの際に表示される
        // GoogleHome通知ボタンは該当コミュニティ設定でOFFの際は非表示となる
        // GoogleHome通知ボタン表示中ONでツモリボタンを押すと通知フラグがONでPOSTされる
        // GoogleHome通知ボタン表示中OFFでツモリボタンを押すと通知フラグがOFFでPOSTされる


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
