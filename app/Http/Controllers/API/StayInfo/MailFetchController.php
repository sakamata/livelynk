<?php

namespace App\Http\Controllers\API\StayInfo;

use App\CommunityUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\StayInfo\PostRequest;
use App\MailBoxName;
use App\Service\UserService;
use DB;
use Illuminate\Support\Facades\Log;

class MailFetchController extends Controller
{
    public function post(PostRequest $request)
    {

        // return $request->all();

        // 既存データの存在確認
        // 確認対象
        // global_ip => global_ips.global_ip
        // name => mail_box_names.mail_box_name

        // name  新規なら登録
        $communityUserId = $this->userSearchOrCreate($request);
        return $communityUserId;

        // 来訪判定

        // 非滞在
        // 来訪 通知
        // 滞在継続
        // 帰宅 通知

        // global_ip DB登録以外の値は来訪と見なさない
        // global_ip 既存の値

        // mail_box_name 無ければ登録

        // global_ip が既存データだった場合

    }

    /**
     * ユーザーを検索、無ければ生成、荒ればcommunity_user_idを返却
     * @param PostRequest $request
     * @return integer $communityUserId
     */
    public function userSearchOrCreate($request)
    {
        $name = $request->name;
        $domain = $request->domain;
        $communityId = $request->community_id;

        $communityUserIds = CommunityUser::getIds($communityId);
        $communityUserId = MailBoxName::getCommunityUserId($communityUserIds, $name);

        if (!$communityUserId) {
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
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('transaction'. __FILE__ .':'. __FUNCTION__ .':'.  __LINE__);
                report($e);
            }
        }

        return $communityUserId;
    }
}
