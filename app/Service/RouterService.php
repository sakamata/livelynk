<?php

namespace App\Service;

use DB;
use App\Router;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class RouterService
{
    public function CommunityRouterGet(int $community_id)
    {
        return 'App\Router'::where('community_id', $community_id)
            ->orderBy('id', 'ASC')->first();
    }
}
