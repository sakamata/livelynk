<?php

namespace App\Http\Controllers;

use App\UserStayLog;
use App\Service\UserStayLogService;
use App\Service\MacAddressService;
use App\Service\SystemSettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserStayLogController extends Controller
{
    public $userStayLogService;
    public $macAddressService;
    public $systemSettingService;
    public $lastLogCheckDatetime;
    public $now;

    public function __construct(
        UserStayLogService      $userStayLogService,
        MacAddressService       $macAddressService,
        SystemSettingService    $systemSettingService
        )
    {
        $this->userStayLogService   = $userStayLogService;
        $this->macAddressService    = $macAddressService;
        $this->systemSettingService = $systemSettingService;
        $this->lastLogCheckDatetime = $systemSettingService->getValue('last_log_check_datetime');
        $this->now = Carbon::now();

    }

    // 仮 一覧画面表示　tableの状態を出力して確認させるものから
    public function index(Request $request)
    {
        $recent_stay_ids = $this->macAddressService->getRecentStayIdsAndMaxPostedAt($this->lastLogCheckDatetime);
        $items = UserStaylog::all();
        return view('logs/index', [
            'items'           => $items,
            'recent_stay_ids' => $recent_stay_ids,
            'now'             => $this->now,
            'last_time'       => $this->lastLogCheckDatetime,
        ]);
    }

    /**
     * 1分毎のcronで滞在者の確認をする 来訪、更新、帰宅をusers_stays_logs tableに残す
     * 
     * @param void
     * @return void
     */
    public function stayCheck()
    {
        $time_obj = new Carbon($this->lastLogCheckDatetime);
        // 他のメソッドの引数にするのであえて文字列に変換する
        $last_check_time = $time_obj->toDateTimeString();

        // 通常のルーティン処理  >1分 || <=90分
        $update_ids_posted_at = $this->macAddressService->getRecentStayIdsAndMaxPostedAt($last_check_time);
        foreach ($update_ids_posted_at as $val) {
            // 更新判断
            // mac_address の last posted が  前回確認時間以降であれば 来訪中と判断
            //log.last_datetimeの値を mac.last_postedの値で更新する
            $this->userStayLogService->lastDatetimeUpdate($val->community_user_id, $val->posted_at);
            // 来訪判断
            $this->arraivalCheck($val->community_user_id, $last_check_time);
        }
        // 帰宅判断
        // 上のforeachに入れては駄目 取得できる時間の範囲外になる
        // log.departure_atがnull かつ log.last_datetime が調査時間より90分より大きければ90分前の時間を挿入する。
        $limit = Carbon::now();
        $pastLimit = $limit->subSeconds(env("JUDGE_STAY_LOGS_DEPARTURE_SECOND"));
        $this->userStayLogService->departurePastTimeUpdate($pastLimit);
        // 最終確認時間の更新
        $this->updateLastLogCheckDatetime();
    }

    /**
     * 来訪判断 foreach内の処理
     * 
     * @param int    $community_user_id
     * @param string $last_check_time  Y-m-d H:i:s
     * @return void
     */
    public function arraivalCheck(int $community_user_id, string $last_check_time)
    {
        // mac_address.arraival が最近であるか 前回確認時間基準
        $near_arraival_exists = $this->macAddressService->nearArraivalExists($community_user_id, $last_check_time);
        // user_stay_log 滞在中が重複していないか？ departure_at が空のカラムが存在していないか?
        $dupl_exists = $this->userStayLogService->ArraivalUserDuplicationCheck($community_user_id);
        if ($near_arraival_exists) {
            if (!$dupl_exists) {
                $this->userStayLogService->arraivalInsertNow($community_user_id, $this->now);
            }
        }
    }

    /**
     * 最終確認時間の更新
     * 
     * @param void
     * @return void
     */
    public function updateLastLogCheckDatetime()
    {
        $this->systemSettingService->updateValue('last_log_check_datetime', $this->now);
    }

    // 未使用
    // サーバー側長期停止後の状況復帰処理 強制帰宅処理を行う
    public function longTermStopForceDeparturesUpdate()
    {
        // 前回調査時間から90分より大きく空いていた場合は
        // 前回調査時間から90分後を仮の帰宅時間と想定し、帰宅処理を行う。
        $past_limit = Carbon::now()->subSeconds(env("JUDGE_DEPARTURE_INTERVAL_SECOND"));
        // 最終確認時間がn分前よりも過去ならば
        if ($this->lastLogCheckDatetime < $past_limit) {
            // 滞在中だったユーザーを一気に更新
            // 最終確認時間から帰宅判断時間以降を帰宅時間と想定して滞在中ユーザー全てを帰宅状態に変更
            $last_check = new Carbon($this->lastLogCheckDatetime);
            $departure_at = $last_check->addSeconds(env("JUDGE_DEPARTURE_INTERVAL_SECOND"));
            $this->userStayLogService->longTermStopAfterStayUsersChangeDeparture($departure_at);
        }
    }

    // 検討中　おそらく不要
    // ラズパイ側停止サーバー側長期停止後の状況復帰処理 通常判断時間より前のイレギュラーな来訪判断を行う
    public function longTermStopArraivaledUsersInsert()
    {
        // 来訪中のユーザーの確認
        // mac.arraival が log.deperture 最後のrecordより後（+90m）の時間なら、滞在中である。
            // idを配列で取得
        // かつ log.departure が null のレコードが無ければ、 滞在中として insert する
            // foreachで 確認 insert の処理
    }
}

// 実装前のロジックメモ
    // 来訪者を確認してtableに挿入する
        // 長期停止後に　対応する動作
        // 前回調査時間から90分より大きく空いていた場合は
            // 前回調査時間から90分後を仮の帰宅時間と想定し、帰宅処理を行う。
            // curret stay 1 を全て来訪者として登録する
            
        // 前回調査から <=90分であれば
            // 通常のルーティン処理

            // 通常のルーティン処理  >1分 || <=90分
            // 来訪判断
            // mac_address arraival_at の時間が前回チェック時間以降なら　かつlogのdepartureが90分以上前なら　前回のlogの 新規存在する ユーザーが来訪者と判断する

            // 更新判断
            // mac_address の last posted が 90分以内であれば来訪中と判断
                //log.last_datetimeの値を mac.last_postedの値で更新する

            // 帰宅判断
            // log.departure_atがnull かつ log.last_datetime が調査時間より90分より大きくなれば帰宅と判断し、 log.departure_at に 90分前の時間を挿入する。
