<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class AuthUser extends Authenticatable
{
    use Notifiable;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'auth_users';

    /**
     * The attributes that should be hidden for arrays.
     * JSON出力の際誤って含めいない属性という事らしい
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'last_access',
        'created_at',
        'updated_at',
    ];
}
