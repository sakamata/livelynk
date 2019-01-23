<?php

namespace Tests\Browser;

use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Login_normal_user_Test extends DuskTestCase
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
    public function normalログインtest()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge')
                ->assertSee('ギークオフィス恵比寿')
                ->assertSee('ログイン')
                ->assertSee('ログインを保持する')
                ->type('#unique_name', 'bbb@bbb.com')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertPathIs('/')
                ->assertSee('ギークオフィス恵比寿')
                ->click('#nav-drawer')
                ->assertSeeLink('HOME')
                ->assertSeeLink('プロフィール編集')
                // 非表示項目
                ->assertDontSeeLink('新規ユーザー登録')
                ->assertDontSeeLink('仮ユーザー一覧')
                ->assertDontSeeLink('ユーザー一覧')
                ->assertDontSeeLink('デバイス一覧')
                ->assertDontSeeLink('ルーター一覧')
                ->assertDontSeeLink('コミュニティ編集')
                ->assertDontSeeLink('コミュニティ一覧')

                ->assertSeeLink('ギークオフィスWebサービス')
                ->assertSeeLink('ツモリンク')
                ->click('.action')
                ->assertSeeLink('ログアウト')
                ->click('.logout')
                ->assertSee('ログイン');
        });
    }
}
