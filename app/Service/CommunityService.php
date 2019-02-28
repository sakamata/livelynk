<?php

namespace App\Service;

use App\Community;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class CommunityService
{
    // IndexController community owner の user_id を取得
    public function GetOwnerUserID(int $community_id)
    {
        return 'App\Community'::where('id', $community_id)
            ->pluck('user_id')->first();
    }

    // InportPostController MacAddress
    public function NameGet(string $community_name)
    {
        return 'App\Community'::where('name', $community_name)->first();
    }
}
