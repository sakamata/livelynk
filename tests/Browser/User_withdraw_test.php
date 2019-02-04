<?php

namespace Tests\Browser;

use App\User;
use App\CommunityUser;
use \Artisan;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class User_withdraw_test extends DuskTestCase
{
    protected static $db_inited = false;
    // 毎メソッド毎にデータ作り直し コメントアウト必須
    // use RefreshDatabase;

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

    // --- superAdmin

    /**
     * @test
     */
    public function 退会画面_superAdmin_自分の退会は出来ない()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('admin_user/delete?id=1')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_superAdmin_to_readerAdminの退会は出来ない()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('admin_user/delete?id=2')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_superAdmin_to_normalAdminの退会をさせる()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                // commu1 noramalAdmin
                ->visit('admin_user/delete?id=4')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('AAA comm1 委託管理者')
                ->assertSee('aaa@aaa.com')
                ->press('退会する');
            $browser->assertPathIs('/admin_user')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.4')
                ->assertDontSee('aaa@aaa.com')
                ->assertDontSee('AAA comm1 委託管理者');

        });
        $this->assertDatabaseMissing('users', [
                'id' => 4,
                'email' => 'aaa@aaa.com',
            ])
            ->assertDatabaseMissing('community_user', ['id' => 4])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 4]);
    }

    /**
     * @test
     */
    public function 退会画面_superAdmin_to_normalの退会をさせる()
    // 複数コミュニティに存在するユーザー、かつ複数コミュにまたがって端末を保持している
    // 従って該当コミュニティNo1 のみ退会の処理となる
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                // commu1 noramal
                ->visit('admin_user/delete?id=5')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('BBB BBB')
                ->assertSee('bbb@bbb.com')
                ->press('退会する');
            $browser->assertPathIs('/admin_user')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.5')
                ->assertDontSee('bbb@bbb.com')
                ->assertDontSee('BBB BBB');
        });
        // userそのものは残る
        $this->assertDatabaseHas('users', [
                'id' => 5,
                'email' => 'bbb@bbb.com',
            ])
            // 他のコミュニティに登録した端末は残っているか確認
            ->assertDatabaseHas('mac_addresses', [
                'id' => 31,
                'id' => 37,
            ])
            ->assertDatabaseHas('community_user', [
                'id' => 31,
                'user_id' => 5,
            ])
            ->assertDatabaseHas('communities_users_statuses', ['id' => 31])
            ->assertDatabaseHas('community_user', [
                'id' => 37,
                'user_id' => 5,
            ])
            ->assertDatabaseHas('communities_users_statuses', ['id' => 37])
            // 削除されたcommu1の関連のデータの確認
            ->assertDatabaseMissing('mac_addresses', [
                'id' => 4,
                'id' => 5,
                ])
            ->assertDatabaseMissing('community_user', ['id' => 5])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 5]);
    }

    /**
     * @test
     */
    public function 退会画面_superAdmin_to_仮ユーザーの退会をさせる()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                // commu1 noramalAdmin
                ->visit('admin_user/delete?id=41')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('red1-human')
                ->press('退会する');
            $browser->assertPathIs('/admin_user_provisional')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.41')
                ->assertDontSee('red1-human');
        });
        $this->assertDatabaseMissing('users', [
            'id' => 31,
            'unique_name' => 'red1-human',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 41])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 41]);
    }

    // --- readerAdmin

    /**
     * @test
     */
    public function 退会画面_readerAdmin_to_superAdminの退会はできない()
    {
        $this->browse(function ($browser) {
            // commu 2 readerAdmin
            $browser->loginAs(User::find(2))
                // commu 1 superAdmin
            ->visit('admin_user/delete?id=1')
            ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_readerAdmin_異なるコミュニティの画面は403()
    {
        $this->browse(function ($browser) {
            // commu 2 readerAdmin
            $browser->loginAs(User::find(2))
                //commu1 normal
                ->visit('admin_user/delete?id=5')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_readerAdmin_自分の退会はできない()
    {
        $this->browse(function ($browser) {
            // commu 2 readerAdmin
            $browser->loginAs(User::find(2))
                ->visit('admin_user/delete?id=2')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_readerAdmin_to_normalAdminの退会をさせる()
    {
        $this->browse(function ($browser) {
            // commu 2 readerAdmin
            $browser->loginAs(User::find(2))
                // commu2 noramalAdmin
                ->visit('admin_user/delete?id=13')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('委託管理者 藤本　太郎喜左衛門将時能')
                ->assertSee('aaa2@aaa.com')
                ->press('退会する');
            $browser->assertPathIs('/admin_user')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.13')
                ->assertDontSee('aaa2@aaa.com')
                ->assertDontSee('委託管理者 藤本　太郎喜左衛門将時能');

        });
        $this->assertDatabaseMissing('users', [
            'id' => 13,
            'email' => 'aaa2@aaa.com',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 13])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 13]);
    }

    /**
     * @test
     */
    public function 退会画面_readerAdmin_to_normalの退会をさせる()
    {
        $this->browse(function ($browser) {
            // commu 2 readerAdmin
            $browser->loginAs(User::find(2))
                // commu2 noramal
                ->visit('admin_user/delete?id=14')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('田中　寿限無寿限無一郎')
                ->assertSee('bbb2@bbb.com')
                ->press('退会する');
            $browser->assertPathIs('/admin_user')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.13')
                ->assertDontSee('bbb2@bbb.com')
                ->assertDontSee('田中　寿限無寿限無一郎');

        });
        $this->assertDatabaseMissing('users', [
            'id' => 14,
            'email' => 'bbb2@bbb.com',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 14])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 14]);
    }

    /**
     * @test
     */
    public function 退会画面_readerAdmin_to_仮ユーザーの退会をさせる()
    {
        $this->browse(function ($browser) {
            // commu 2 readerAdmin
            $browser->loginAs(User::find(2))
                // commu2 provisional
                ->visit('admin_user/delete?id=43')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('green3-human')
                ->press('退会する');
            $browser->assertPathIs('/admin_user_provisional')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.43')
                ->assertDontSee('green3-human');
        });
        $this->assertDatabaseMissing('users', [
            'id' => 33,
            'unique_name' => 'green3-human',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 43])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 43]);
    }

    // --- normalAdmin

    /**
     * @test
     */
    public function 手動DB再生処理()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(21))
                ->visit('/admin_user/delete?id=21')
                // 警告除けで適当な assert を入れる
                ->assertSee('退会の確認');
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
        });
    }

    /**
     * @test
     */
    public function 退会画面_normalAdmin_異なるコミュニティの画面は403()
    {
        $this->browse(function ($browser) {
            // commu 1 normalAdmin
            $browser->loginAs(User::find(4))
                // commu2 normal
                ->visit('admin_user/delete?id=21')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_normaAdmin_to_superAdminの退会はできない()
    {
        $this->browse(function ($browser) {
            // commu 1 normalAdmin
            $browser->loginAs(User::find(4))
                // commu1 superAdmin
                ->visit('admin_user/delete?id=1')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_normaAdmin_to_readerAdminの退会はできない()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                // commu2 readerAdmin
                ->visit('admin_user/delete?id=2')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_normaAdmin_to_自分の退会をする()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                ->visit('admin_user/delete?id=13')
                ->assertSee('退会の確認')
                ->assertSee('このコミュニティから退会してもよろしいですか？')
                ->assertDontSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('委託管理者 藤本　太郎喜左衛門将時能')
                ->assertSee('aaa2@aaa.com')
                ->press('退会する');
            $browser->assertPathIs('/')
                ->assertSeeIn('.alert', '退会が完了しました。ご利用ありがとうございました');
        });
        $this->assertDatabaseMissing('users', [
            'id' => 13,
            'unique_name' => '委託管理者 藤本　太郎喜左衛門将時能',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 13])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 13]);
    }

    /**
     * @test
     */
    public function 手動DB再生処理2()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(21))
                ->visit('/admin_user/delete?id=21')
                // 警告除けで適当な assert を入れる
                ->assertSee('退会の確認');
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
        });
    }

    /**
     * @test
     */
    public function 退会画面_normaAdmin_to_normalAdminの退会()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                // commu2 readerAdmin
                // 他のユーザーを管理者に昇格させる
                // commu2 normal
                ->visit('admin_user/edit?id=14')
                ->radio('role', 'normalAdmin')
                ->script("window.scrollTo(0, 1500);");
            $browser->press('ユーザー情報を更新');
            $browser->visit('admin_user/delete?id=14')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('bbb2@bbb.com')
                ->assertSee('田中　寿限無寿限無一郎')
                ->press('退会する');
            $browser->assertPathIs('/admin_user')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.14')
                ->assertDontSee('bbb2@bbb.com')
                ->assertDontSee('田中　寿限無寿限無一郎');
        });
        $this->assertDatabaseMissing('users', [
            'id' => 14,
            'unique_name' => '田中　寿限無寿限無一郎',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 14])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 14]);
    }

    /**
     * @test
     */
    public function 退会画面_normaAdmin_to_normalの退会をさせる()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                // commu2 noramal
                ->visit('admin_user/delete?id=15')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('燕　東海林太郎兵衛宗清')
                ->assertSee('ccc2@ccc.com')
                ->press('退会する');
            $browser->assertPathIs('/admin_user')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.15')
                ->assertDontSee('ccc2@ccc.com')
                ->assertDontSee('燕　東海林太郎兵衛宗清');

        });
        $this->assertDatabaseMissing('users', [
            'id' => 15,
            'email' => 'ccc2@ccc.com',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 15])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 15]);
    }

    /**
     * @test
     */
    public function 退会画面_normalAdmin_to_仮ユーザーの退会をさせる()
    {
        $this->browse(function ($browser) {
            // commu 2 normalAdmin
            $browser->loginAs(User::find(13))
                ->visit('admin_user/delete?id=43')
                ->assertSee('退会の確認')
                ->assertSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('green3-human')
                ->press('退会する');
            $browser->assertPathIs('/admin_user_provisional')
                ->assertSeeIn('.alert', 'ユーザーを退会させました')
                ->assertSee('ユーザー一覧')
                ->script("window.scrollTo(0, 2000);");
            $browser->assertDontSee('No.43')
                ->assertDontSee('green3-human');
        });
        $this->assertDatabaseMissing('users', [
            'id' => 33,
            'unique_name' => 'green3-human',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 43])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 43]);
    }

    // --- normal

    /**
     * @test
     */
    public function 退会画面_normal_異なるコミュニティの画面は403()
    {
        $this->browse(function ($browser) {
            // commu 1 normal
            $browser->loginAs(User::find(12))
                // commu3 normal
                ->visit('admin_user/delete?id=28')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_normal_異なるユーザーの画面は403()
    {
        $this->browse(function ($browser) {
            // commu 1 normal
            $browser->loginAs(User::find(12))
                // commu1 normal
                ->visit('admin_user/delete?id=11')
                ->assertSee('このページは閲覧できません');
        });
    }

    /**
     * @test
     */
    public function 退会画面_norma_自分の退会をする()
    {
        $this->browse(function ($browser) {
            // commu 1 normal
            $browser->loginAs(User::find(12))
                ->visit('admin_user/delete?id=12')
                ->assertSee('退会の確認')
                ->assertSee('このコミュニティから退会してもよろしいですか？')
                ->assertDontSee('このユーザーを退会させてもよろしいですか？')
                ->assertSee('III III')
                ->assertSee('iii@iii.com')
                ->press('退会する');
            $browser->assertPathIs('/')
                ->assertSeeIn('.alert', '退会が完了しました。ご利用ありがとうございました');
        });
        $this->assertDatabaseMissing('users', [
            'id' => 12,
            'unique_name' => 'III III',
        ])
            ->assertDatabaseMissing('community_user', ['id' => 12])
            ->assertDatabaseMissing('communities_users_statuses', ['id' => 12]);
    }

    /**
     * @test
     */
    public function 後処理()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(21))
            ->visit('/admin_user/delete?id=21')
                // 警告除けで適当な assert を入れる
                ->assertSee('退会の確認');
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
        });
    }
}
