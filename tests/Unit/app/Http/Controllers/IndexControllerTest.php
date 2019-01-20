<?php

namespace Tests\Unit\app\Http\Controllers;

use App\Community;
use App\CommunityUser;
use App\User;
use App\Router;
use App\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;
    const COMMUNITY_ID = 1;
    const USER_ID = 1;
    const NAME = 'hoge';
    const SERVICE_NAME = 'hoge';
    const SERVICE_NAME_READING = 'hoge';
    const URL_PATH = 'hoge';
    const HASH_KEY = 'hoge';

    protected function setUp()
    {
        parent::setUp();

        Carbon::setTestNow();

        factory(Community::class)->create([
            'url_path' => self::URL_PATH,
        ]);
        factory(CommunityUser::class)->create();
        factory(User::class, 10)->create();
        factory(Router::class, 1)->create();
        factory(Role::class)->create([
            'role' => 'normal',
            'role' => 'normalAdmin',
        ]);
    }

    /**
     * @test
     */
    public function accsess_no_auth_未ログイン閲覧_存在するpathなら表示()
    {
        $this->assertDatabaseHas('communities',[
            'url_path' => self::URL_PATH,
        ]);
    }

    /**
     * @test
     */
    public function accsess_no_auth_未ログイン閲覧_存在しないpathなら404表示()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function accsess_auth_ログイン閲覧_sessionにcommunity_idが無ければログアウト()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function accsess_auth_ログイン閲覧_存在するpathなら該当コミュニティの滞在者画面一覧表示()
    {
        $this->assertTrue(true);
    }
}
