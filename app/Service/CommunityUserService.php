<?php

namespace App\Service;

use App\CommunityUser;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class CommunityUserService
{
    // IndexController index, mac_address のサブクエリを入れて現在の滞在者を取得
    public function StayUsersGet($sub_query, int $owner_id, int $community_id, bool $provisional)
    {
        // user_id, name, min_arraival_at, last_access
        return 'App\CommunityUser'::select('community_user.id AS id', 'user_id', 'unique_name', 'name', 'min_arraival_at', 'last_access')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
            ->JoinSub($sub_query, 'mac_addresses', function ($join) {
                $join->on('community_user.id', '=', 'mac_addresses.community_user_id');
            })
            ->where([
                ['user_id', '<>', $owner_id],
                ['community_id', $community_id],
                ['provisional', $provisional],
        ])->get();
    }

    // IndexController 該当コミュニティのuser id を配列で取得 （非表示user,readerAdmin除外）
    public function GetNotStayUsersIdObjectArray(int $owner_id, int $community_id)
    {
        return 'App\CommunityUser'::join(
            'communities_users_statuses' , 'community_user.id', '=', 'communities_users_statuses.id'
            )
            ->where([
                ['user_id', '<>', $owner_id],
                ['community_id', $community_id],
                ['hide', false],
        ])->pluck('user_id');
    }

    // IndexController で表示に使用する現在の滞在してないユーザーを取得
    public function NotStayUsersGet(int $community_id, array $not_stay_users_id)
    {
        return 'App\CommunityUser'::select('community_user.id AS id','user_id', 'name', 'last_access')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
            ->where([
                ['community_id', $community_id],
            ])
            ->whereIn('community_user.user_id', $not_stay_users_id)
            ->orderBy('last_access', 'desc')
        ->get();
    }

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
        return 'App\CommunityUser'::select(
            'community_user.id AS community_user_id',
            'user_id',
            'name',
            'name_reading',
            'hide'
            )
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
            ->where([
                ['community_user.id', $community_user_id],
                ['hide', false],
        ])->first();
    }

    // TumolinkController post()
    public function IsCurrentStay($community_user_id)
    {
        return 'App\CommunityUser'::leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['current_stay', true],
                ['community_user_id', $community_user_id],
            ])
        ->exists();
    }
}
