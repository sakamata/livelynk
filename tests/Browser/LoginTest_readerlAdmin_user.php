<?php

namespace Tests\Browser;

use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest_readerAdmin_user extends DuskTestCase
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
    public function readerAdminログインtest()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge2')
                ->assertSee('長い名前の人コミュニティ')
                ->assertSee('ログイン')
                ->assertSee('ログインを保持する')
                ->type('#unique_name', 'admin2@aaa.com')
                ->type('password', 'aaaaaa')
                ->press('ログイン')
                ->assertPathIs('/')
                ->assertSee('長い名前の人コミュニティ')
                ->click('#nav-drawer')
                ->assertSeeLink('HOME')
                ->assertSeeLink('プロフィール編集')
                ->assertSeeLink('新規ユーザー登録')
                ->assertSeeLink('仮ユーザー一覧')
                ->assertSeeLink('ユーザー一覧')
                ->assertSeeLink('コミュニティ編集')

                ->assertDontSeeLink('コミュニティ一覧')

                ->click('.action')
                ->assertSeeLink('ログアウト')
                ->click('.logout')
                ->assertSee('ログイン');
        });
    }
}
