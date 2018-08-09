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
        $items = 'App\MacAddress'::orderBy('updated_at', 'desc')->get();
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
        $users = DB::table('users')->get(['id', 'name']);

        // Log::debug(print_r($item, 1));

        return view('admin_mac_address.edit', [
            'item' => $item,
            'users' => $users,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'device_name' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:100',
            'hide' => 'required|boolean',
            'user_id' => 'required|integer',
        ]);

        $now = Carbon::now();
        $param = [
            'device_name' => $request->device_name,
            'vendor' => $request->vendor,
            'hide' => $request->hide,
            'user_id' => $request->user_id,
            'updated_at' => $now,
        ];
        'App\MacAddress'::where('id', $request->id)->update($param);
        return redirect('/admin_mac_address');
    }
}
