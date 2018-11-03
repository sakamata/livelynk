<?php

namespace App\Service;

use App\CommunityUser;
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

    public function CommunityHavingMac(int $community_id, int $reader_id, string $order, string $key, string $case)
    {
        return 'App\CommunityUser'::CommunityHavingMac($community_id, $reader_id, $order, $key, $case)->get();
    }

    public function SuperHavingMac()
    {
        return 'App\CommunityUser'::SuperHavingMac()->get();
    }

    public function MacIDtoGetCommunityID(int $mac_address_id)
    {
        return 'App\CommunityUser'::MacIDtoGetCommunityID($mac_address_id);
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

    public function UpdateChangeOwner(int $mac_id, string $vendor, string $device_name, bool $hide, string $now, int $community_user_id)
    {
        return 'App\MacAddress'::where('id', $mac_id)
            ->update([
                'vendor'      => $vendor,
                'device_name' => $device_name,
                'hide'        => $hide,
                'updated_at'  => $now,
                'community_user_id'  => $community_user_id,
        ]);
    }


}
