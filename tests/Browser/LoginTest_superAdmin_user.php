<?php

namespace Tests\Browser;

use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest_superAdmin_user extends DuskTestCase
{
    use RefreshDatabase;
    // protected function setUp()
    // {
    //     parent::setUp();
    //     Artisan::call('migrate:refresh');
    //     Artisan::call('db:seed');
    // }

    // 不本意だがひとまずベタに書く
    // superAdmin でのtest項目を書いた後、シュリンクした配下のroleのテストに転用
    // ***ToDo***
    //   文言編集に対応出来るようメタに書く( \resources\lang\ja\messages.php  に書いて呼び出す)
    //   https://readouble.com/laravel/5.6/ja/localization.html
    //   他のroleとの共通確認項目を共通で呼び出せるようにする
    //   seederファイルの refreshはブラウザテスト開始の際のみ行えるようにする

    /*
    仮ユーザーのログインテスト

    プロフィール編集画面のrole別表示確認
    プロフィール編集の
        バリテーションチェック
        編集後の画面確認
        パスワード変更のバリテーション
        編集後のパスワード再定義確認
     */

     
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
    public function superAdmin_selfプロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=1')
                ->assertSeeIn('.comp-title', 'プロフィール編集')
                ->assertSeeLink('パスワード変更')
                ->assertDontSeeLink('退会')
                ->assertSee('Livelynk全体管理者')
                ->assertSee('このユーザーは権限の変更ができません。')

                ->assertSee('管理権限');
            });
    }

    /**
     * @test
     */
    public function superAdmin_to_readerAdmin_プロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=2')
                ->assertSeeIn('.comp-title', 'プロフィール編集')
                ->assertSeeLink('パスワード変更')
                ->assertDontSeeLink('退会')
                ->assertSee('コミュニティ管理者')
                ->assertSee('このユーザーは権限の変更ができません。')

                ->assertSee('管理権限');
        });
    }

    /**
     * @test
     */
    public function superAdmin_to_normalAdmin_プロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=4')
                ->assertSeeIn('.comp-title', 'プロフィール編集')
                ->assertSeeLink('パスワード変更')
                ->assertSeeLink('退会')
                ->assertSee('委託管理者')
                ->assertDontSee('このユーザーは権限の変更ができません。')
                ->assertRadioSelected('role', 'normalAdmin')

                ->assertSee('管理権限');
        });
    }

    /**
     * @test
     */
    public function superAdmin_to_normal_プロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=5')
                ->assertSeeIn('.comp-title', 'プロフィール編集')
                ->assertSeeLink('パスワード変更')
                ->assertSeeLink('退会')
                ->assertSee('委託管理者')
                ->assertDontSee('このユーザーは権限の変更ができません。')
                ->assertRadioSelected('role', 'normal')

                ->assertSee('管理権限');
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
