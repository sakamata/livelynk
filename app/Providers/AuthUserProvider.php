<?php
// Thanks!! https://blog.regrex.jp/2016/06/23/post-627/
namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Log;

class AuthUserProvider extends EloquentUserProvider
{
    // 認証時に作られる User model を複数tableから定義
    // Auth::user()->hoge で取得可能なカラムを定義

    public function retrieveById($identifier) {
        $result = $this->createModel()->newQuery()
            ->Join('community_user', 'community_user.user_id', '=', 'users.id')
            ->Join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
            ->Join('roles', 'communities_users_statuses.role_id', '=', 'roles.id')
            ->select([
                'users.*',
                'community_user.*',
                'communities_users_statuses.hide',
                'communities_users_statuses.last_access',
                'communities_users_statuses.created_at',
                'communities_users_statuses.updated_at',
                'roles.role',
            ])->find($identifier);
        return $result;
    }

/*
    public function retrieveByCredentials(array $credentials) {
        log::debug(print_r('credentials',1));
        log::debug(print_r($credentials,1));
        $result = $this->createModel()->newQuery()
            ->Join('community_user', 'community_user.user_id', '=', 'users.id')
            ->Join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
            ->Join('roles', 'communities_users_statuses.role_id', '=', 'roles.id')
            ->select([
                'users.*',
                'community_user.*',
                'communities_users_statuses.hide',
                'communities_users_statuses.last_access',
                'communities_users_statuses.created_at',
                'communities_users_statuses.updated_at',
                'roles.role',
            ])
            ->where(
                'community_user.id', $credentials['community_user.id']
            );
        return $result;
    }
*/

}
