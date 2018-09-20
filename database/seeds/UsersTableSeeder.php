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
            'role' => 'normalAdmin',
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
            $param = [
                'community_id' => $rand_num,
                'name' => $rand10,
                'email' => $mail,
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
