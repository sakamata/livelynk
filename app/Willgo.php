<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Willgo extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'willgo';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'from_datetime',
        'to_datetime',
        'maybe_departure_datetime',
        'created_at',
        'updated_at',
    ];

    public function community_user()
    {
        return $this->belongsTo('App\CommunityUser', 'community_user_id');
    }

    ////////////////////////////////////////////////////////////
    //// scope集 ここから  willgo の期間ごとの scopeを定義する
    ////////////////////////////////////////////////////////////

    /**
     * soon これから 現在以降、1時間以内
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeSoon($query)
    {
        return $query
            ->whereBetween('from_datetime', [
                Carbon::now(),
                Carbon::now()->addHour()
            ])
            ->where('to_datetime', '<', Carbon::today()->addDay());
    }

    /**
     * today きょう これからの範囲を除く本日
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeToday($query)
    {
        return $query
            ->whereBetween('from_datetime', [
                    Carbon::today(),
                    Carbon::now()
            ])
            ->orWhere(function ($query) {
                return $query->whereBetween('from_datetime', [
                    Carbon::now()->addHour(),
                    Carbon::today()->addDay()
                ]);
            })
            ->whereBetween('to_datetime', [
                Carbon::now()->addHour(),
                Carbon::today()->addDay()
            ]);
    }

    /**
     * tomorrow あした
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeTomorrow($query)
    {
        return $query
            ->whereBetween('from_datetime', [
                Carbon::today()->addDay(),
                Carbon::createFromTime(23, 59)->addDays(2)
            ])
            ->where('to_datetime', '<', Carbon::today()->addDays(2));
    }

    /**
     * dayAfterTomorrow あさって
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeDayAfterTomorrow($query)
    {
        return $query
            ->whereBetween('from_datetime', [
                Carbon::today()->addDays(2),
                Carbon::createFromTime(23, 59)->addDays(3)
            ])
            ->where('to_datetime', '<', Carbon::today()->addDays(3));
    }

    /**
     * ThisWeek 今週
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeThisWeek($query)
    {
        $from   = Carbon::today();
        // 月=1, 日=7
        $num    = Carbon::today()->dayOfWeekIso;

        // （過去）月曜日 0:00 を設定
        $subDay = $num - 1;
        $from   = Carbon::today()->subDays($subDay);

        // 日曜日 23:59 を設定
        $addDay = 7 - $num;
        $to     = Carbon::today()->addDays($addDay)->addHours(23)->addMinutes(59);

        return $query
            ->where('from_datetime', $from)
            ->where('to_datetime', $to);
    }

    /**
     * ThisWeekend 土日
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeWeekend($query)
    {
        // 月=1, 日=7
        $num    = Carbon::today()->dayOfWeekIso;
        $addDay = 7 - $num;
        // 土曜日 0:00 を設定
        $from   = Carbon::today()->addDays($addDay - 1);
        // 日曜日 23:59 を設定
        $to     = Carbon::today()->addDays($addDay)->addHours(23)->addMinutes(59);

        return $query
            ->where('from_datetime', $from)
            ->where('to_datetime', $to);
    }

    /**
     * nextWeek 来週
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeNextWeek($query)
    {
        // 月=1, 日=7 日曜日となる追加日を算出
        $addDay = 7 - Carbon::today()->dayOfWeekIso;
        // 来週月曜日 0:00 を設定
        $from   = Carbon::today()->addDays($addDay + 1);
        // 来週日曜日 23:59 を設定
        $to     = Carbon::today()->addDays($addDay + 7)->addHours(23)->addMinutes(59);

        return $query
            ->where('from_datetime', $from)
            ->where('to_datetime', $to);
    }

    /**
     * thisMonth 今月
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeThisMonth($query)
    {
        // 月初 0:00 を設定
        $day    = Carbon::today()->day;
        $from   = Carbon::today()->subDays($day - 1);
        // 月末 23:59:59.999999 を取得
        $to = Carbon::now()->endOfMonth();
        // 0秒に設定
        $to->second = 0;

        return $query
            ->where('from_datetime', $from)
            ->where('to_datetime', $to);
    }

    /**
     * NextMonth 来月
     *
     * @param object $query eroquent
     * @return void
     */
    public function scopeNextMonth($query)
    {
        // 来月初日 0:00 をセット
        $from = Carbon::now();
        $from->day  = 1;
        $from->hour = 0;
        $from->minute = 0;
        $from->second = 0;
        $from->addMonth();

        // 来月末 23:59:59.999999 を取得
        $to = Carbon::now()->addMonth()->endOfMonth();
        // 0秒に設定
        $to->second = 0;

        return $query
            ->where('from_datetime', $from)
            ->where('to_datetime', $to);
    }
    ////////////////////////////////////////////////////////////
    //// scope集 ここまで
    ////////////////////////////////////////////////////////////
}
