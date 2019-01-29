<?php

namespace Tests\Browser;

use App\User;
use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Profile_normal_user_Test extends DuskTestCase
{
    use RefreshDatabase;
    protected function setUp()
    {
        parent::setUp();
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    /**
     * @test
     */
    public function normalログイン()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge2')
                ->assertSee('長い名前の人コミュニティ')
                ->assertSee('ログイン')
                ->assertSee('ログインを保持する')
                ->type('#unique_name', 'aaa2@aaa.com')
                ->type('password', 'aaaaaa')
                ->press('ログイン');
        });
    }

    /**
     * @test
     */
    public function normal_selfプロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(14))
                ->visit('/admin_user/edit?id=14')
                ->assertSeeIn('.comp-title', 'プロフィール編集')
                ->assertSeeLink('パスワード変更')
                ->assertSeeLink('退会')
                ->assertDontSee('コミュニティID')
                ->assertDontSee('コミュニティコード')
                ->assertDontSee('コミュニティ名')
                ->assertDontSee('Livelynk全体管理者')
                ->assertDontSee('コミュニティ管理者')
                ->assertDontSee('このユーザーは権限の変更ができません。')
                ->assertDontSee('管理権限');
        });
    }

    /**
     * @test
     */
    public function normal_selfプロフィール編集_空入力確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=14')
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
    public function normal_selfプロフィール編集_文字数過剰バリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=14')
                ->type('name', '1234567890123456789012345678901')
                ->type('unique_name', '12345678901234567890123456789012345678901')
                ->type('email', 'a2345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901@a3456.com')
                ->type('mac_address[14][vendor]', '12345678901234567890123456789012345678901')
                ->type('mac_address[14][device_name]', '12345678901234567890123456789012345678901')
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
    public function normal_selfプロフィール編集_異常文字形式バリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=14')
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
    public function normal_selfプロフィール編集_正常編集確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=14')
                ->type('name', '編集')
                ->type('unique_name', 'bbb2edit@laravel.com')
                ->type('email', 'bbb2edit@laravel.com')
                ->radio('hide', '1')
                ->type('mac_address[14][vendor]', 'edit_vendor')
                ->type('mac_address[14][device_name]', 'edit_device_name')
                ->check('mac_address[14][hide]')
                //下までスクロール
                ->script("window.scrollTo(0, 1500);");
            $browser
                // ->uncheck('mac_address[3][hide]')
                ->press('ユーザー情報を更新')
                ->assertPathIs('/admin_user/edit');
            // 編集内容の確認
            $browser->visit('/admin_user/edit?id=14')
                ->assertInputValue('name', '編集')
                ->assertInputValue('unique_name', 'bbb2edit@laravel.com')
                ->assertInputValue('email', 'bbb2edit@laravel.com')
                ->assertRadioSelected('hide', '1')
                ->assertInputValue('mac_address[14][vendor]', 'edit_vendor')
                ->assertInputValue('mac_address[14][device_name]', 'edit_device_name')
                ->assertChecked('mac_address[14][hide]')

                // 最初の状態に戻す
                ->type('name', '田中　寿限無寿限無一郎')
                ->type('unique_name', 'bbb2@aaa.com')
                ->type('email', 'bbb2@aaa.com')
                ->radio('hide', '0')
                ->type('mac_address[14][vendor]', 'Apple.inc')
                ->type('mac_address[14][device_name]', 'i-phoneX')
                ->uncheck('mac_address[14][hide]')
                //下までスクロール
                ->script("window.scrollTo(0, 1500);");
            $browser->press('ユーザー情報を更新');
        });
    }

    /**
     * @test
     */
    public function normal_to_superAdmin_プロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=1')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function normal_to_other_community_user_プロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=10')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function normal_to_readerAdmin_プロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=2')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function normal_to_normal_and_Provision_プロフィール編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin_user/edit?id=43')
                ->assertSee('このページは閲覧できません');
        });
    }
}
