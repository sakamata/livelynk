<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserTable extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'users';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        's_created_at',
        's_updated_at',
        's_last_access',
    ];


    // controller-----------
    // $hoge = 'App\UserTable'::find(1)->test;
    // view-----------------
    // @foreach($hoge as $var)
    // {{$var->name}}
    // @endforeach

    // done
    public function community()
    {
        return $this->belongsToMany('App\Community', 'community_user', 'user_id', 'community_id');
    }

    public function mac_addresses()
    {
        // 選択中のcommunity_idの範囲のみ端末を取得
        // MEMO hasManyThrough 第五引数の定義が日本語ドキュメントと異なり動作が怪しい
        $community_id = Auth::user()->community_id;
        return $this->hasManyThrough(
            'App\MacAddress',
            'App\CommunityUser',
            'community_user.user_id',
            'community_user_id',
            'user_id',
            'community_user.id'
            )
            ->where('community_user.community_id', $community_id)->orderBy('arraival_at', 'desc');
    }

    public function scopeUsersGet($query, $key, $order)
    {
        // 'mac_addresses.*',
        return $query->select([
            'users.name',
            'users.unique_name',
            'users.email',
            'users.provisional',
            'community_user.*',
            'communities_users_statuses.hide',
            'communities_users_statuses.last_access as s_last_access',
            'communities_users_statuses.created_at as s_created_at',
            'communities_users_statuses.updated_at as s_updated_at',
            'communities.name as community_name',
            'communities.service_name as community_service_name',
            'roles.role',
        ])
        ->Join('community_user', 'community_user.user_id', '=', 'users.id')
        ->Join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
        ->Join('roles', 'communities_users_statuses.role_id', '=', 'roles.id')
        // ->leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
        ->leftJoin('communities', 'community_user.community_id', '=', 'communities.id')
        ->orderBy($key, $order);
    }

    public function scopeSelf($query, $self_id)
    {
        return $query->where('community_user.id', $self_id);
    }

    public function scopeMyCommunity($query, $self_community)
    {
        return $query->where('community_user.community_id', $self_community);
    }

    public function scopeProvisional($query, $case)
    {
        switch ($case) {
            case 'index':
                $set = false;
            break;

            case 'provisional':
                $set = true;
            break;

            case null:
                return;
            break;
            
            default:
                return;
            break;
        }
        return $query->where('users.provisional', $set);
    }
}
