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

        // tikara waza
        // user1
        $param = [
            'name' => '未登録 comm1 super',
            'email' => 'admin@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // user2
        $param = [
            'name' => '未登録 comm2',
            'email' => 'admin2@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // user3
        $param = [
            'name' => '未登録 comm3',
            'email' => 'admin3@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // user4
        $param = [
            'name' => 'AAA comm1 委託管理者',
            'email' => 'aaa@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // community 1 2～10
        // user5
        $param = [
            'name' => 'BBB BBB',
            'email' => 'bbb@bbb.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'CCC CCC',
            'email' => 'ccc@ccc.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'DDD DDD',
            'email' => 'ddd@ddd.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'EEE EEE',
            'email' => 'eee@eee.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'FFF FFF',
            'email' => 'fff@fff.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);
        //user10
        $param = [
            'name' => 'GGG GGG',
            'email' => 'ggg@ggg.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'HHH HHH',
            'email' => 'hhh@hhh.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);
        //user12
        $param = [
            'name' => 'III III',
            'email' => 'iii@iii.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // community 2 2～10
        $param = [
            'name' => '委託管理者 藤本　太郎喜左衛門将時能',
            'email' => 'aaa2@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '田中　寿限無寿限無一郎',
            'email' => 'bbb2@bbb.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '燕　東海林太郎兵衛宗清',
            'email' => 'ccc2@ccc.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '根本　寝坊之助食左衛門',
            'email' => 'ddd2@ddd.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '野田　江川富士一二三四五左衛門助太郎',
            'email' => 'eee2@eee.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '沢井　麻呂女鬼久壽老八重千代子さん',
            'email' => 'fff2@fff.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '古屋敷　後部屋新九郎左衛門介之亟',
            'email' => 'ggg2@ggg.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '一二三　 四五六七八九十郎',
            'email' => 'hhh2@hhh.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '渡辺　七五三吉五郎次郎三郎衛門',
            'email' => 'iii2@iii.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // community 3 2～10
        $param = [
            'name' => 'ランダム太郎 委託管理者',
            'email' => 'aaa3@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'ランダム次郎',
            'email' => 'bbb3@bbb.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'ランダム三郎',
            'email' => 'ccc3@ccc.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        for ($i=1; $i <= 6; $i++) {
            $rand10 = str_random(10);
            $mail = $rand10 .'@gmail.com';
            $param = [
                'name' => $rand10,
                'email' => $mail,
                'password' => $password,
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
            ];
            DB::table('users')->insert($param);
        }
    }
}
