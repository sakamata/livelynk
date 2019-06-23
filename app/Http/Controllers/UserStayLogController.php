<?php

namespace App\Http\Controllers;

use App\UserStayLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserStayLogController extends Controller
{
    //
    public function __construct()
    {

    }

    // 1分毎にcron実行で滞在者の確認をする
    public function stayCheck()
    {

    }
}
