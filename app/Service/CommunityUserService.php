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

    // return bool
    public function NewComerCheck(int $community_id_int, string $post_mac_hash)
    {
        return 'App\CommunityUser'::leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_user.community_id', $community_id_int],
                ['mac_addresses.mac_address_hash', $post_mac_hash],
        ])->exists();
    }
}
