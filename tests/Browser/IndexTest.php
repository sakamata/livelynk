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
use Carbon\Carbon;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class IndexTest extends DuskTestCase
{
    use RefreshDatabase;
    const COMMUNITY_ID = 1;
    const USER_ID = 1;
    const NAME = 'hoge';
    const SERVICE_NAME = 'テストコミュニティ';
    const SERVICE_NAME_READING = 'hoge';
    const URL_PATH = 'hoge';
    const HASH_KEY = 'hoge';

    protected function setUp()
    {
        parent::setUp();
        // Artisan::call('migrate:refresh');
        // Artisan::call('db:seed');

        // Carbon::setTestNow();
/*
        factory(Community::class)->create([
            'url_path' => self::URL_PATH,
            'service_name' => 'テストコミュニティ',
        ]);
        $user = factory(User::class)->create([
            'name' => 'hoge',
        ]);
        $this->actingAs($user)
            ->withSession([
                'community_id' => 1,
                'community_user_id' => 1,
            ]);

        factory(MacAddress::class)->create();
        factory(Router::class, 1)->create();
        factory(CommunityUser::class)->create();
        factory(CommunityUserStatus::class)->create();
        factory(Role::class)->create([
            'role' => 'normal',
        ]);
        factory(Role::class)->create([
            'role' => 'normalAdmin',
        ]);
        factory(Role::class)->create([
            'role' => 'readerAdmin',
        ]);
        factory(Role::class)->create([
            'role' => 'superAdmin',
        ]);
*/
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
    public function 未ログインで恵比寿_滞在者一覧画面閲覧()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?path=hoge')
                    ->assertSee('ギークオフィス恵比寿')
                    ->assertSee('今日のイベント')
                    ->assertSeeLink('ログイン');
        });
    }

    /**
     * @test
     */
    public function 未ログインで一般コミュニティ_滞在者一覧画面閲覧()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/?path=hoge2')
                ->assertSee('長い名前の人コミュニティ')
                ->assertDontSee('今日のイベント')
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
    public function ログインtest()
    {
        $this->browse(function ($browser)  {
            $browser->visit('/login/?path=hoge')
                ->type('unique_name', 'aaa@aaa.com')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertPathIs('/');
        });
    }
}
