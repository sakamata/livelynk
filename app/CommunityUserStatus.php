<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityUserStatus extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'communities_users_statuses';

    public function community_user()
    {
        return $this->hasOne('App\CommunityUser');
    }

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

}
