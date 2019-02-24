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
            'name_reading' => '未登録 comm1 super',
            'unique_name' => 'admin@aaa.com',
            'email' => 'admin@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // user2
        $param = [
            'name' => '未登録 comm2',
            'name_reading' => '未登録 comm2',
            'unique_name' => 'admin2@aaa.com',
            'email' => 'admin2@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // user3
        $param = [
            'name' => '未登録 comm3',
            'name_reading' => '未登録 comm3',
            'unique_name' => 'admin3@aaa.com',
            'email' => 'admin3@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // user4
        $param = [
            'name' => 'AAA comm1 委託管理者',
            'name_reading' => 'AAA comm1 委託管理者',
            'unique_name' => 'aaa@aaa.com',
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
            'name_reading' => 'BBB BBB',
            'unique_name' => 'bbb@bbb.com',
            'email' => 'bbb@bbb.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'CCC CCC',
            'name_reading' => 'CCC CCC',
            'unique_name' => 'ccc@ccc.com',
            'email' => 'ccc@ccc.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'DDD DDD',
            'name_reading' => 'DDD DDD',
            'unique_name' => 'ddd@ddd.com',
            'email' => 'ddd@ddd.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'EEE EEE',
            'name_reading' => 'EEE EEE',
            'unique_name' => 'eee@eee.com',
            'email' => 'eee@eee.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'FFF FFF',
            'name_reading' => 'FFF FFF',
            'unique_name' => 'fff@fff.com',
            'email' => 'fff@fff.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);
        //user10
        $param = [
            'name' => 'GGG GGG',
            'name_reading' => 'GGG GGG',
            'unique_name' => 'ggg@ggg.com',
            'email' => 'ggg@ggg.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'HHH HHH',
            'name_reading' => 'HHH HHH',
            'unique_name' => 'hhh@hhh.com',
            'email' => 'hhh@hhh.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);
        //user12
        $param = [
            'name' => 'III III',
            'name_reading' => 'III III',
            'unique_name' => 'iii@iii.com',
            'email' => 'iii@iii.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // community 2 2～10
        $param = [
            'name' => '委託管理者 藤本　太郎喜左衛門将時能',
            'name_reading' => '委託管理者 藤本　太郎喜左衛門将時能',
            'unique_name' => 'aaa2@aaa.com',
            'email' => 'aaa2@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '田中　寿限無寿限無一郎',
            'name_reading' => '田中　寿限無寿限無一郎',
            'unique_name' => 'bbb2@bbb.com',
            'email' => 'bbb2@bbb.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '燕　東海林太郎兵衛宗清',
            'name_reading' => '燕　東海林太郎兵衛宗清',
            'unique_name' => 'ccc2@ccc.com',
            'email' => 'ccc2@ccc.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '根本　寝坊之助食左衛門',
            'name_reading' => '根本　寝坊之助食左衛門',
            'unique_name' => 'ddd2@ddd.com',
            'email' => 'ddd2@ddd.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '野田　江川富士一二三四五左衛門助太郎',
            'name_reading' => '野田　江川富士一二三四五左衛門助太郎',
            'unique_name' => 'eee2@eee.com',
            'email' => 'eee2@eee.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '沢井　麻呂女鬼久壽老八重千代子さん',
            'name_reading' => '沢井　麻呂女鬼久壽老八重千代子さん',
            'unique_name' => 'fff2@fff.com',
            'email' => 'fff2@fff.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '古屋敷　後部屋新九郎左衛門介之亟',
            'name_reading' => '古屋敷　後部屋新九郎左衛門介之亟',
            'unique_name' => 'ggg2@ggg.com',
            'email' => 'ggg2@ggg.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '一二三　 四五六七八九十郎',
            'name_reading' => '一二三　 四五六七八九十郎',
            'unique_name' => 'hhh2@hhh.com',
            'email' => 'hhh2@hhh.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '渡辺　七五三吉五郎次郎三郎衛門',
            'name_reading' => '渡辺　七五三吉五郎次郎三郎衛門',
            'unique_name' => 'iii2@iii.com',
            'email' => 'iii2@iii.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        // community 3 2～10
        $param = [
            'name' => 'ランダム太郎 委託管理者',
            'unique_name' => 'aaa3@aaa.com',
            'email' => 'aaa3@aaa.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'ランダム次郎',
            'name_reading' => 'ランダム次郎',
            'unique_name' => 'bbb3@bbb.com',
            'email' => 'bbb3@bbb.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => 'ランダム三郎',
            'name_reading' => 'ランダム三郎',
            'unique_name' => 'ccc3@ccc.com',
            'email' => 'ccc3@ccc.com',
            'password' => $password,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        for ($i=1; $i <= 6; $i++) {
            $rand10 = str_random(10);
            $unique_name = $rand10;
            $email = $rand10 .'@gmail.com';
            $param = [
                'name' => $rand10,
                'unique_name' => $unique_name,
                'email' => $email,
                'password' => $password,
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
            ];
            DB::table('users')->insert($param);
        }

        // provision user
        $color = array('red1','blue2','green3','yellow4','black5','white6');
        for ($i = 0; $i <= 5; $i++) {
            $password = bcrypt($color[$i] . '-human');
            $param = [
                'name' => $color[$i] . '-human',
                'unique_name' => $color[$i] .'-human',
                'provisional' => true,
                'password' => $password,
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
            ];
            DB::table('users')->insert($param);
        }
    }
}
