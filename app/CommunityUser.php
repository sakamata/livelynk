<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityUser extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'community_user';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'last_access',
        'created_at',
        'updated_at',
        'arraival_at',
        'departure_at',
        'posted_at',
    ];

    public function status()
    {
        return $this->hasOne('App\CommunityUserStatus', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\UserTable');
    }

    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    public function mac_address()
    {
        return $this->hasMany('App\MacAddress');
    }

    public function scopeMacIDtoGetCommunityID($query, $mac_address_id)
    {
        return $this->where('id', $mac_address_id)->pluck('community_id')->first();
    }

    public function scopeCommunityHavingMac($query, $community_id, $reader_id, $order, $key, $case)
    {
        // user_id の where条件を切り替え
        // reader_id を除外か、それのみかで未登録一覧と登録済み一覧に分ける
        if ($case == 'index') {
            $cmp = '<>';
        } else {
            $cmp = '=';
        }
        return $query->select([
            'mac_addresses.*',
            'community_user.user_id',
            'community_user.community_id',
            'users.name as user_name',
            'communities.id as community_id',
            'communities.name as community_name',
            'communities.service_name as service_name',
            'routers.id as router_id',
            'routers.name as router_name',
        ])
            ->Join('mac_addresses', 'community_user.id', '=', 'mac_addresses.community_user_id')
            ->Join('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities', 'communities.id', '=', 'community_user.community_id')
            ->Join('routers', 'routers.id', '=', 'mac_addresses.router_id')
            ->where([
                ['community_user.community_id', $community_id],
                ['community_user.user_id', $cmp, $reader_id],
            ])
            ->orderBy($key, $order);
    }

    // 上と一本化したかったが、object取得が上手く行かず諦めた
    public function scopeSuperHavingMac($query)
    {
        return $query->select([
            'mac_addresses.*',
            'community_user.user_id',
            'community_user.community_id',
            'users.name as user_name',
            'communities.id as community_id',
            'communities.name as community_name',
            'communities.service_name as service_name',
            'routers.id as router_id',
            'routers.name as router_name',
        ])
            ->Join('mac_addresses', 'community_user.id', '=', 'mac_addresses.community_user_id')
            ->Join('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities', 'communities.id', '=', 'community_user.community_id')
            ->Join('routers', 'routers.id', '=', 'mac_addresses.router_id');
    }

}
