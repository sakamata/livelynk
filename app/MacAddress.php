<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MacAddress extends Model
{
    use HasFactory;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'mac_addresses';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'arraival_at',
        'departure_at',
        'posted_at',
        'm_created_at',
        'm_updated_at',
    ];

    public function community_user()
    {
        return $this->belongsTo('App\CommunityUser');
    }

    public function scopeSelf($query, $self_id)
    {
        return $query->where('user_id', $self_id);
    }

    public function scopeMyCommunity($query, $self_community)
    {
        return $query->where('community_id', $self_community);
    }

    public function scopeUserHaving($query, $community_user_id)
    {
        return $query->select([
            'mac_addresses.*',
            'community_user.user_id',
            'community_user.community_id',
        ])
            ->Join('community_user', 'community_user.id', '=', 'mac_addresses.community_user_id')
            ->where('community_user.id', $community_user_id);
    }
}
