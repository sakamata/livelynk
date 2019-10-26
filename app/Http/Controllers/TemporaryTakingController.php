<?php

namespace App\Http\Controllers;

use App\AuthUser;
use App\TalkMessage;
use App\Http\Controllers\ExportPostController;
use Illuminate\Http\Request;

class TemporaryTakingController extends Controller
{
    private $exportPostController;

    public function __construct(
        ExportPostController    $exportPostController
        )
    {
        $this->exportPostController = $exportPostController;
    }

    public function TakingRecorder(Request $request)
    {
        $request->validate([
            'community_user_id' => 'required|integer',
            'router_id'         => 'required|integer',
            'talking_message'   => 'required|max:140',
        ]);
        $user = AuthUser::find($request->community_user_id);
        if (is_null($user->name_reading)) {
            $name = $user->name;
        } else {
            $name = $user->name_reading;
        }

        // ifttt通知へ
        $this->exportPostController->temporaryTakingMessageMaker($user, $request->talking_message);

        // name max 30
        // 23文字 ライブリンクより、、さんからのメッセージです。
        $message = 'ライブリンクより、' . $name . '、さんからのメッセージです。 ' . $request->talking_message;

        $model = new TalkMessage();
        $model->router_id           = $request->router_id;
        $model->talking_message     = $message;
        $model->save();

        return redirect('/')->with('message', 'メッセージを受け付けました。');
    }
}
