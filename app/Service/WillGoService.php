<?php

namespace App\Service;

use Auth;
use DB;
use App\Willgo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Community;
use App\CommunityUser;
use App\MacAddress;
use App\Http\Controllers\ExportPostController;
use App\TalkMessage;
use App\Repository\WillGoRepository;

/**
 *
 */
class WillGoService
{
    private $community;
    private $communityUser;
    private $exportPostController;
    private $macAddress;
    private $talkMessage;
    private $willGoRepository;

    public function __construct(
        Community           $community,
        CommunityUser       $communityUser,
        ExportPostController    $exportPostController,
        MacAddress          $macAddress,
        TalkMessage         $talkMessage,
        WillGoRepository    $willGoRepository
    ) {
        $this->community        = $community;
        $this->communityUser    = $communityUser;
        $this->exportPostController = $exportPostController;
        $this->macAddress       = $macAddress;
        $this->talkMessage      = $talkMessage;
        $this->willGoRepository = $willGoRepository;
    }

    public function willGoUsersGet(int $communityId)
    {
        $soon               = $this->soonGet($communityId);
        $today              = $this->todayGet($communityId);
        $tomorrow           = $this->tomorrowGet($communityId);
        $dayAfterTomorrow   = $this->dayAfterTomorrowGet($communityId);
        $thisWeek           = $this->thisWeekGet($communityId);
        $weekend            = $this->weekendGet($communityId);
        $nextWeek           = $this->nextWeekGet($communityId);
        $thisMonth          = $this->thisMonthGet($communityId);
        $nextMonth          = $this->nextMonthGet($communityId);
        return [
            'これから'  => $soon,
            'きょう'    => $today,
            'あした'    => $tomorrow,
            'あさって'  => $dayAfterTomorrow,
            '今週'      => $thisWeek,
            '土日'      => $weekend,
            '来週'      => $nextWeek,
            '今月'      => $thisMonth,
            '来月'      => $nextMonth,
        ];
    }

