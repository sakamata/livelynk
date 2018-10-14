<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array('normal', 'normalAdmin', 'readerAdmin', 'superAdmin');
        foreach ($roles as $role) {
            $param = [
                'role' => $role,
            ];
            DB::table('roles')->insert($param);
        }
    }
}
