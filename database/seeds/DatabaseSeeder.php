<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TumolinkTableSeeder::class);
        $this->call(CommunitiesTableSeeder::class);
        $this->call(CommunitiesUsersStatusesTableSeeder::class);
        $this->call(CommunityUserTableSeeder::class);
        $this->call(MacAddressesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RoutersTableSeeder::class);
        $this->call(UserStayLogTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(GlobalIpTableSeeder::class);
    }
}
