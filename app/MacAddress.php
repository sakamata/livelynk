<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MacAddress extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'mac_addresses';

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
