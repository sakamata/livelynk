<?php

namespace App\Service;

use App\UserTable;

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
}
