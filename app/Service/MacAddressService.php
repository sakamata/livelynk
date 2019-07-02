<?php

namespace App\Service;
use DB;
use App\CommunityUser;
use App\MacAddress;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class MacAddressService
{
    // IndexController サブクエリ用の滞在中端末を取得
    public function GetStayMacAddressForSubQuery()
    {
        return 'App\MacAddress'::select(
            DB::raw("community_user_id, min(arraival_at) as min_arraival_at")
        )
        ->where([
            ['hide', false],
            ['current_stay', true],
        ])
        ->orderBy('min_arraival_at', 'desc')
        ->groupBy('community_user_id');
    }

    public function PersonHavingGet(int $community_user_id, int $community_id)
    {
        return 'App\MacAddress'::UserHaving($community_user_id)
            ->MyCommunity($community_id)
            ->orderBy('hide','asc')
            ->orderBy('arraival_at','desc')
            ->orderBy('mac_addresses.id', 'desc')
            ->get();
    }

    public function CommunityHavingMac(int $community_id, int $reader_id, string $order, string $key, string $case)
    {
        return 'App\CommunityUser'::CommunityHavingMac($community_id, $reader_id, $order, $key, $case)->get();
    }

    public function MacIDtoGetCommunityID(int $mac_address_id)
    {
        return 'App\CommunityUser'::MacIDtoGetCommunityID($mac_address_id);
    }

    public function Update(int $mac_id, $vendor,  $device_name, bool $hide, string $now)
    {
        return 'App\MacAddress'::where('id', $mac_id)
            ->update([
                'vendor'      => $vendor,
                'device_name' => $device_name,
                'hide'        => $hide,
                'updated_at'  => $now,
        ]);
    }

    public function UpdateChangeOwner(int $mac_id, $vendor, $device_name, bool $hide, string $now, int $community_user_id)
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

    public function UpdateProvisionalOwner(int $mac_id, int $old_community_user_id, int $new_community_user_id, string $now)
    {
        return 'App\MacAddress'::where([
                ['id', $mac_id],
                ['community_user_id', $old_community_user_id],
            ])->update([
                'updated_at' => $now,
                'community_user_id' => $new_community_user_id,
        ]);
    }

    // InportPostController MacAddress
    public function Arraival_at_Update(
        int $community_user_id,
        string $post_mac_hash,
        int $router_id,
        string $now
        )
    {
        'App\MacAddress'::where([
            ['community_user_id', $community_user_id],
            ['mac_address_hash', $post_mac_hash],
        ])->update([
            'router_id' => $router_id,
            'arraival_at' => $now,
            'current_stay' => true,
        ]);
        Log::debug(print_r('mac arraival_at update now!!!', 1));
    }

    // InportPostController MacAddress
    public function ThisUserExists(int $community_user_id)
    {
        return 'App\MacAddress'::where([
            ['community_user_id', $community_user_id],
            ['current_stay', true],
            ['hide', false],
        ])->exists();
    }

    // InportPostController MacAddress
    public function MacAddressStatusUpdate(
        int $community_user_id,
        string $post_mac_hash,
        int $router_id,
        string $now
        )
    {
        'App\MacAddress'::where([
            ['community_user_id', $community_user_id],
            ['mac_address_hash', $post_mac_hash],
            ['hide', false],
        ])->update([
            'router_id' => $router_id,
            'current_stay' => true,
            'posted_at' => $now,
            'updated_at' => $now,
        ]);
    }

    // 未使用　使わなくなっている
    // mac_address.posted_at がnowからn分以内の curret stay 1 のcommunity_user_idを配列で取得する
    // UserStayLogController->arraivalUsersRecords
    /**
     * @return array
     */
    public function getRecentStayCommunityUserIds(string $recent_datetime)
    {
        return 'App\MacAddress'::where([
                ['current_stay', true],
                ['posted_at', '>=', $recent_datetime],
            ])
            ->groupBy('community_user_id')
        ->pluck('community_user_id');
    }

    // mac_address.posted_at がnowからn分以内の curret stay 1 のcommunity_user_idとposted_atを取得する
    // UserStayLogController->stayCheck
    public function getRecentStayIdsAndMaxPostedAt(string $last_check_time)
    {
        return DB::select("
            SELECT
                community_user_id,
                current_stay,
                MAX(posted_at) AS posted_at
            FROM mac_addresses
            WHERE
                current_stay = true
                AND
                posted_at >= ?
            GROUP BY community_user_id
            ORDER BY community_user_id
        ", [$last_check_time]);
    }

    public function nearArraivalExists(int $community_user_id, string $past_limit)
    {
        return DB::table('mac_addresses')
            ->where([
                ['community_user_id', $community_user_id],
                ['arraival_at', '>', $past_limit]
            ])
            ->exists();
    }
}
