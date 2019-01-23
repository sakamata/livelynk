<?php

namespace Tests\Browser;

use App\User;
use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Login_superAdmin_user_Test extends DuskTestCase
{
    use RefreshDatabase;
    // protected function setUp()
    // {
    //     parent::setUp();
    //     Artisan::call('migrate:refresh');
    //     Artisan::call('db:seed');
    // }

    /**
     * @test
     */
    public function superAdminログイン()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge')
                ->assertSee('ギークオフィス恵比寿')
                ->assertSee('ログイン')
                ->assertSee('ログインを保持する')
                ->type('#unique_name', 'admin@aaa.com')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertPathIs('/')
                ->assertSeeIn('.comp-title', 'ギークオフィス恵比寿')
                ->assertSeeIn('div.comp-box-container.clearfix > div:nth-child(1) > div.name > div.text', 'red1-human')
                ->click('#nav-drawer')
                ->assertSeeLink('HOME')
                ->assertSeeLink('プロフィール編集')
                ->assertSeeLink('新規ユーザー登録')
                ->assertSeeLink('仮ユーザー一覧')
                ->assertSeeLink('ユーザー一覧')
                ->assertSeeLink('デバイス一覧')
                ->assertSeeLink('ルーター一覧')
                ->assertSeeLink('コミュニティ編集')
                ->assertSeeLink('コミュニティ一覧')
                ->assertSeeLink('ギークオフィスWebサービス')
                ->assertSeeLink('ツモリンク');
        });
    }

    /**
     * @test
     */
    public function superAdminログアウトtest()
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('ギークオフィス恵比寿')
                ->click('.action')
                ->assertSeeLink('ログアウト')
                ->click('.logout')
                ->assertSee('ログイン');
        });
    }
}
