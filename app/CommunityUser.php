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

    public function status()
    {
        return $this->hasOne('App\CommunityUserStatus');
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
}
