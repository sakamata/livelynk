<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CommunityUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // comm1 superAdmin
        $param = [
            'community_id' => 1,
            'user_id' => 1,
        ];
        DB::table('community_user')->insert($param);
        // comm2 管理者
        $param = [
            'community_id' => 2,
            'user_id' => 2,
        ];
        DB::table('community_user')->insert($param);
        // comm3 管理者
        $param = [
            'community_id' => 3,
            'user_id' => 3,
        ];
        DB::table('community_user')->insert($param);
        // comm1 委託管理者
        $param = [
            'community_id' => 1,
            'user_id' => 4,
        ];
        DB::table('community_user')->insert($param);

        // userid  5～12 community 1 残り8名作成
        $user_id = 5;
        for ($u=5; $u <= 12; $u++) {
            $param = [
                'community_id' => 1,
                'user_id' => $user_id,
            ];
            DB::table('community_user')->insert($param);
            $user_id++;
        }

        // community 2 & 3 の9+9=18名作成
        $user_id = 13;
        for ($c=2; $c <= 3; $c++) {
            for ($u=1; $u <= 9; $u++) {
                $param = [
                    'community_id' => $c,
                    'user_id' => $user_id,
                ];
                DB::table('community_user')->insert($param);
                $user_id++;
            }
        }

        // 複数コミュニティに登録しているuser 10単位追加
        // userid  1～6 community 2
        $user_id = 1;
        for ($u=1; $u <= 6; $u++) {
            $param = [
                'community_id' => 2,
                'user_id' => $user_id,
            ];
            DB::table('community_user')->insert($param);
            $user_id++;
        }
        // userid  1～4 community 3
        $user_id = 1;
        for ($u=1; $u <= 4; $u++) {
            $param = [
                'community_id' => 3,
                'user_id' => $user_id,
            ];
            DB::table('community_user')->insert($param);
            $user_id++;
        }
    }
}
