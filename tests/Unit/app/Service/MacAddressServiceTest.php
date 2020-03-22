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
    public function setup(): void
    {
        parent::setUp();
        $this->now = Carbon::now();
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
