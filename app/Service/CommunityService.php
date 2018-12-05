<?php

namespace App\Service;

use App\Community;
use App\CommunityUserStatus;

use Illuminate\Support\Facades\Log;

/**
 *
 */
class CommunityService
{
    public function NameGet(string $community_name)
    {
        return 'App\Community'::where('name', $community_name)->first();
    }

}