    public function soonGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->Soon()->get();
    }

    public function todayGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->Today()->get();
    }

    public function tomorrowGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->Tomorrow()->get();
    }

    public function dayAfterTomorrowGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->DayAfterTomorrow()->get();
    }

    public function thisWeekGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->ThisWeek()->get();
    }

    public function weekendGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->Weekend()->get();
    }

    public function nextWeekGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->nextWeek()->get();
    }

    public function thisMonthGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->ThisMonth()->get();
    }

    public function nextMonthGet(int $communityId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->NextMonth()->get();
    }

    /**
     * 投稿の時間帯からDBに登録する from,to の datetimeをセットし配列で返却する
     *
     * @param string    $when
     * @param int       $hour
     * @param int       $minute
     * @return array    from => carbon datetime, to => carbon datetime
     */
    public function postDatetimeGenerater(string $when, int $hour, int $minute)
    {
        switch ($when) {
            case 'soon':
                $datetimes = $this->willGoRepository->soonDateTimeSetter(
                    $hour,
                    $minute,
                    $case = 'soon'
                );
                break;

            case 'today':
                $datetimes = $this->willGoRepository->todayDateTimeSetter(
                    $hour,
                    $minute,
                    $case = 'today'
                );
                break;

            case 'tomorrow':
                $datetimes = $this->willGoRepository->tomorrowDateTimeSetter(
                    $hour,
                    $minute,
                    $case = 'tomorrow'
                );
                break;

            case 'dayAfterTomorrow':
                $datetimes = $this->willGoRepository->dayAfterTomorrowDateTimeSetter(
                    $hour,
                    $minute,
                    $case = 'dayAfterTomorrow'
                );
                break;

            case 'thisWeek':
                $datetimes = $this->willGoRepository->thisWeekDateTimeSetter();
                break;

            case 'weekend':
                $datetimes = $this->willGoRepository->thisWeekendTimeSetter();
                break;

            case 'nextWeek':
                $datetimes = $this->willGoRepository->nextWeekTimeSetter();
                break;

            case 'thisMonth':
                $datetimes = $this->willGoRepository->thisMonthTimeSetter();
                break;

            case 'nextMonth':
                $datetimes = $this->willGoRepository->nextMonthTimeSetter();
                break;

            // 初期値はひとまず soon と同じものを設定
            default:
                $datetimes = $this->willGoRepository->soonDateTimeSetter(
                    $hour,
                    $minute,
                    $case = 'soon'
                );
                break;
        }
        return $datetimes;
    }

    public function willgoStore($request, $datetimes)
    {
        return $this->willGoRepository->willgoStore($request, $datetimes);
    }

    /**
     * ifttt 通知メッセージのテキストを生成する（訪問予定）
     * @param object $request
     * @return string
     */
    public function textMessageMaker($request)
    {
        $userName       = Auth::user()->name;
        $when           = $this->varWhenTextChanger($request->when);
        $time           = $this->carbonTimeMaker($request->when, $request->hour, $request->minute);
        if ($time) {
            $time = $time->format('G:i');
        }
        $serviceName    = $this->community::find(Auth::user()->community_id)->service_name;

        return $userName . "「" . $when . $time . 'くらいに' . $serviceName . "に行くツモリ！」";
    }

    /**
     * 時間表記が必要かを判定し、nullか時間のCarbonオブジェクトを返却する
     *
     * @param Type $var
     * @return Carbon|null
     */
    public function carbonTimeMaker(string $when, string $hour, string $minute)
    {
        // 0時0分は時間指定無しとして扱う
        if ($hour == 0 && $minute == 0) {
            $time = null;
        } else {
            // 時間指定がある場合
            $now    = Carbon::now();
            $time   = Carbon::createFromTime($hour, $minute);
            // 指定時間が現在以前で いまから,今日 の指定なら時間をnullに
            if ($time->lt($now) && ($when == 'soon' || $when == 'today')) {
                $time = null;
            } elseif ($this->isNoUseTime($when)) {
                // 今週以降の時間指定不要な通知か判定
                $time = null;
            } else {
                // 返却される時間
                return $time;
            }
        }
    }

    /**
     * 時間の通知が必要ない通知かを判定する 不要なら true
     *
     * @param [type] $when
     * @return boolean
     */
    public function isNoUseTime($when)
    {
        $noUseTime = [
            'thisWeek',
            'weekend',
            'nextWeek',
            'thisMonth',
            'nextMonth'
        ];
        return array_key_exists($when, $noUseTime);
    }

    /**
     * 変数 $when の値を日本語変換する
     *
     * @param string $when
     * @return string
     */
    public function varWhenTextChanger($when)
    {
        $array = [
            'soon'              => 'これから',
            'today'             => 'きょう',
            'tomorrow'          => 'あした',
            'dayAfterTomorrow'  => 'あさって',
            'thisWeek'          => '今週',
            'weekend'           => '土日',
            'nextWeek'          => '来週',
            'thisMonth'         => '今月',
            'nextMonth'         => '来月'
        ];

        if (array_key_exists($when, $array)) {
            return $array[$when];
        } else {
            // 該当無しの場合
            return 'いつかは';
        }
    }

    /**
     * ifttt通知メソッドに渡す必要な引数を揃えて通知メソッドを実行する
     *
     * @param string $textMessage
     * @param integer $communityId
     * @return void
     */
    public function pushIfttt(string $textMessage, int $communityId)
    {
        $title = 'ヨテイのお知らせ';
        $community = $this->community::find($communityId);
        $this->exportPostController->push_ifttt($title, $textMessage, $community);
    }

    public function voiceMessageMaker($request)
    {
        $voiceMessage = 'ライブリンクよりお知らせです。';
        // よみがな優先で発話
        Auth::user()->name_reading ? $userName = Auth::user()->name_reading : $userName = Auth::user()->name;
        $when = $this->varWhenTextChanger($request->when);

        $time = $this->carbonTimeMaker($request->when, $request->hour, $request->minute);
        if ($time) {
            $time = $time->format('G時i分');
        }

        $voiceMessage .= $userName . 'さんが' . $when . $time . 'くらいに来るつもりみたいですよ。';

        return $voiceMessage;
    }

    /**
     * GoogheHome通知が必要であるかを判定し、必要ならメッセージを登録する
     *
     * @param string    $voiceMessage
     * @param object    $request
     * @return void
     */
    public function storeGoogleHomeMessage($voiceMessage, $request)
    {
        // メッセージ作成と記録の判定
        $googleHomeEnable = $this->community::find(Auth::user()->community_id)->google_home_enable;
        if ($request->google_home_push == false || $googleHomeEnable == false) {
            return;
        }

        // 発話tableに入れる
        $talkMessage = new $this->talkMessage();
        // ひままずcommunityの最初のrouterに紐づいたGoogleHomeを対象にする
        $router = 'App\Community'::find(Auth::user()->community_id)->router()->orderBy('id')->first();
        $talkMessage->router_id       = $router->id;
        $talkMessage->talking_message = $voiceMessage;
        $talkMessage->save();
    }

    public function willGoGet(int $community_id)
    {
        return DB::table('tumolink')
            ->select(
                'tumolink.*',
                'users.name',
                'users.name_reading',
                'users.provisional',
                'communities_users_statuses.hide'
            )
            ->join('community_user', 'community_user.id', '=', 'tumolink.community_user_id')
            ->join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
            ->join('users', 'users.id', '=', 'community_user.user_id')
            ->where('community_user.community_id', $community_id)
            ->get();
    }
}
