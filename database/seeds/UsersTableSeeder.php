<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dateTime = date("Y-m-d H:i:s");
        $password = bcrypt('aaaaaa');

        $param = [
            'community_id' => 1,
            'name' => '未登録',
            'email' => 'admin@aaa.com',
            'login_id' => 'admin@aaa.com@1',
            'role' => 'superAdmin',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 2,
            'name' => '未登録',
            'email' => 'aaa@aaa.com',
            'login_id' => 'aaa@aaa.com@2',
            'role' => 'readerAdmin',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 3,
            'name' => '未登録',
            'email' => 'zzz@zzz.com',
            'login_id' => 'zzz@zzz.com@3',
            'role' => 'readerAdmin',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 2,
            'name' => '藤本　太郎喜左衛門将時能 委託管理者',
            'email' => 'bbb@bbb.com',
            'login_id' => 'bbb@bbb.com@2',
            'role' => 'normalAdmin',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 1,
            'name' => '鈴木　一郎 委託管理者',
            'email' => 'ccc@ccc.com',
            'login_id' => 'ccc@ccc.com@1',
            'role' => 'normalAdmin',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 1,
            'name' => '佐藤　ふたば　非表示さん',
            'email' => 'ddd@ddd.com',
            'login_id' => 'ddd@ddd.com@1',
            'role' => 'normal',
            'password' => $password,
            'hide' => 1,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 2,
            'name' => '田中　寿限無寿限無一郎　委託管理者',
            'email' => 'eee@eee.com',
            'login_id' => 'eee@eee.com@2',
            'role' => 'normalAdmin',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 3,
            'name' => 'ランダム太郎',
            'email' => 'fff@fff.com',
            'login_id' => 'fff@fff.com@3',
            'role' => 'normal',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 3,
            'name' => 'ランダム次郎',
            'email' => 'ggg@ggg.com',
            'login_id' => 'ggg@ggg.com@3',
            'role' => 'normal',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'community_id' => 3,
            'name' => 'ランダム三郎',
            'email' => 'hhh@hhh.com',
            'login_id' => 'hhh@hhh.com@3',
            'role' => 'normal',
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        for ($i=0; $i < 10; $i++) {
            $rand10 = str_random(10);
            $mail = $rand10 .'@gmail.com';
            $rand_num = rand(1,3);
            $login_id = $mail . '@' . $rand_num;
            $param = [
                'community_id' => $rand_num,
                'name' => $rand10,
                'email' => $mail,
                'login_id' => $login_id,
                'role' => 'normal',
                'password' => $password,
                'last_access' => $dateTime,
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
            ];
            DB::table('users')->insert($param);
        }
    }
}
