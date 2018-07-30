<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminMacAddressController extends Controller
{
    public function index(Request $request)
    {
        $items = DB::table('mac_addresses')->get();
        return view('admin_mac_address.index', [
            'items' => $items,
        ]);
    }
}
