<?php

namespace App\Http\Controllers;

use App\CommunityUser;
use App\Community;
use App\MailBoxName;
use App\Http\Controllers\ExportPostController;
use App\Http\Controllers\InportPostController;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;

/**
 * タスクスケジュールで実行 \app\Console\Kernel.php
 */
class TaskController extends Controller
{
    private $inportPostController;

    public function __construct(
        InportPostController        $inportPostController
    ) {
        $this->inportPostController = $inportPostController;
    }
    /**
     * 1月以上来訪が無い 仮userを削除 端末, community_user id も削除する
     *
     * @return void
     */
    public function auto_provisional_user_remove()
    {
        $date = Carbon::now();
        $pastMonth = $date->subMonth(1);
        $res = DB::table('users')
            ->select('users.id as users_id', 'community_user.id as community_user_id', 'mac_addresses.id as mac_id')
            ->join('community_user', 'community_user.user_id', '=', 'users.id')
            ->join('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['mac_addresses.posted_at', '<=', $pastMonth],
                ['users.provisional',  true],
        ])->get();
        log::debug(print_r('Schedule TaskController@auto_provisional_user_remove run! delete records >>>', 1));
        log::debug(print_r($res, 1));
        foreach ($res as $key) {
            DB::table('users')->where('id', $key->users_id)->delete();
            DB::table('community_user')->where('id', $key->community_user_id)->delete();
            DB::table('communities_users_statuses')->where('id', $key->community_user_id)->delete();
            DB::table('mac_addresses')->where('id', $key->mac_id)->delete();
        }
    }

    /**
     * 前日の送信されなかった発話メッセージを削除する
     *
     * @return void
     */
    public function noUseTalksMessageRemove()
    {
        $date = Carbon::now();
        $past = $date->subDay(1);
        $model = 'App\TalkMessage'::where('created_at', '<', $past)->pluck('id')->toArray();
        'App\TalkMessage'::destroy($model);
    }

    /**
     * 端末からの一定時間(30分)以上POSTが無い各コミュニティの帰宅者判断を行う
     *
     * @return void
     */
    public function taskDepartureCheck()
    {
        $date = Carbon::now()->subMinute(1);
        $communityIds = 'App\Router'::Join(
            'communities',
            'communities.id',
            '=',
            'routers.community_id'
        )
            ->where('communities.enable', 1)
            ->where(function ($query) use ($date) {
                $query->where('last_post_datetime', '<', $date)
                    ->orWhere('last_post_datetime', null);
            })
            ->groupBy('community_id')
            ->pluck('community_id');

        foreach ($communityIds as  $communityId) {
            log::debug(print_r('Schedule TaskController@taskDepartureCheck run!  DepartureCheck community_id >>>' . $communityId, 1));
            $this->inportPostController->DepartureCheck($communityId);
        }
    }

    /**
     * MailBox式の帰宅処理と通知
     *
     * @return void
     */
    public function taskGlobalIpDepartureCheck()
    {
        logger()->debug('taskGlobalIpDepartureCheck run!');
        $communities = Community::whereNotNull('mail_box_domain')->get();

        foreach ($communities as $community) {
            $communityUserIds = CommunityUser::where('community_id', $community->id)
                ->pluck('id')->toArray();
            $mailBoxNames = MailBoxName::getNowDepartures($communityUserIds);

            $push_users = [];
            foreach ($mailBoxNames as $mailBoxName) {
                // 帰宅状態に更新
                $mailBoxName->setDeparture($mailBoxName->community_user_id);
                $push_users[] = [
                    "id" => $mailBoxName->communityUser->user_id,
                    "name" => $mailBoxName->communityUser->user->name,
                ];
            }
            if (!empty($push_users)) {
                // 帰宅通知
                logger()->debug('mailbox帰宅通知');
                $exportController = new ExportPostController();
                $exportController->access_message_maker(
                    $push_users,
                    $category = 'mail_fatch_departure',
                    $community->id
                );
            }
        }
    }
}
