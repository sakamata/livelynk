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

    public function mac_addresses()
    {
        return $this->hasMany('App\MacAddress', 'user_id');
    }
}
