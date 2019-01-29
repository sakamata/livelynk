<?php

namespace Tests\Browser;

use App\User;
use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Admin_Community_superAdmin_user_Test extends DuskTestCase
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
    public function superAdminログイン()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login/?path=hoge')
                ->assertSee('ギークオフィス恵比寿')
                ->assertSee('ログイン')
                ->assertSee('ログインを保持する')
                ->type('#unique_name', 'admin@aaa.com')
                ->type('password', 'aaaaaa')
                ->press('ログイン');
        });
    }

    /**
     * @test
     */
    public function superAdmin_community編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->assertSeeIn('h2', 'Community編集')
                ->assertSee('ギークオフィス恵比寿')
                ->assertSee('未登録 comm1 super')
                ->assertSee('http://whois.test/index?path=hoge')
                ->assertSee('Google Home アシスタント機能   :   有効')
                ->assertInputValue('service_name', 'ギークオフィス恵比寿')
                ->assertInputValue('service_name_reading', 'ギークオフィスえびす')
                ->assertInputValue('name', 'GeekOfficeEbisu')
                ->assertInputValue('hash_key', 'hoge')
                ->assertInputValue('ifttt_event_name', 'livelynk_local_dev')
                ->assertInputValue('ifttt_webhooks_key', '')
                ->assertRadioSelected('google_home_enable', '1')
                ->assertInputValue('admin_comment', '')

                ->assertSee('admin@aaa.com');
        });
    }

    /**
     * @test
     */
    public function superAdmin_community編集_空入力()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->type('service_name', 'ギークオフィス恵比寿')
                ->type('service_name_reading', 'ギークオフィスえびす')
                ->type('name', 'GeekOfficeEbisu')
                ->type('hash_key', 'hoge')
                ->type('ifttt_event_name', 'livelynk_local_dev')
                ->type('ifttt_webhooks_key', '')
                ->assertInputValue('admin_comment', '')

                ->assertSee('コミュニティ編集');
        });
    }

    /**
     * @test
     */
    public function superAdmin_communit編集_文字数過剰バリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->assertSee('コミュニティ編集');
        });
    }

    /**
     * @test
     */
    public function superAdmin_communit編集_異常文字形式バリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->assertSee('コミュニティ編集');
                
        });
    }

    /**
     * @test
     */
    public function superAdmin_communit編集_正常編集確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->assertSee('コミュニティ編集');
        });
    }
}
