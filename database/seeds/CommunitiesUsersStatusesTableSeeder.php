<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CommunitiesUsersStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subSecond = Carbon::now();
        $subSecond = $subSecond->subSecond(rand(1,59));
        $subHour = Carbon::now();
        $subHour = $subHour->subHour(rand(1,23));
        $subDay = Carbon::now();
        $subDay = $subDay->subDay(rand(31,59));
        $subDay2 = Carbon::now();
        $subDay2 = $subDay2->subDay(rand(1,30));

        // user1 comm1 super管理者
        $param = [
            'id' => 1,
            'role_id' => 4,
            'hide' => 0,
            'last_access' => $subSecond,
            'created_at' => $subDay,
            'updated_at' => $subDay2,
        ];
        DB::table('communities_users_statuses')->insert($param);

        // user2 comm2管理者
        $param = [
            'id' => 2,
            'role_id' => 3,
            'hide' => 0,
            'last_access' => $subSecond,
            'created_at' => $subDay,
            'updated_at' => $subDay2,
        ];
        DB::table('communities_users_statuses')->insert($param);

        // user3 comm3管理者
        $param = [
            'id' => 3,
            'role_id' => 3,
            'hide' => 0,
            'last_access' => $subSecond,
            'created_at' => $subDay,
            'updated_at' => $subDay2,
        ];
        DB::table('communities_users_statuses')->insert($param);

        // user4 AAA
        $param = [
            'id' => 4,
            'role_id' => 2,
            'hide' => 0,
            'last_access' => $subSecond,
            'created_at' => $subDay,
            'updated_at' => $subDay2,
        ];
        DB::table('communities_users_statuses')->insert($param);


        // 管理ユーザー& community1 前半 滞在中
        for ($i=5; $i <= 8; $i++) {
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1,59));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1,23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(31,59));
            $subDay2 = Carbon::now();
            $subDay2 = $subDay2->subDay(rand(1,30));
            $param = [
                'id' => $i,
                'role_id' => 1,
                'hide' => 0,
                'last_access' => $subSecond,
                'created_at' => $subDay,
                'updated_at' => $subDay2,
            ];
            DB::table('communities_users_statuses')->insert($param);
        }

        // community1 後半 帰宅中
        for ($i=9; $i <= 14; $i++) {
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1,59));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1,23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(31,59));
            $subDay2 = Carbon::now();
            $subDay2 = $subDay2->subDay(rand(1,30));
            $param = [
                'id' => $i,
                'role_id' => 1,
                'hide' => 0,
                'last_access' => $subHour,
                'created_at' => $subDay,
                'updated_at' => $subDay2,
            ];
            DB::table('communities_users_statuses')->insert($param);
        }

        // community2 & 3 重複ユーザー 全て滞在中
        for ($i=15; $i <= 40; $i++) {
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1,59));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1,23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(31,59));
            $subDay2 = Carbon::now();
            $subDay2 = $subDay2->subDay(rand(1,30));
            $param = [
                'id' => $i,
                'role_id' => 1,
                'hide' => 0,
                'last_access' => $subSecond,
                'created_at' => $subDay,
                'updated_at' => $subDay2,
            ];
            DB::table('communities_users_statuses')->insert($param);
        }

        // provisonal user
        $id = 41;
        for ($i= 0; $i <= 5; $i++) { 
            $param = [
                'id' => $id,
                'role_id' => 1,
                'hide' => 0,
                'last_access' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('communities_users_statuses')->insert($param);
            $id++;
        }
    }
}
