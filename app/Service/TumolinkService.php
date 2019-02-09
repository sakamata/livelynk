<?php

namespace App\Service;

use DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class TumolinkService
{
    public function tumolistGet(int $community_id)
    {
        return DB::table('tumolink')
            ->select(
                'tumolink.*',
                'users.name',
                'users.name_reading',
                'users.provisional',
                'communities_users_statuses.hide'
            )
            ->join('community_user', 'community_user.id', '=', 'tumolink.community_user_id')
            ->join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
            ->join('users', 'users.id', '=', 'community_user.user_id')
            ->where('community_user.community_id', $community_id)
            ->get();
    }

}
