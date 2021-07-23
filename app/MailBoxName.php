<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailBoxName extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'mail_box_names';

    public function communityUser()
    {
        return $this->hasMany(CommunityUser::class);
    }
}
