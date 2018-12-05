<?php

namespace App\Service;

use App\CommunityUser;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class CommunityUserService
{
    public function GetStaysMacs(int $community_id_int)
    {
        return 'App\CommunityUser'::leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_user.community_id', $community_id_int],
                ['mac_addresses.current_stay', true],
        ])->pluck('mac_address_hash');
    }

    // InportPostController MacAddress
    // return bool
    public function NewComerCheck(int $community_id_int, string $post_mac_hash)
    {
        return 'App\CommunityUser'::leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_user.community_id', $community_id_int],
                ['mac_addresses.mac_address_hash', $post_mac_hash],
        ])->exists();
    }

    // InportPostController MacAddress
    public function GetLastAccsessUpdateMacAddress(int $community_id, string $post_mac_hash)
    {
        return 'App\CommunityUser'::select('community_user.id', 'departure_at')
            ->leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_user.community_id', $community_id],
                ['mac_addresses.hide', false],
                ['mac_addresses.mac_address_hash', $post_mac_hash],
        ])->first();
    }

    // InportPostController MacAddress
    public function GetPushUser(int $community_user_id)
    {
        return 'App\CommunityUser'::select('user_id', 'name', 'hide')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
            ->where([
                ['community_user.id', $community_user_id],
                ['hide', false],
        ])->first();
    }

}
