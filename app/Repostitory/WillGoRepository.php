<?php

namespace App\Repository;

use Auth;
use App\Community;
use App\CommunityUser;
use App\MacAddress;
use App\TalkMessage;
use App\Willgo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * 予定昨日のレポジトリ
 */
class WillGoRepository
{
    private $community;
    private $communityUser;
    private $macAddress;
    private $talkMessage;
    private $willgo;

    public function __construct(
        Community       $community,
        CommunityUser   $communityUser,
        MacAddress      $macAddress,
        TalkMessage     $talkMessage,
        Willgo          $willgo
    ) {
        $this->community        = $community;
        $this->communityUser    = $communityUser;
        $this->macAddress       = $macAddress;
        $this->talkMessage      = $talkMessage;
        $this->willgo           = $willgo;
    }

    public function willgoUsersGet(int $communityId)
    {
        return $this->willgo::
            select(
                'willgo.*',
                'users.name',
                'users.name_reading',
                'users.provisional',
                'communities_users_statuses.hide'
            )
            ->join('community_user', 'community_user.id', '=', 'willgo.community_user_id')
            ->join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
            ->join('users', 'users.id', '=', 'community_user.user_id')
            ->where('community_user.community_id', $communityId);
    }

    public function todayWillgoUsers(int $communityId)
    {
        return $this->willgo::
            select(
                'willgo.*',
                'users.name',
                'users.name_reading',
                'users.provisional',
                'communities_users_statuses.hide'
            )
            ->join('community_user', 'community_user.id', '=', 'willgo.community_user_id')
            ->join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
            ->join('users', 'users.id', '=', 'community_user.user_id')
            ->where('community_user.community_id', $communityId)
            ->whereBetween('from_datetime', [Carbon::today(),Carbon::today()->addHours(23)->addMinutes(59)])
            ->get();
    }

    /**
     * ヨテイの宣言の件数を取得する
     *
     * @param integer $communityId
     * @return integer
     */
    public function willGoCountGet(int $communityId)
    {
        return $this->willgo::
            join('community_user', 'community_user.id', '=', 'willgo.community_user_id')
            ->where('community_user.community_id', $communityId)
            ->count();
    }

