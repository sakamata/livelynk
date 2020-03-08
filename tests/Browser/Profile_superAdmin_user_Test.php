<?php

namespace Tests\Browser;

use App\User;
use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Profile_superAdmin_user_Test extends DuskTestCase
{
    protected static $db_inited = false;
    use RefreshDatabase;

    protected static function initDB()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }
    }

    // 不本意だがひとまずベタに書く
    // superAdmin でのtest項目を書いた後、シュリンクした配下のroleのテストに転用
    // ***ToDo***
    //   文言編集に対応出来るようメタに書く( \resources\lang\ja\messages.php  に書いて呼び出す)
    //   https://readouble.com/laravel/5.6/ja/localization.html
    //   他のroleとの共通確認項目を共通で呼び出せるようにする

    /**
     * @test
     */
    public function superAdmin_selfプロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/admin_user/edit?id=1')
                ->assertSeeIn('.comp-title', 'プロフィール編集')
                ->assertSeeLink('パスワード変更')
                ->assertDontSeeLink('退会')
                ->assertSee('コミュニティID')
                ->assertSee('コミュニティコード')
                ->assertSee('コミュニティ名')
                ->assertSee('Livelynk全体管理者')
                ->assertSee('このユーザーは権限の変更ができません。')
                ->assertSee('デバイスメモ')
                ->assertSee('管理権限');
        });
    }

    /**
     * @test
     */
    public function superAdmin_selfプロフィール編集_空入力確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=1')
                ->type('name', '')
                //下までスクロール
                ->script("window.scrollTo(0, 1500);");

            $browser->press('ユーザー情報を更新')
                ->assertSeeIn('div.alert.alert-danger', 'エラー')
                ->assertSee('名前は必ず指定してください');
        });
    }

    /**
     * @test
     */
    public function superAdmin_selfプロフィール編集_文字数過剰バリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=1')
                ->type('name', '1234567890123456789012345678901')
                ->type('unique_name', '12345678901234567890123456789012345678901')
                ->type('email', 'a2345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901@a3456.com')
                ->type('mac_address[3][vendor]', '12345678901234567890123456789012345678901')
                ->type('mac_address[3][device_name]', '12345678901234567890123456789012345678901')
                //下までスクロール
                ->script("window.scrollTo(0, 1500);");
            $browser->press('ユーザー情報を更新')
                ->assertSeeIn('div.alert.alert-danger', 'エラー')
                ->assertSeeIn('div.alert.alert-danger', 'ユーザーIDは、40文字以下で指定してください。')
                ->assertSeeIn('div.alert.alert-danger', '名前は、30文字以下で指定してください。')
                ->assertSeeIn('div.alert.alert-danger', 'メールアドレスは、170文字以下で指定してください。')
                ->assertSeeIn('div.alert.alert-danger', '端末メーカーは、40文字以下にしてください。')
                ->assertSeeIn('div.alert.alert-danger', 'デバイスメモは、40文字以下にしてください。');
        });
    }

    /**
     * @test
     */
    public function superAdmin_selfプロフィール編集_異常文字形式バリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=1')
                ->type('unique_name', 'あいうえおかきくけこ')
                ->type('email', 'あいうえお@a3456.com')
                //下までスクロール
                ->script("window.scrollTo(0, 1500);");
            $browser->press('ユーザー情報を更新')
                ->assertSeeIn('div.alert.alert-danger', 'エラー')
                ->assertSeeIn('div.alert.alert-danger', 'ユーザーIDに正しい形式を指定してください。')
                ->assertSeeIn('div.alert.alert-danger', 'メールアドレスには、有効なメールアドレスを指定してください。');
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
    public function superAdmin_to_normal_and_Provision_プロフィール編集画面表示()
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
    public function superAdmin_selfプロフィール編集_正常編集確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=1')
                ->type('name', '編集後')
                ->type('unique_name', 'super_self_edit@laravel.com')
                ->type('email', 'super_self_edit@laravel.com')
                ->radio('hide', '1')
                ->type('mac_address[1][vendor]', 'edit_vendor')
                ->type('mac_address[1][device_name]', 'edit_device_name')
                ->check('mac_address[1][hide]')
                //下までスクロール
                ->script("window.scrollTo(0, 1500);");
            $browser->uncheck('mac_address[3][hide]')
                ->press('ユーザー情報を更新')
                ->assertPathIs('/admin_user');
            // 編集内容の確認
            $browser->visit('/admin_user/edit?id=1')
                ->assertInputValue('name', '編集後')
                ->assertInputValue('unique_name', 'super_self_edit@laravel.com')
                ->assertInputValue('email', 'super_self_edit@laravel.com')
                ->assertRadioSelected('hide', '1')
                ->assertInputValue('mac_address[1][vendor]', 'edit_vendor')
                ->assertInputValue('mac_address[1][device_name]', 'edit_device_name')
                ->assertChecked('mac_address[1][hide]');
        });
    }

    /**
     * @test
     */
    public function 後処理()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=1')
                ->assertSeeIn('.comp-title', 'プロフィール編集');
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
        });
    }
}
