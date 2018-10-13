<?php

use Illuminate\Database\Seeder;

class CommunityUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // community3つの30名作成
        // userid  1～10 community 1
        // userid 11～20 community 2
        // userid 21～30 community 3
        $user_id = 1;
        for ($c=1; $c <= 3; $c++) {
            for ($u=1; $u <= 10; $u++) {
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
