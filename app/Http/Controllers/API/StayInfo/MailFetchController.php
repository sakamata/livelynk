<?php

namespace App\Http\Controllers\API\StayInfo;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StayInfo\PostRequest;

class MailFetchController extends Controller
{
    public function post(PostRequest $request)
    {
        // プレミアムラウンジ
        // 180.235.50.169
        // コミュニティラウンジまたはフリーデスクスペース
        // 202.213.146.42
        // domain linkdesign.jp

        return $request->all();
        // 既存データの存在確認
        // global_ip 既存の値

        // mail_box_domain  無ければ登録　更新は基本しない
        // mail_box_name 無ければ登録

        // global_ip が既存データだった場合

        //
        // mail_box_domain
        // mail_box_name
    }
}
