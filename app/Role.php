<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'roles';
    public $timestamps = false;

    public function status()
    {
        return $this->hasMany('App\CommunityUserStatus');
    }
}
