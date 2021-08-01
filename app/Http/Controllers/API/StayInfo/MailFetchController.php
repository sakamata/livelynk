<?php

namespace App\Http\Controllers\API\StayInfo;

use App\CommunityUser;
use App\GlobalIp;
use App\MailBoxName;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ExportPostController;
use App\Http\Requests\API\StayInfo\PostRequest;
use App\Service\UserService;
use DB;
use Illuminate\Support\Facades\Log;

class MailFetchController extends Controller
{
    public function post(PostRequest $request)
    {
        $communityUserIds = CommunityUser::getIds($request->community_id);
        $communityUserId = MailBoxName::getCommunityUserId($communityUserIds, $request->name);
        if (!$communityUserId) {
            // アカウント登録、来訪なら通知する
            return $this->userCreate($request);
        }

        // 滞在判定
        $isStay = GlobalIp::isStay($request->community_id, $request->global_ip);
        if (!$isStay) {
            $message = '非滞在中、処理無し';
            $request->merge(['status' => $message]);
            Log::debug($message);
            Log::debug($request->all());
            return $request->all();
        }

        $isArraival = MailBoxName::isArraival($communityUserId, $request);
        $mailBoxName = new MailBoxName();

        if (!$isArraival) {
            // 滞在更新
            $mailBoxName->setStayUpdate($communityUserId, $request);
            $message = '滞在更新';
            $request->merge(['status' => $message]);
            Log::debug($message);
            Log::debug($request->all());
            return $request->all();
        }

        // 来訪判定
        $mailBoxName->setArraival($communityUserId, $request);

        $export = new ExportPostController();
        $push_users = $this::setUserInfo($communityUserId);
        $export->access_message_maker(
            $push_users,
            $category = 'arraival',
            $request->community_id
        );

        $message = '来訪者あり';
        $request->merge(['status' => $message]);
        Log::debug($message);
        Log::debug($request->all());
        return $request->all();

        // memo 帰宅処理は以下で schedule 実行
        // App\Http\Controllers\TaskController->taskGlobalIpDepartureCheck()
    }

    /**
     * ユーザーを登録、来訪中ならば来訪セット
     * @param PostRequest $request
     * @return array
     */
    public function userCreate($request)
    {
        $name = $request->name;
        $domain = $request->domain;
        $communityId = $request->community_id;

        $userService = new UserService();
        DB::beginTransaction();
        try {
            $communityUserId = $userService->UserCreate(
                $name,
                $name_reading = null,
                $name . '@' . $domain,
                $name . '@' . $domain,
                $provisional = 1,
                $name,
                $communityId,
                $role_id = 1,
                $action = 'InportPostProvisional'
            );
            MailBoxName::store($request, $communityUserId);
            $message = '新規ユーザーを登録しました : ';

            $export = new ExportPostController();
            $push_users = $this::setUserInfo($communityUserId);
            // ユーザー登録をslack通知
            $export->access_message_maker(
                $push_users,
                $category = 'mail_box_create',
                $request->community_id
            );

            // アクセス元が登録済み global_ip なら初来訪処理を追加
            $isStay = GlobalIp::isStay($communityId, $request->global_ip);
            if ($isStay) {
                $mailBoxName = new MailBoxName();
                $mailBoxName->setArraival($communityUserId, $request);
                $message = $message . '新規ユーザー登録、初来訪';
                // 初来訪をslack通知
                $export->access_message_maker(
                    $push_users,
                    $category = 'mail_box_first_arraival',
                    $request->community_id
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('transaction'. __FILE__ .':'. __FUNCTION__ .':'.  __LINE__);
            report($e);
        }

        $request->merge(['status' => $message]);
        Log::debug($message);
        Log::debug($request->all());
        return $request->all();
    }

    protected static function setUserInfo(int $communityUserId)
    {
        $communityUser = CommunityUser::find($communityUserId);
        $push_users[] = [
            "id" => $communityUser->user_id,
            "name" => $communityUser->user->name,
        ];
        return $push_users;
    }
}
