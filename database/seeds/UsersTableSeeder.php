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

        $param = [
            'name' => '鈴木　一郎',
            'email' => 'aaa@aaa.com',
            'admin_user' => 1,
            'password' => '$2y$10$UVYV.ayVgbkDGDY703mPFu.NKy1ChxgGMDAuzng23JgQsXiQSEfA6',
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '佐藤　ふたば',
            'email' => 'bbb@bbb.com',
            'admin_user' => 0,
            'password' => '$2y$10$kAM0qYxjnXQUrVlfHbsIoOJ7qhi4d7YQ4uOp3lBFginKYjljFPr.K',
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '藤本　太郎喜左衛門将時能',
            'email' => 'ccc@ccc.com',
            'admin_user' => 0,
            'password' => '$2y$10$UvsqfOZSTryRR7kiv29NkOBiMXbPzsjMUOTnr2D8w0DTE/lKvgzmG',
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);

        $rand10 = str_random(10);
        $mail = $rand10 .'@gmail.com';
        $password = bcrypt('secret');

        $param = [
            'name' => $rand10,
            'email' => $mail,
            'admin_user' => 0,
            'password' => $password,
            'last_access' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('users')->insert($param);
    }
}
