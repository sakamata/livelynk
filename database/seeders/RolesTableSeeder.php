<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
