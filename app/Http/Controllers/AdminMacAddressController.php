<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\MacAddress;

class AdminMacAddressController extends Controller
{
    public function index(Request $request)
    {
        $items = DB::table('mac_addresses')->get();
        return view('admin_mac_address.index', [
            'items' => $items,
        ]);
    }

    public function edit(Request $request)
    {
        // 不正なrequestはひとまず /homeへ飛ばす
        if (!$request->id || !ctype_digit($request->id)) {
            return redirect('/home');
        }

        $item = 'App\MacAddress'::where('id', $request->id)->first();

        Log::debug(print_r($item, 1));

        return view('admin_mac_address.edit', [
            'item' => $item,
        ]);
    }

    public function update($value='')
    {
        // code...
    }
}
