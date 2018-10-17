<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        'created_at',
        'updated_at',
    ];

    public function community()
    {
        return $this->belongsToMany('App\AdminCommunity', 'community_user');
    }


    public function mac_addresses()
    {
        return $this->hasMany('App\MacAddress', 'user_id')->orderBy('arraival_at', 'desc');
    }


    public function scopeSelf($query, $self_id)
    {
        return $query->where('id', $self_id);
    }

    public function scopeMyCommunity($query, $self_community)
    {
        return $query->where('community_id', $self_community);
    }

}
