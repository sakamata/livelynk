<?php

namespace App\Service;

use DB;
use App\UserTable;
use App\CommunityUserStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 *
 */
class UserService
{
    public function PersonGet(int $community_user_id)
    {
        return 'App\UserTable'::UsersGet('community_user.id', 'asc')
            ->where('community_user.id', $community_user_id)
            ->first();
    }

    public function SelfCommunityUsersGet(string $orderkey, string $order, int $community_id)
    {
        return 'App\UserTable'::UsersGet($orderkey, $order)
            ->MyCommunity($community_id)
            ->get();
    }

    public function AllCommunityUsersGet(string $orderkey, string $order)
    {
        return 'App\UserTable'::UsersGet($orderkey, $order)
            ->get();
    }

    public function IDtoRoleGet(int $community_user_id)
    {
        return 'App\CommunityUserStatus'::IDtoRoleGet($community_user_id);
    }

    public function UserCreate(
        string $name = null,
        string $unique_name,
        string $email = null,
        string $password,
        int $community_id,
        int $role_id,
        string $action
    ) {
        $now = Carbon::now();
        $user_id = 'App\UserTable'::insertGetId([
            'name' => $name,
            'unique_name' => $unique_name,
            'email' => $email,
            'password' => Hash::make($password),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        // 中間tableに値を入れる
        $community_user_id = DB::table('community_user')->insertGetId([
            'community_id' => $community_id,
            'user_id' => $user_id,
        ]);
        // user status管理のtableに値を入れる
        DB::table('communities_users_statuses')->insert([
            'id' => $community_user_id,
            'role_id' => $role_id,
            'hide' => 0,
            'last_access' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($action == 'AdminUserCreate') {
            return $community_user_id;
        }
        if ($action == 'AdminCommunityCreate') {
            return $user_id;
        }
    }
}