    /**
     * いまから soon の際の from to の時間を配列で返却する
     *
     * @param   int     $hour
     * @param   int     $minute
     * @param   string  $case
     * @return array
     */
    public function soonDateTimeSetter(int $hour, int $minute, string $case)
    {
        $from   = $this->todayFromDatetimeSetter($hour, $minute, $case);
        $to     = Carbon::createFromTime(23, 59);
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     * きょう today の際の from to の時間を配列で返却する
     *
     * @param   int     $hour
     * @param   int     $minute
     * @param   string  $case
     * @return array
     */
    public function todayDateTimeSetter(int $hour, int $minute, string $case)
    {
        $from   = $this->todayFromDatetimeSetter($hour, $minute, $case);
        $to     = Carbon::createFromTime(23, 59);
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     * いまから・きょうの宣言の到着予告時間を判定し返却する
     *
     * @param   int     $hour
     * @param   int     $minute
     * @param   string  $case
     * @return  Carbon
     */
    public function todayFromDatetimeSetter(int $hour, int $minute, string $case)
    {
        $now = Carbon::now();
        if ($hour != 0 || $minute != 0) {
            // 時間指定がある場合
            $from   = Carbon::createFromTime($hour, $minute);
            $from   = $now->max($from); // 2つの日付のうち後のものを取得
        } else {
            // 時間指定が無い場合
            if ($case == 'soon') {
                // 今から　は1時間後
                $from   = Carbon::now()->addHour();
            } elseif ($case == 'today') {
                // きょう　なら0:00
                $from   = Carbon::today();
            }
        }
        return $from;
    }

    /**
     * あした tomorrow の際の from to の時間を配列で返却する
     *
     * @param   int     $hour
     * @param   int     $minute
     * @param   string  $case
     * @return array
     */
    public function tomorrowDateTimeSetter(int $hour, int $minute, string $case)
    {
        $from   = Carbon::tomorrow()->addHours($hour)->addMinutes($minute);
        $to     = Carbon::createFromTime(23, 59)->addDay();
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     * あさって dayAfterTomorrow の際の from to の時間を配列で返却する
     *
     * @param   int     $hour
     * @param   int     $minute
     * @param   string  $case
     * @return array
     */
    public function dayAfterTomorrowDateTimeSetter(int $hour, int $minute, string $case)
    {
        $from   = Carbon::today()->addDays(2)->addHours($hour)->addMinutes($minute);
        $to     = Carbon::createFromTime(23, 59)->addDays(2);
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     * 今週 thisWeek の際の from to の時間を配列で返却する
     *
     * @param void
     * @return array
     */
    public function thisWeekDateTimeSetter()
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
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     *  週末 weekend の際の from to の時間を配列で返却する
     *
     * @param void
     * @return array
     */
    public function thisWeekendTimeSetter()
    {
        // 月=1, 日=7
        $num    = Carbon::today()->dayOfWeekIso;
        $addDay = 7 - $num;
        // 土曜日 0:00 を設定
        $from   = Carbon::today()->addDays($addDay - 1);
        // 日曜日 23:59 を設定
        $to     = Carbon::today()->addDays($addDay)->addHours(23)->addMinutes(59);
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     *  来週 nextWeek の際の from to の時間を配列で返却する
     *
     * @param void
     * @return array
     */
    public function nextWeekTimeSetter()
    {
        // 月=1, 日=7 日曜日となる追加日を算出
        $addDay = 7 - Carbon::today()->dayOfWeekIso;
        // 来週月曜日 0:00 を設定
        $from   = Carbon::today()->addDays($addDay + 1);
        // 来週日曜日 23:59 を設定
        $to     = Carbon::today()->addDays($addDay + 7)->addHours(23)->addMinutes(59);
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     *  今月 thisMonth の際の from to の時間を配列で返却する
     *
     * @param void
     * @return array
     */
    public function thisMonthTimeSetter()
    {
        // 月初 0:00 を設定
        $day    = Carbon::today()->day;
        $from   = Carbon::today()->subDays($day - 1);
        // 月末 23:59:59.999999 を取得
        $to = Carbon::now()->endOfMonth();
        // 0秒に設定
        $to->second = 0;
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     *  来月 nextMonth の際の from to の時間を配列で返却する
     *
     * @param void
     * @return array
     */
    public function nextMonthTimeSetter()
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
        return [
            'from'  => $from,
            'to'    => $to
        ];
    }

    /**
     * willgo Table にヨテイを登録する
     * @param object    $request
     * @param array     $datetimes
     * @return void
     */
    public function willgoStore($request, array $datetimes)
    {
        $model = new $this->willgo();
        $model->community_user_id   = $request->community_user_id;
        $model->from_datetime       = $datetimes['from'];
        $model->to_datetime         = $datetimes['to'];
        $model->google_home_push    = $request->google_home_push;
        $model->save();
    }

    /**
     * 投稿の更新を行う
     *
     * @param integer   $id
     * @param object    $request
     * @param array     $datetimes
     * @return void
     */
    public function willgoUpdate(int $id, $request, array $datetimes)
    {
        $model = $this->willgo::find($id);
        $model->community_user_id   = $request->community_user_id;
        $model->from_datetime       = $datetimes['from'];
        $model->to_datetime         = $datetimes['to'];
        $model->google_home_push    = $request->google_home_push;
        $model->save();
    }

    /**
     * 更新対象の時間帯POSTが既に登録済みかを確認する
     *
     * @param object $request
     * @return Eloquent object|null
     */
    public function returnUpdateDataOrNull($request)
    {
        if ($request->when == 'soon' || $request->when == 'today') {
            return Willgo::select('id', 'from_datetime', 'to_datetime')
                ->where('community_user_id', Auth::user()->id)
                ->where('from_datetime', '>=', Carbon::today())
                ->where('to_datetime', '<=', Carbon::createFromTime(23, 59))
                ->first();
        }

        if ($request->when == 'tomorrow') {
            return Willgo::select('id', 'from_datetime', 'to_datetime')
                ->where('community_user_id', Auth::user()->id)
                ->where('from_datetime', '>=', Carbon::today()->addDay())
                ->where('to_datetime', '<=', Carbon::createFromTime(23, 59)->addDay())
                ->first();
        }

        if ($request->when == 'dayAfterTomorrow') {
            return Willgo::select('id', 'from_datetime', 'to_datetime')
                ->where('community_user_id', Auth::user()->id)
                ->where('from_datetime', '>=', Carbon::today()->addDays(2))
                ->where('to_datetime', '<=', Carbon::createFromTime(23, 59)->addDays(2))
                ->first();
        }

        return null;
    }

    /**
     * 予定1件の削除を行う
     *
     * @param integer $id
     * @return bool
     */
    public function delete(int $id)
    {
        $model = $this->willgo::find($id);
        $res = $model->delete();
        if (!$res) {
            logger()->warning('予定の削除に失敗');
            logger()->warning('Auth::user()->id>>>' . Auth::user()->id);
            return false;
        }
        return true;
    }
}
