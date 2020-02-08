<?php

namespace App\Service;

use Auth;
use DB;
use App\Willgo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Community;
use App\CommunityUser;
use App\CommunityUserStatus;
use App\MacAddress;
use App\Service\MacAddressService;
use App\Http\Controllers\ExportPostController;
use App\TalkMessage;
use App\Repository\WillGoRepository;

/**
 * 予定機能のサービス
 */
class WillGoService
{
    private $community;
    private $communityUserStatus;
    private $exportPostController;
    private $macAddress;
    private $macAddressServece;
    private $talkMessage;
    private $willGoRepository;

    public function __construct(
        Community               $community,
        CommunityUserStatus     $communityUserStatus,
        ExportPostController    $exportPostController,
        MacAddress              $macAddress,
        MacAddressService       $macAddressServece,
        TalkMessage             $talkMessage,
        WillGoRepository        $willGoRepository
    ) {
        $this->community            = $community;
        $this->communityUserStatus  = $communityUserStatus;
        $this->exportPostController = $exportPostController;
        $this->macAddress           = $macAddress;
        $this->macAddressServece    = $macAddressServece;
        $this->talkMessage          = $talkMessage;
        $this->willGoRepository     = $willGoRepository;
    }

    /**
     * ヨテイ宣言を行ったユーザー一覧の情報を出力
     *
     * @param integer   $communityId
     * @param array     $staysUsersId
     * @return array
     */
    public function willGoUsersGet(int $communityId, array $staysUsersId)
    {
        $soon               = $this->soonGet($communityId, $staysUsersId);
        $today              = $this->todayGet($communityId, $staysUsersId);
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

    /**
     * ヨテイの宣言の件数を取得する
     *
     * @param integer   $communityId
     * @return integer
     */
    public function willGoCountGet(int $communityId)
    {
        return $this->willGoRepository->willGoCountGet($communityId);
    }

    public function getTodayWillgoUsersIds(int $communityId)
    {
        return $this->willGoRepository->getTodayWillgoUsersIds($communityId);
    }

    public function todayWillgoUsers(int $communityId, array $staysUsersId)
    {
        return $this->willGoRepository->todayWillgoUsers($communityId, $staysUsersId);
    }

    public function soonGet(int $communityId, array $staysUsersId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->whereNotIn('community_user.id', $staysUsersId)
                    ->Soon()->get();
    }

    public function todayGet(int $communityId, array $staysUsersId)
    {
        $query = $this->willGoRepository->willgoUsersGet($communityId);
        return $query->whereNotIn('community_user.id', $staysUsersId)
                    ->Today()->get();
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
     * 帰宅宣言をしたユーザーを取得
     *
     * @param integer $communityId
     * @param array   $todayGobackUsers
     * @return Illuminate\Database\Eloquent\Collection
     */
    // 現状は今日の宣言のみ取得
    // TODO 明日、明後日の帰るリストをこのメソッドで平行して呼んでまとめる
    public function gobackUsers(int $communityId, array $todayGobackUsers)
    {
        return $this->willGoRepository->todayGobackUsers(
            (int)$communityId,
            (array)$todayGobackUsers
        );
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

    /**
     * ヨテイ表示のプルダウンリスト出力で必要なもののみを生成する
     * 既に宣言しているリストは出力させない
     * 来訪中は今日、これからを出力させない
     *
     * @param void
     * @return array|null
     */
    public function willgoPullDownListGet()
    {
        if (!Auth::check()) {
            return null;
        }
        $id = Auth::user()->id;
        // 該当ユーザーが来訪中の場合は「今日、これから」 表示プルダウンから除外
        $isNowStay = $this->macAddressServece->ThisUserExists($id);
        $array = [];
        if (!$isNowStay) {
            $array =  [
                ["when" => "soon",              "text" => "これから"],
                ["when" => "today",             "text" => "きょう"],
            ];
        }

        $array  = array_merge($array, array(
            ["when" => "tomorrow",          "text" => "あした"],
            ["when" => "dayAfterTomorrow",  "text" => "あさって"]
        ));

        $collection = collect($array);


        if (!Willgo::where('community_user_id', $id)->ThisWeek()->exists()) {
            $collection = $collection->concat([["when" => "thisWeek", "text" => "今週"]]);
        }

        if (!Willgo::where('community_user_id', $id)->Weekend()->exists()) {
            $collection = $collection->concat([["when" => "weekend", "text" => "土日"]]);
        }

        if (!Willgo::where('community_user_id', $id)->NextWeek()->exists()) {
            $collection = $collection->concat([["when" => "nextWeek", "text" => "来週"]]);
        }

        if (!Willgo::where('community_user_id', $id)->ThisMonth()->exists()) {
            $collection = $collection->concat([["when" => "thisMonth", "text" => "今月"]]);
        }

        if (!Willgo::where('community_user_id', $id)->NextMonth()->exists()) {
            $collection = $collection->concat([["when" => "nextMonth", "text" => "来月"]]);
        }

        return $collection;
    }

    /**
     * willgo Table にヨテイを登録するメソッドの呼び出し
     *
     * @param object    $request
     * @param array     $datetimes
     * @return void
     */
    public function willgoStore($request, array $datetimes)
    {
        return $this->willGoRepository->willgoStore($request, $datetimes);
    }

    /**
     * willgo Table にヨテイを更新するメソッドの呼び出し
     *
     * @param integer   $id
     * @param object    $request
     * @param array     $datetimes
     * @return void
     */
    public function willgoUpdate(int $id, $request, array $datetimes)
    {
        return $this->willGoRepository->willgoUpdate($id, $request, $datetimes);
    }

    public function goBackStoreOrUpdate(int $minute, int $gobackAddDay, bool $googleHomePush)
    {
        return $this->willGoRepository->goBackStoreOrUpdate(
            (int)$minute,
            (int)$gobackAddDay,
            (bool)$googleHomePush
        );
    }

    /**
     * 更新が必要かを確認、更新なら必要なオブジェクトを返却
     *
     * @param   object      $request
     * @return  object|null
     */
    public function checkUpdateReturnObject($request)
    {
        // 更新対象の時間帯のPOSTの確認、必要ならupdateを行う
        $checkArray = ['soon', 'today', 'tomorrow', 'dayAfterTomorrow'];
        if (in_array($request->when, $checkArray)) {
            return $this->willGoRepository->returnUpdateDataOrNull($request);
        }
        return null;
    }

    /**
     * 投稿された予定の時間指定が以前のものと重複しているかを確認する
     *
     * @param string $fromDatetime  YYYY-mm-dd hh:mm:ss
     * @param integer $hour
     * @param integer $minute
     * @return boolean
     */
    public function isDuplicateTime(string $fromDatetime, int $hour, int $minute)
    {
        $from = new Carbon($fromDatetime);
        // 重複判定 from の時間が既存レコードと同じか？
        if ($from->hour == $hour && $from->minute == $minute) {
            // 投稿前と同じ時間だった場合
            return true;
        } else {
            return false;
        }
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

        return $userName . "「" . $when . $time . 'くらいに' . $serviceName . "に行くかも」";
    }

    /**
     * ifttt 通知メッセージのテキストを生成する（訪問時間更新）
     * @param object $request
     * @return string
     */
    public function textReMessageMaker($request)
    {
        $userName       = Auth::user()->name;
        $when           = $this->varWhenTextChanger($request->when);
        $time           = $this->carbonTimeMaker($request->when, $request->hour, $request->minute);
        if ($time) {
            $time = $time->format('G:i');
        }
        $serviceName    = $this->community::find(Auth::user()->community_id)->service_name;

        return $userName . "「やっぱり" . $when . $time . 'くらいに' . $serviceName . "に行くかも」";
    }

    /**
     * ifttt 通知メッセージのテキストを生成する（帰宅予定）
     * @param object $request
     * @return string
     */
    public function textMessageMakerForGoback($request)
    {
        $userName   = Auth::user()->name;
        $minute     = $request->go_back_minute;
        if ($minute == 30) {
            $time = 'そろそろ';
        } elseif ($minute == 60) {
            $time = '1時間後くらいに';
        } else {
            $time = Carbon::now()->addMinutes($minute)->format('G時i分') . 'くらいに';
        }
        $when           = $this->varGobackAddDayTextChanger($request->go_back_add_day);
        $serviceName    = $this->community::find(Auth::user()->community_id)->service_name;
        return $userName . "「" . $when . $time . 'くらいに' . $serviceName . "から帰るかも」";
    }

    /**
     * 時間表記が必要かを判定し、nullか時間のCarbonオブジェクトを返却する
     *
     * @param string $when
     * @param string $hour
     * @param string $minute
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
     * @param string $when
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
     * 変数 $when の値を日本語変換する
     *
     * @param string $gobackAddDday
     * @return string
     */
    public function varGobackAddDayTextChanger($gobackAddDday)
    {
        $array = [
            '0' => '',
            '1' => 'あした',
            '2' => 'あさって'
        ];

        if (array_key_exists($gobackAddDday, $array)) {
            return $array[$gobackAddDday];
        } else {
            // 該当無しの場合
            return 'そのうち';
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

    /**
     * 来訪予定の音声メッセージのテキストを作成する
     *
     * @param object $request
     * @return string
     */
    public function willgoVoiceMessageMaker($request)
    {
        $voiceMessage = 'ライブリンクよりお知らせです。';
        // よみがな優先で発話
        Auth::user()->name_reading ? $userName = Auth::user()->name_reading : $userName = Auth::user()->name;
        $when = $this->varWhenTextChanger($request->when);

        $time = $this->carbonTimeMaker($request->when, $request->hour, $request->minute);
        if ($time) {
            $time = $time->format('G時i分');
        }

        $voiceMessage .= $userName . 'さんが' . $when . $time . 'くらいに来るかもしれないです。';

        return $voiceMessage;
    }

    /**
     * 予定(更新)の音声メッセージのテキストを作成する
     *
     * @param object $request
     * @return string
     */
    public function voiceReMessageMaker($request)
    {
        $voiceMessage = 'ライブリンクよりお知らせです。';
        // よみがな優先で発話
        Auth::user()->name_reading ? $userName = Auth::user()->name_reading : $userName = Auth::user()->name;
        $when = $this->varWhenTextChanger($request->when);

        $time = $this->carbonTimeMaker($request->when, $request->hour, $request->minute);
        if ($time) {
            $time = $time->format('G時i分');
        }

        $voiceMessage .= $userName . 'さんが、予定を更新しました。やっぱり' . $when . $time . 'くらいに来るかもしれないです。';

        return $voiceMessage;
    }

    /**
     * 帰宅予定の音声メッセージのテキストを作成する
     *
     * @param integer $minute
     * @param integer $addDay
     * @return string
     */
    public function gobackVoiceMessageMaker(int $minute, int $addDay)
    {
        $voiceMessage = 'ライブリンクよりお知らせです。';
        // よみがな優先で発話
        Auth::user()->name_reading ? $userName = Auth::user()->name_reading : $userName = Auth::user()->name;
        $when = $this->varGobackAddDayTextChanger($addDay);

        if ($minute == 30) {
            $time = 'そろそろ';
        } elseif ($minute == 60) {
            $time = '1時間後くらいに';
        } else {
            $time = Carbon::now()->addMinutes($minute)->format('G時i分') . 'くらいに';
        }

        $voiceMessage .= $userName . 'さんが' . $when . $time . '帰るみたいです。';
        logger()->debug($voiceMessage);
        return $voiceMessage;
    }

    /**
     * GoogheHome通知が必要であるかを判定し、必要ならメッセージを登録する
     *
     * @param string    $voiceMessage
     * @param object    $request
     * @return void
     */
    public function storeGoogleHomeMessage(string $voiceMessage, bool $isPush)
    {
        // 記録の判定
        $googleHomeEnable = $this->community::find(Auth::user()->community_id)->google_home_enable;
        if ($isPush == false || $googleHomeEnable == false) {
            return;
        }
        $this->willGoRepository->storeGoogleHomeMessage((string)$voiceMessage);
    }

    /**
     * 予定1件の削除を行う
     *
     * @param integer $request
     * @return bool
     */
    public function delete(int $id)
    {
        $model = WillGo::find($id);

        if (Auth::user()->id != $model->community_user_id) {
            logger()->warning('異なるユーザーが予定削除を試みる');
            logger()->warning('Auth::user()->id>>>' . Auth::user()->id);
            return false;
        }

        return $this->willGoRepository->delete($id);
    }

    /**
     * 予定削除の通知をIFTTTに送信する
     *
     * @param integer $communityId
     * @param string $userName
     * @param string $when
     * @return void
     */
    public function deleteIiftttPush(int $communityId, string $userName, string $when)
    {
        if ($when != 'goback') {
            $this->willgoDeleteIiftttPush($communityId, $userName, $when);
        } else {
            $this->gobackDeleteIiftttPush($communityId, $userName);
        }
    }

    /**
     * 来訪予定の宣言をキャンセルする通知をiftttにpushする
     * @param integer $communityId
     * @param string $userName
     * @param string $when
     * @return void
     */
    public function willgoDeleteIiftttPush(int $communityId, string $userName, string $when)
    {
        $textMessage = $userName . "「". $when . "くらいに行くのはやっぱりやめるかも」";
        $this->pushIfttt($textMessage, $communityId);
    }

    /**
     * 帰宅予定の宣言をキャンセルする通知をiftttにpushする
     * @param integer $communityId
     * @param string $userName
     * @return void
     */
    public function gobackDeleteIiftttPush(int $communityId, string $userName)
    {
        $textMessage = $userName . "「帰るのやめて、もう少しいるかも」";
        $this->pushIfttt($textMessage, $communityId);
    }
}
