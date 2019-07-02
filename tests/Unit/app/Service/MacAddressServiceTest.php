<?php

namespace Tests\Unit\app\Http\Controllers;

use \Artisan;
use App\MacAddress;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MacAddressServiceTest extends TestCase
{
    use RefreshDatabase;
    public $now;
    public function setup()
    {
        parent::setUp();
        $this->now = Carbon::now();
    }

    // 未使用　使わなくなっている
    public function dataProvider_for_getRecentStayCommunityUserIds() :array
    {
        // testの仮想時間を12:00とし 遡って90前の 10:30までの滞在中userを取得できるか確認
        $now            = Carbon::create(2018, 12, 31, 12, 00, 00);
        $sub60Min       = Carbon::create(2018, 12, 31, 11, 00, 00);
        $sub89Min       = Carbon::create(2018, 12, 31, 10, 31, 00);
        $sub89Min59sec  = Carbon::create(2018, 12, 31, 10, 30, 01);
        $sub90Min       = Carbon::create(2018, 12, 31, 10, 30, 00);
        $sub90Min1sec   = Carbon::create(2018, 12, 31, 10, 29, 59);
        $sub91Min       = Carbon::create(2018, 12, 31, 10, 29, 00);

        //              community_user_id,  current_stay, posted_at, 取得できるか
        return [
            '60min_true' =>               [100,   1,  $sub60Min,       true  ],
            '60min_not_stay_false' =>     [101,   0,  $sub60Min,       false ],
            '89min_true' =>               [102,   1,  $sub89Min,       true  ],
            'sub89Min59sec_true' =>       [103,   1,  $sub89Min59sec,  true  ],
            '90min_true' =>               [104,   1,  $sub90Min,       true  ],
            '90min1sec_false' =>          [105,   1,  $sub90Min1sec,   false ],
            '91min_false' =>              [106,   1,  $sub91Min,       false ],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider_for_getRecentStayCommunityUserIds
     */
    // 未使用　使わなくなっている
    // MacAddressService->getRecentStayCommunityUserIds の test
    public function last_postedが現在からn分以内の滞在中のcommunity_user_idを配列で取得する($community_user_id,  $current_stay, $posted_at, $assert_bool)
    {
        factory(MacAddress::class)->create([
            'community_user_id' => $community_user_id,
            'current_stay' => $current_stay,
            'posted_at' => $posted_at,
        ]);
        // testの仮想時間を12:00とし 遡って90前の 10:30までの滞在中userを取得できるか確認
        $service = app()->make('\App\Service\MacAddressService');
        $recent_datetime = Carbon::create(2018, 12, 31, 10, 30, 00);
        $ids = $service->getRecentStayCommunityUserIds($recent_datetime);
        if ($assert_bool) {
            $this->assertContains($community_user_id, $ids);
        } else {
            $this->assertNotContains($community_user_id, $ids);
        }
    }

    /**
     * @test
     */
    public function getRecentStayIdsAndMaxPostedAt_が最新の端末の更新情報を出しているかのtest()
    {
        // 1ユーザーにつき複数の端末を保持していた際、滞在中の最新の値のみが1レコード取得されるか検証
        $now           = '2018-12-31 12:00:00';
        $sub1Min       = '2018-12-31 11:59:00';
        $sub2Min       = '2018-12-31 11:58:00';
        $sub3Min       = '2018-12-31 11:57:00';
        $sub90Min      = '2018-12-31 10:30:00';
        $sub90Min1sec = '2018-12-31 10:29:59';
        $sub91Min      = '2018-12-31 10:29:00';

        //                 community_user_id,  current_stay
        $arr = [
            '0_userA_1min_true' =>       [110,   1, 'posted_at' => $sub1Min,      'assert'=> true  ],
            '1_userA_2min_false' =>       [110,   1,'posted_at' => $sub2Min,      'assert'=> false  ],
            '2_userA_3min_false' =>       [110,   1,'posted_at' => $sub3Min,      'assert'=> false  ],
            '3_userA_91min_false' =>     [110,   1, 'posted_at' => $sub1Min,      'assert'=> false  ],
            '4_userB_not_stay_false' =>  [111,   0, 'posted_at' => $sub1Min,      'assert'=> false ],
            '5_userC_3min_false' =>      [112,   1, 'posted_at' => $sub3Min,      'assert'=> false ],
            '6_userC_2min_false' =>      [112,   1, 'posted_at' => $sub2Min,      'assert'=> false ],
            '7_userC_1min_true' =>       [112,   1, 'posted_at' => $sub1Min,      'assert'=> true  ],
            '8_userD_90min_true' =>       [113,   1, 'posted_at' => $sub90Min,      'assert'=> true  ],
            '9_90min1sec_false' =>       [114,   1, 'posted_at' => $sub90Min1sec, 'assert'=> false ],
            '10_91min_false' =>           [115,   1, 'posted_at' => $sub91Min,     'assert'=> false ],
        ];
        // $arr の値でダミーのデータを作成
        foreach ($arr as $key => $value) {
            factory(MacAddress::class)->create([
                'community_user_id' => $value[0],
                'current_stay' => $value[1],
                'posted_at' => $value['posted_at'],
            ]);
        }

        $service = app()->make('\App\Service\MacAddressService');
        // testの仮想時間を12:00 , 最大判断時間を90分前と想定する
        $recent_datetime = Carbon::create(2018, 12, 31, 10, 30, 00);
        $res = $service->getRecentStayIdsAndMaxPostedAt($recent_datetime);
        $i = 0;
        // $arr で true としたものを検証
        foreach ($arr as $key => $col) {
            if ($col['assert']) {
                $this->assertSame($col[0], $res[$i]->community_user_id);
                $this->assertSame($col[1], $res[$i]->current_stay);
                $this->assertSame($col['posted_at'], $res[$i]->posted_at);
                $i++;
            }
        }
        // 出力されたレコード数の検証
        // $i 0始まりだが、最後の $i++ でincrimentされるので $res のrecord配列数と同義となる
        $this->assertCount($i, $res);
    }
}
