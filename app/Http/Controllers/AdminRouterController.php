<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\AdminRouter;

class AdminRouterController extends Controller
{
    // ***ToDo*** communitiesIDでデフォルトフィルタリング
    public function index(Request $request)
    {
        $items = 'App\AdminRouter'::get();
        return view('admin_router.index',[
            'items' => $items,
        ]);
    }

    public function create(Request $request)
    {
        // code...
    }

    public function edit(Request $request)
    {
        // code...
    }

    public function update(Request $request)
    {

    }
}
