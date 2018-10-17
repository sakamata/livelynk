<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'roles';

    public function status()
    {
        return $this->hasMany('App\CommunityUserStatus');
    }

}
