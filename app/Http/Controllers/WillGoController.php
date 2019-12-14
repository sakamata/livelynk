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

    public function index(Request $request)
    {
        return response()->json([
            'community_id' => $request->community_id,
            'key' => 'value!',
        ], 200);
    }

    public function store(WillgoRequest $request)
    {
        // return array
        // from => carbon datetime
        // to   => carbon datetime
        if ($request->action == 'willgo') {
            $datetimes = $this->willGoService->postDatetimeGenerater(
                (string)$request->when,
                (int)$request->hour,
                (int)$request->minute
            );
            // TODO トランザクション
            $this->willGoService->willgoStore($request, $datetimes);
            $textMessage = $this->willGoService->textMessageMaker($request);
            // TODO googlehome通知
            $voiceMessage = $this->willGoService->voiceMessageMaker($request);
            $this->willGoService->storeGoogleHomeMessage($voiceMessage, $request);

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
