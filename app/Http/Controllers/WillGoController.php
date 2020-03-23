<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\WillgoRequest;
use App\Service\WillGoService;
use Exception;

class WillGoController extends Controller
{
    private $willGoService;

    public function __construct(
        WillGoService           $willGoService
    ) {
        $this->willGoService        = $willGoService;
    }

    // /**
    //  * test用メソッド使用しない
    //  *
    //  * @param Request $request
    //  * @return void
    //  */
    // public function index(Request $request)
    // {
    //     $willgoUsers = $this->willGoService->willGoUsersGet($request->community_id);
    //     return response()->json([
    //         'willgoUsers'    => $willgoUsers,
    //         'community_id'  => $request->community_id,
    //         'key' => 'value!'
    //     ], 200);
    // }

    /**
     * ヨテイの登録を行う
     *
     * @param App\Http\Requests\WillgoRequest $request
     * @return void
     */
    public function store(WillgoRequest $request)
    {
        /*
        $request->all();
        array:7 [
            "_token" => "UhmmpZej2VqIS5LNohDe2yC0jD8qIsxanILcqHY5"
            "community_user_id" => "3"
            "when" => "soon"
            "hour" => "0 .. 23"
            "minute" => "0 10 20 30 40 50"
            "action" => "willgo | go_back | cancel"
            "cancel_id" => "int"  willgo tableのIDを指定
            "google_home_push" => "0 | 1"
            "go_back_minute" => "30|60|90|120|180"
            "go_back_add_day" => "0|1|2"
        ]
        */
        if ($request->action == 'willgo') {

            // 宣言が更新かを確認し、必要なら更新を行い処理完了をする
            $res = $this->willGoService->checkUpdateReturnObject($request);
            if ($res) {
                // 更新の予定時間が既存の時間と重複していないか確認
                $isDuplicate = $this->willGoService->isDuplicateTime(
                    $res->from_datetime,
                    $request->hour,
                    $request->minute
                );
                if ($isDuplicate) {
                    return redirect('/')->with('message', 'すでにその日時は宣言済みです。');
                } else {
                    // 更新作業へ
                    return $this->update($res->id, $request);
                }
            }

            // return array
            // from => carbon datetime
            // to   => carbon datetime
            $datetimes = $this->willGoService->postDatetimeGenerater(
                (string)$request->when,
                (int)$request->hour,
                (int)$request->minute
            );
            DB::beginTransaction();
            try {
                $this->willGoService->willgoStore($request, $datetimes);
                $voiceMessage = $this->willGoService->willgoVoiceMessageMaker($request);
                $this->willGoService->storeGoogleHomeMessage($voiceMessage, $request->google_home_push);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                logger()->critical('error!'. __METHOD__);
                logger()->critical($e->getMessage());
                throw new $e;
                return redirect('/')->with('message', '帰る宣言が登録されませんでした。');
            }

            $textMessage = $this->willGoService->textMessageMaker($request);
            $this->willGoService->pushIfttt($textMessage, Auth::user()->community_id);

            return redirect('/')->with('message', 'ヨテイの宣言をしました。');
        }

        // 帰宅宣言の場合
        if ($request->action == 'go_back') {
            DB::beginTransaction();
            try {
                $this->willGoService->goBackStoreOrUpdate(
                    (int)$request->go_back_minute,
                    (int)$request->go_back_add_day, // 現状当日のみなので 0が送られる
                    (bool)$request->google_home_push
                );
                $voiceMessage = $this->willGoService->gobackVoiceMessageMaker(
                    (int)$request->go_back_minute,
                    (int)$request->go_back_add_day
                );
                $this->willGoService->storeGoogleHomeMessage($voiceMessage, $request->google_home_push);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                logger()->critical('error!'. __METHOD__);
                logger()->critical($e->getMessage());
                throw new $e;
                return redirect('/')->with('message', '帰る宣言が登録されませんでした。');
            }
            $textMessage = $this->willGoService->textMessageMakerForGoback($request);
            $this->willGoService->pushIfttt($textMessage, Auth::user()->community_id);

            return redirect('/')->with('message', '帰る宣言をしました。');
        }
    }

    /**
     * 予定の更新を行う
     *
     * @param integer $id
     * @param [type] $request
     * @return void
     */
    public function update(int $id, $request)
    {
        // return array
        // from => carbon datetime
        // to   => carbon datetime
        $datetimes = $this->willGoService->postDatetimeGenerater(
            (string)$request->when,
            (int)$request->hour,
            (int)$request->minute
        );

        DB::beginTransaction();
        try {
            // 既存の来訪宣言時間とPOST値が異なればupdate
            $this->willGoService->willgoUpdate($id, $request, $datetimes);

            // やっぱりの音声文言作成とDB POST
            $voiceMessage = $this->willGoService->voiceReMessageMaker($request);
            $this->willGoService->storeGoogleHomeMessage($voiceMessage, $request->google_home_push);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger()->critical('error!'. __METHOD__);
            logger()->critical($e->getMessage());
            throw new $e;
        }

        // やっぱりifttt文言作成とPOST
        $textMessage = $this->willGoService->textReMessageMaker($request);
        $this->willGoService->pushIfttt($textMessage, Auth::user()->community_id);

        return redirect('/')->with('message', 'ヨテイの宣言を更新しました。');
    }

    /**
     * 予定1件の削除を行う
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $validatedData = $request->validate([
            'when'  => [
                        'required',
                        'regex:/^(これから|きょう|あした|あさって|今週|土日|来週|今月|来月|goback)$/'
                    ],
        ]);

        $res = $this->willGoService->delete($request->id);
        if (!$res) {
            return redirect('/')->with('message', 'ヨテイを取り消しできませんでした。');
        }
        // 通知 iftttのみ
        $this->willGoService->deleteIiftttPush(
            Auth::user()->community_id,
            Auth::user()->name,
            $request->when
        );

        return redirect('/')->with('message', 'ヨテイを取り消しました。');
    }
}
