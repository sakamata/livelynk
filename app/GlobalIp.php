<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalIp extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'global_ips';

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
