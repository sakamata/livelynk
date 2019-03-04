<?php

namespace Tests\Browser;

use \Artisan;
use App\Community;
use App\CommunityUser;
use App\CommunityUserStatus;
use App\MacAddress;
use App\User;
use App\Router;
use App\Role;
use App\Tumolink;
use Carbon\Carbon;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class IndexTest extends DuskTestCase
{
    protected static $db_inited = false;
    // 使うとDBtest前にrollbackがかかってしまう
    // use RefreshDatabase;

    protected static function initDB()
    {
        Artisan::call('migrate:refresh');
        // Artisan::call('db:seed');
        Artisan::call('db:seed', ['--class' => 'CommunitiesTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'CommunitiesUsersStatusesTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'CommunityUserTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'MacAddressesTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'RolesTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'RoutersTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'UsersTableSeeder']);
        // Tumolink Tableは後で検証
        // Artisan::call('db:seed', ['--class' => 'TumolinkTableSeeder']);
    } 

    public function setUp()
    {
        parent::setUp();
        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }
    }

    /**
     * @test
     */
    public function 未ログインでindexページ閲覧()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Geek Office');
        });
    }

    /**
     * @test
     */
    public function 未ログインで恵比寿_滞在者一覧画面閲覧_ツモリスト無し()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?path=hoge')
                    ->assertSee('ギークオフィス恵比寿')
                    ->assertSee('今日のイベント')
                    ->assertMissing('.tumolist')
                    ->assertSeeLink('ログイン');
        });
    }

    /**
     * @test
     */
    public function 未ログインで一般コミュニティ_滞在者一覧画面閲覧_ツモリスト無し()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?path=hoge2')
                ->assertSee('長い名前の人コミュニティ')
                ->assertDontSee('今日のイベント')
                ->assertMissing('.tumolist')
                ->assertSeeLink('ログイン');
        });
    }

    /**
     * @test
     */
    public function 未ログインで恵比寿_滞在者一覧画面閲覧_ツモリスト有り()
    {
        factory(Tumolink::class)->create([
            'community_user_id' => 4,
        ]);
        factory(Tumolink::class)->create([
            'community_user_id' => 5,
        ]);
        factory(Tumolink::class)->create([
            'community_user_id' => 30,
        ]);
        $this->browse(function (Browser $browser) {
            // $browser->pause(5000);
            $browser->visit('/?path=hoge')
            ->assertSee('ギークオフィス恵比寿')
            ->assertSee('今日のイベント')
            ->assertPresent('.tumolist')
            ->assertSeeLink('ログイン');
        });
        $this->assertDatabaseHas('tumolink', ['community_user_id' => 30]);
    }

    /**
     * @test
     */
    public function 未ログインで一般コミュニティ_滞在者一覧画面閲覧_ツモリスト有り()
    {
        factory(Tumolink::class)->create([
            'community_user_id' => 13,
        ]);
        $this->browse(function (Browser $browser) {
            $browser->visit('/?path=hoge2')
                ->assertSee('長い名前の人コミュニティ')
                ->assertDontSee('今日のイベント')
                ->assertPresent('.tumolist')
                ->assertSeeLink('ログイン');
        });
    }

    /**
     * @test
     */
    public function 未ログインで存在しないpathでコミュニティ_滞在者一覧画面閲覧()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?path=xxxx')
                ->assertSee('404 Not Found')
                ->assertSeeLink('ログイン');
        });
    }

    /**
     * @test
     */
    public function ログイン_バリデート_異常入力test()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge')
                ->type('unique_name', 'あいうえおかきくけこ')
                ->type('password', 'aaa')
                ->press('ログイン')
                ->assertSee('ユーザーIDに正しい形式を指定してください。')
                ->assertSee('パスワードは、6文字以上で指定してください。');
        });
    }

    /**
     * @test
     */
    public function ログイン_バリデート_文字数_less_test()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge')
                ->type('unique_name', 'aaa')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertSee('ユーザーIDは、6文字以上で指定してください。');
        });
    }

    /**
     * @test
     */
    public function ログイン_バリデート_文字数_gt_test()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge')
                ->type('unique_name', '12346578901234657890123465789012346578901')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertSee('ユーザーIDは、40文字以下で指定してください。');
        });
    }

    /**
     * @test
     */
    public function ログイン_バリデート_not_user_test()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge')
                ->type('unique_name', '1234657890123465789012346578901234657890')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertSee('ユーザーIDかPasswordが正しくありません');
        });
    }

    /**
     * @test
     */
    public function ログイン_正常_test()
    {
        $this->browse(function ($browser)  {
            $browser->visit('/login/?path=hoge')
                ->type('unique_name', 'aaa@aaa.com')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertSee('ギークオフィス恵比寿')
                ->assertPathIs('/');
        });
    }

    /**
     * @test
     */
    public function DB_を空にするdummy_test()
    {
        Artisan::call('migrate:refresh');
        $this->assertTrue(true);
    }
}
