<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TalkMessage extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'talks_messages';

    public function router()
    {
        return $this->belongsTo('App\Router');
    }
}
