<?php

namespace Tests\Browser;

use App\User;
use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Change_password_test extends DuskTestCase
{
    protected static $db_inited = false;
    use RefreshDatabase;

    protected static function initDB()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    protected function setUp()
    {
        parent::setUp();
        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }
    }

    // --- superAdmin test

    /**
     * @test
     */
    public function パスワード変更画面_表示確認()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/password/edit?id=1')
                ->assertSeeIn('h2', 'Password変更')
                ->assertSee('未登録 comm1 super')
                ->assertSee('admin@aaa.com');
        });
    }

    // パスワード変更画面_入力無しでの変更でバリデートエラー
    // viewに required 有りの為不要
    
    // パスワード変更画面_現在のpassword_想定外の値でバリデートエラー
    // form passwordの為不要

    /**
     * @test
     */
    public function パスワード変更画面_現在のpasswordが異なる場合でバリデートエラー()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/password/edit?id=1')
                ->type('now_password','zzzzzz')
                ->type('password','aaaaaa')
                ->type('password_confirmation','bbbbbb');
            $browser->press('Password変更')
                ->assertSee('現在のPasswordが一致しません');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_新しいpassword_の文字数が規定値以下でバリデートエラー()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/password/edit?id=1')
                ->type('now_password', 'aaaaaa')
                ->type('password', '12345')
                ->type('password_confirmation', '12345');
            $browser->press('Password変更')
                ->assertSee('パスワードは、6文字以上で指定してください。');
        });
    }

    // パスワード変更画面_新しいpassword_想定外の値でバリデートエラー
    // viewに required 有りの為不要

    /**
     * @test
     */
    public function パスワード変更画面_現在passwordが異なる場合でバリデートエラー()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/password/edit?id=1')
                ->type('now_password', 'zzzzzz')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb');
            $browser->press('Password変更')
                ->assertSee('現在のPasswordが一致しません');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_新しいpasswordが確認と不一致の場合でバリデートエラー()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/password/edit?id=1')
                ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'cccccc');
            $browser->press('Password変更')
                ->assertSee('パスワードと、確認フィールドとが、一致していません。');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_superAdmin_自分のpassword正常編集の確認()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/password/edit?id=1')
                ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
            ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 編集内容の確認 新たなpasswordで変更可能か
            $browser->visit('/password/edit?id=1')
                ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
            // 変更できたか確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_superAdmin_他のコミュニティオーナーのpassword正常編集の確認()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/password/edit?id=2')
                ->assertSee('admin2@aaa.com')
                ->assertDontSee('現在のPassword')
                // ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 元に戻す
            $browser->visit('/password/edit?id=2')
                // ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
        });
    }

    // --- readerAdmin test
    
    /**
     * @test
     */
    public function パスワード変更画面_readerAdmin_他のコミュニティ閲覧ができない()
    {
        $this->browse(function ($browser) {
            // commu 2 readerAdmin
            $browser->loginAs(User::find(2))
                // commu 1 superAdmin
                ->visit('/password/edit?id=1')
                ->assertSee('このページは閲覧できません')
                // commu 3 readerAdmin
                ->visit('/password/edit?id=3')
                ->assertSee('このページは閲覧できません')
                // commu 1 normalAdmin
                ->visit('/password/edit?id=4')
                ->assertSee('このページは閲覧できません')
                // commu 1 normal
                ->visit('/password/edit?id=5')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_readerAdmin_自分のpassword正常編集の確認()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
                ->visit('/password/edit?id=2')
                ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 編集内容の確認 新たなpasswordで変更可能か
            $browser->visit('/password/edit?id=2')
                ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
            // 変更できたか確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_readerAdmin_normalAdminのpassword正常編集の確認()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
            // commu 2 normalAdmin
            ->visit('/password/edit?id=13')
                ->assertSee('委託管理者 藤本　太郎喜左衛門将時能')
                ->assertSee('aaa2@aaa.com')
                ->assertDontSee('現在のPassword')
                // ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 元に戻す
            $browser->visit('/password/edit?id=13')
                // ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_readerAdmin_normalのpassword正常編集の確認()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
            // commu 2 normal
                ->visit('/password/edit?id=14')
                ->assertSee('田中　寿限無寿限無一郎')
                ->assertSee('bbb2@bbb.com')
                ->assertDontSee('現在のPassword')
                // ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 元に戻す
            $browser->visit('/password/edit?id=14')
                // ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
        });
    }

    // --- normalAdmin test

    /**
     * @test
     */
    public function パスワード変更画面_normalAdmin_他のコミュニティ閲覧ができない()
    // 兼 パスワード変更画面_normalAdmin_superAdminのpassword変更できない
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                // commu 1 superAdmin
                ->visit('/password/edit?id=1')
                ->assertSee('このページは閲覧できません')
                // commu 3 readerAdmin
                ->visit('/password/edit?id=3')
                ->assertSee('このページは閲覧できません')
                // commu 1 normalAdmin
                ->visit('/password/edit?id=4')
                ->assertSee('このページは閲覧できません')
                // commu 1 normal
                ->visit('/password/edit?id=5')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_normalAdmin_自分のpassword正常編集の確認()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                ->visit('/password/edit?id=13')
                ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 編集内容の確認 新たなpasswordで変更可能か
            $browser->visit('/password/edit?id=13')
                ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
            // 変更できたか確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_normalAdmin_他のnormalAdminの正常編集の確認()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                // 14を委託管理者に昇格
                ->visit('/admin_user/edit?id=14')
                ->radio('role', 'normalAdmin')
                ->press('ユーザー情報を更新');

            // commu 2 昇格したnormalAdmin
            $browser->visit('/password/edit?id=14')
                ->assertSee('田中　寿限無寿限無一郎')
                ->assertSee('bbb2@bbb.com')
                // normalAdmin同志は現在のpassword入力が必要
                ->assertSee('現在のPassword')
                ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 元に戻す
            $browser->visit('/password/edit?id=14')
                ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_normalAdmin_normalの正常編集の確認()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
            // commu 2 normal
                ->visit('/password/edit?id=15')
                ->assertSee('燕　東海林太郎兵衛宗清')
                ->assertSee('ccc2@ccc.com')
                ->assertDontSee('現在のPassword')
                // ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 元に戻す
            $browser->visit('/password/edit?id=15')
                // ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
        });
    }

    // --- normal test

    /**
     * @test
     */
    public function パスワード変更画面_normal_他のコミュニティ閲覧ができない()
    // 兼 パスワード変更画面_normal_superAdminのpassword変更できない
    // 兼 パスワード変更画面_normal_他のユーザーの変更ができない
    {
        $this->browse(function ($browser) {
            // commu 2 normal
            $browser->loginAs(User::find(16))
                // commu 1 superAdmin
                ->visit('/password/edit?id=1')
                ->assertSee('このページは閲覧できません')
                // commu 3 readerAdmin
                ->visit('/password/edit?id=3')
                ->assertSee('このページは閲覧できません')
                // commu 1 normalAdmin
                ->visit('/password/edit?id=4')
                ->assertSee('このページは閲覧できません')
                // commu 1 normal
                ->visit('/password/edit?id=5')
                ->assertSee('このページは閲覧できません')
                // commu 2 normal
                ->visit('/password/edit?id=17')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function パスワード変更画面_normal_自分のpassword正常編集の確認()
    {
        $this->browse(function ($browser) {
            // commu 2 normal
            $browser->loginAs(User::find(16))
                ->visit('/password/edit?id=16')
                ->type('now_password', 'aaaaaa')
                ->type('password', 'bbbbbb')
                ->type('password_confirmation', 'bbbbbb')
                ->press('Password変更');
            // 移動先確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
            // 編集内容の確認 新たなpasswordで変更可能か
            $browser->visit('/password/edit?id=16')
                ->type('now_password', 'bbbbbb')
                ->type('password', 'aaaaaa')
                ->type('password_confirmation', 'aaaaaa')
                ->press('Password変更');
            // 変更できたか確認
            $browser->assertPathIs('/admin_user/edit')
                ->assertSeeIn('.alert', 'パスワードを変更しました。');
        });
    }

    /**
     * @test
     */
    public function 後処理()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('password/edit?id=1')
                ->assertSeeIn('h2', 'Password変更');
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
        });
    }
}
