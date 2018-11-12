<?php
// Thanks!! https://blog.regrex.jp/2016/06/23/post-627/
namespace App\Providers;
use App\CommunityUser;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Log;

class AuthUserProvider extends EloquentUserProvider
{
    // 認証時に作られる User model を複数tableから定義
    // Auth::user()->hoge で取得可能なカラムを定義

    public function retrieveById($identifier) {

        $result = $this->createModel()->newQuery()
            ->where('users.id', $identifier)->first();

        $result->community_user = 'App\CommunityUser'::select([
            'community_user.*',
            'communities_users_statuses.hide',
            'communities_users_statuses.last_access',
            'communities_users_statuses.created_at',
            'communities_users_statuses.updated_at',
            'roles.role',
        ])
        ->Join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
        ->Join('roles', 'communities_users_statuses.role_id', '=', 'roles.id')
        ->where('user_id', $identifier)->orderBy('id')->get();
        return $result;
    }
}
