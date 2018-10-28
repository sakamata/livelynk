<?php

namespace App\Service;

use App\MacAddress;

/**
 *
 */
class MacAddressService
{
    public function PersonHavingGet(int $community_user_id, int $community_id)
    {
        return 'App\MacAddress'::UserHaving($community_user_id)
            ->MyCommunity($community_id)
            ->orderBy('hide','asc')
            ->orderBy('user_id', 'desc')
            ->orderBy('arraival_at','desc')
            ->get();
    }

    public function Update(int $mac_id, string $vendor, string $device_name, bool $hide, string $now)
    {
        return 'App\MacAddress'::where('id', $mac_id)
            ->update([
                'vendor'      => $vendor,
                'device_name' => $device_name,
                'hide'        => $hide,
                'updated_at'  => $now,
        ]);
    }

}
