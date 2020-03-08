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

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    // ->dump($browser);
              ->assertSee('Livelynk');
        });
    }

    /**
     * @test
     */
    public function superAdmin_community編集画面表示()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('admin_community/edit?id=1')
                ->assertSeeIn('h2', 'Community編集')
                ->assertSee('ギークオフィス恵比寿')
                ->assertSee('未登録 comm1 super')
                ->assertSee(env('APP_URL') .'/index?path=hoge')
                ->assertSee('Google Home アシスタント機能   :   有効')
                ->assertInputValue('service_name', 'ギークオフィス恵比寿')
                ->assertInputValue('service_name_reading', 'ギークオフィスえびす')
                ->assertInputValue('name', 'GeekOfficeEbisu')
                ->assertInputValue('hash_key', 'hoge')
                ->assertInputValue('ifttt_event_name', 'livelynk_local_dev')
                ->assertInputValue('ifttt_webhooks_key', env('IFTTT_WEBHOOKS_KEY_SEED'))
                ->assertRadioSelected('google_home_enable', '1')
                ->assertInputValue('admin_comment', '')

                ->assertSee('admin@aaa.com');
        });
    }

    /**
     * @test
     */
    public function superAdmin_community編集_そのまま編集確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->script("window.scrollTo(0, 1200);");
            $browser->press('編集')
                ->assertPathIs('/admin_community')
                ->assertSeeIn('#app > div', 'コミュニティを編集しました。');
        });
    }


    /**
     * @test
     */
    public function superAdmin_community編集_空入力()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->type('service_name', '')
                ->type('service_name_reading', '')
                ->type('name', '')
                ->type('url_path', '')
                ->type('hash_key', '')
                ->script("window.scrollTo(0, 600);");
            $browser->type('ifttt_event_name', '')
                ->type('ifttt_webhooks_key', '')
                ->radio('google_home_enable', '1')
                ->type('admin_comment', '');
            $browser->press('編集')
                ->assertSeeIn('div.alert.alert-danger', 'エラー')
                ->assertSee('名前は必ず指定してください。')
                ->assertSee('コミュニティ名称は必ず指定してください。')
                ->assertSee('コミュニティ名称は必ず指定してください。')
                ->assertSee('url pathは必ず指定してください。')
                ->assertSee('secretは必ず指定してください。');
        });
    }

    /**
     * @test
     */
    public function superAdmin_community編集_文字数過剰バリデート確認()
    {
        $text_33 = '123456789012345678901234567890123';
        $text_65 = '12345678901234567890123456789012345678901234567890123456789012345';
        $text_192 = '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012';
        $text_1001 = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901';

        $text = array(
            '33' => $text_33,
            '65' => $text_65,
            '192' => $text_192,
            '1001' => $text_1001,
        );
        $this->browse(function ($browser) use ($text) {
            $browser->visit('admin_community/edit?id=1')
                ->type('service_name', $text['33'])
                ->type('service_name_reading', $text['65'])
                // view側で pattern="^\w{3,32}$" 有りの為コメントアウト
                // ->type('name', $text['33'])
                ->type('url_path', $text['65'])
                ->type('hash_key', $text['65'])
                ->script("window.scrollTo(0, 600);");
            $browser->type('ifttt_event_name', $text['192'])
                ->type('ifttt_webhooks_key', $text['192'])
                ->radio('google_home_enable', '1')
                ->type('admin_comment', $text['1001']);
            $browser->press('編集')
                ->assertSeeIn('div.alert.alert-danger', 'エラー')
                ->assertSee('url pathは、64文字以下で指定してください。')
                ->assertSee('secretは、64文字以下で指定してください。')
                ->assertSee('IFTTT Event Nameは、191文字以下で指定してください。')
                ->assertSee('IFTTT Webhooks keyは、191文字以下で指定してください。')
                ->assertSee('admin commentは、1000文字以下で指定してください。');
        });
    }

    /**
     * @test
     */
    public function superAdmin_community編集_文字数_lessバリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->type('service_name', '01')
                ->type('service_name_reading', '01')
                ->type('url_path', '012')
                ->type('hash_key', '012')
                ->script("window.scrollTo(0, 1200);");
            $browser->press('編集')
                ->assertSeeIn('div.alert.alert-danger', 'エラー')
                ->assertSee('コミュニティ名称は、3文字以上で指定してください。')
                ->assertSee('service name readingは、3文字以上で指定してください。')
                ->assertSee('secretは、4文字以上で指定してください。');
        });
    }

    /**
     * @test
     */
    public function superAdmin_communit編集_異常文字形式バリデート確認()
    {
        $this->browse(function ($browser) {
            $browser->visit('admin_community/edit?id=1')
                ->type('url_path', 'あいう')
                ->type('hash_key', 'あいう')
                ->script("window.scrollTo(0, 1200);");
            $browser->press('編集')
                ->assertSeeIn('div.alert.alert-danger', 'エラー')
                ->assertSee('url pathに正しい形式を指定してください。')
                ->assertSee('secretに正しい形式を指定してください。');
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
