<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// 設定用の値を入れるModel set_key, set_value というカラムで各種設定を管理・保存、更新する
class SystemSetting extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'systems_settings';
    protected $fillable= array('set_key','set_value');
}
