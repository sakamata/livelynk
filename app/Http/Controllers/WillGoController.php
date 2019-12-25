<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\WillgoRequest;
use App\Service\WillGoService;

class WillGoController extends Controller
{
    private $willGoService;

    public function __construct(
        WillGoService           $willGoService
    ) {
        $this->willGoService        = $willGoService;
    }

    /**
     * test用メソッド使用しない
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $willgoUsers = $this->willGoService->willGoUsersGet($request->community_id);
        return response()->json([
            'willgoUsers'    => $willgoUsers,
            'community_id'  => $request->community_id,
            'key' => 'value!'
        ], 200);
    }

    public function store(WillgoRequest $request)
    {
        if ($request->action == 'willgo') {
            // return array
            // from => carbon datetime
            // to   => carbon datetime
            $datetimes = $this->willGoService->postDatetimeGenerater(
                (string)$request->when,
                (int)$request->hour,
                (int)$request->minute
            );
            // TODO トランザクション
            $this->willGoService->willgoStore($request, $datetimes);
            $voiceMessage = $this->willGoService->voiceMessageMaker($request);
            $this->willGoService->storeGoogleHomeMessage($voiceMessage, $request);

            $textMessage = $this->willGoService->textMessageMaker($request);
            $this->willGoService->pushIfttt($textMessage, Auth::user()->community_id);

            return redirect('/')->with('message', 'ヨテイの宣言をしました。');
        }

        /*
        array:7 [
            "_token" => "UhmmpZej2VqIS5LNohDe2yC0jD8qIsxanILcqHY5"
            "community_user_id" => "3"
            "when" => "soon"
            "hour" => "0 .. 23"
            "minute" => "0 10 20 30 40 50"
            "action" => "willgo | turnBack | cancel"
            "cancel_id" => "int"  willgo tableのIDを指定
            "google_home_push" => "0 | 1"
        ]
        */
    }
}
