<?php

namespace App\Repository;

use DB;
use App\CommunityUser;
use App\MacAddress;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class MacAddressRepository
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

    public function PersonHavingGet(int $communityUserId, int $communityId)
    {
        return 'App\MacAddress'::UserHaving($communityUserId)
            ->MyCommunity($communityId)
            ->orderBy('hide', 'asc')
            ->orderBy('arraival_at', 'desc')
            ->orderBy('mac_addresses.id', 'desc')
            ->get();
    }

    public function Update(int $macId, $vendor, $deviceName, bool $hide, string $now)
    {
        return 'App\MacAddress'::where('id', $macId)
            ->update([
                'vendor'      => $vendor,
                'device_name' => $deviceName,
                'hide'        => $hide,
                'updated_at'  => $now,
        ]);
    }

    public function UpdateChangeOwner(int $macId, $vendor, $deviceName, bool $hide, string $now, int $communityUserId)
    {
        return 'App\MacAddress'::where('id', $macId)
            ->update([
                'vendor'            => $vendor,
                'device_name'       => $deviceName,
                'hide'              => $hide,
                'updated_at'        => $now,
                'community_user_id' => $communityUserId,
        ]);
    }

    public function UpdateProvisionalOwner(int $macId, int $oldCommunityUserId, int $newCommunityUserId, string $now)
    {
        return 'App\MacAddress'::where([
                ['id', $macId],
                ['community_user_id', $oldCommunityUserId],
            ])->update([
                'updated_at'        => $now,
                'community_user_id' => $newCommunityUserId,
        ]);
    }

    // InportPostController MacAddress
    public function ArraivalAtUpdate(
        int $communityUserId,
        string $postMacHash,
        int $routerId,
        string $now
    ) {
        'App\MacAddress'::where([
            ['community_user_id', $communityUserId],
            ['mac_address_hash', $postMacHash],
        ])->update([
            'router_id'     => $routerId,
            'arraival_at'   => $now,
            'current_stay'  => true,
        ]);
        Log::debug(print_r('mac arraival_at update now!!!', 1));
    }

    // InportPostController MacAddress
    public function IsUserExists(int $communityUserId)
    {
        return 'App\MacAddress'::where([
            ['community_user_id', $communityUserId],
            ['current_stay', true],
            ['hide', false],
        ])->exists();
    }

    // InportPostController MacAddress
    public function MacAddressStatusUpdate(
        int $communityUserId,
        string $postMacHash,
        int $routerId,
        string $now
    ) {
        'App\MacAddress'::where([
            ['community_user_id', $communityUserId],
            ['mac_address_hash', $postMacHash],
            ['hide', false],
        ])->update([
            'router_id'     => $routerId,
            'current_stay'  => true,
            'posted_at'     => $now,
            'updated_at'    => $now,
        ]);
    }

    // mac_address.posted_at がnowからn分以内の curret stay 1 のcommunity_user_idとposted_atを取得する
    // UserStayLogController->stayCheck
    public function getRecentStayIdsAndMaxPostedAt(string $lastCheckDatetime)
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
        ", [$lastCheckDatetime]);
    }

    public function nearArraivalExists(int $communityUserId, string $pastLimit)
    {
        return DB::table('mac_addresses')
            ->where([
                ['community_user_id', $communityUserId],
                ['arraival_at', '>', $pastLimit]
            ])
            ->exists();
    }

    /**
     * 端末が仮ユーザーか判定する
     * @param int $id  mac_addresses.id
     * @return object  int community_user_id
     * @return object  bool provisional
     */
    public function isDeviceProvisionUser(int $id)
    {
        return DB::table('mac_addresses')
        ->select(
            'users.provisional',
            'mac_addresses.community_user_id'
        )
        ->Join(
            'community_user',
            'community_user.id',
            '=',
            'mac_addresses.community_user_id'
        )
        ->Join(
            'users',
            'community_user.user_id',
            '=',
            'users.id'
        )
        ->where('mac_addresses.id', $id)->first();
    }
}
