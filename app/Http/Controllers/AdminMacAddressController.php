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
        $request->validate([
            'id' => ['nullable','regex:/asc|desc/'],
            'current_stay' => ['nullable','regex:/asc|desc/'],
            'mac_address' => ['nullable','regex:/asc|desc/'],
            'vendor' => ['nullable','regex:/asc|desc/'],
            'arraival_at' => ['nullable','regex:/asc|desc/'],
            'departure_at' => ['nullable','regex:/asc|desc/'],
            'posted_at' => ['nullable','regex:/asc|desc/'],
        ]);

        // ***ToDo*** もう少しスマートに書けないものか?
        if ($request->id) {
            $order = $request->id;
            $key = 'id';
        }
        elseif ($request->current_stay) {
            $order = $request->current_stay;
            $key = 'current_stay';
        }
        elseif ($request->mac_address) {
            $order = $request->mac_address;
            $key = 'mac_address';
        }
        elseif ($request->vendor) {
            $order = $request->vendor;
            $key = 'vendor';
        }
        elseif ($request->arraival_at) {
            $order = $request->arraival_at;
            $key = 'arraival_at';
        }
        elseif ($request->departure_at) {
            $order = $request->departure_at;
            $key = 'departure_at';
        }
        elseif ($request->posted_at) {
            $order = $request->posted_at;
            $key = 'posted_at';
        } else {
            // default order
            $order = 'desc';
            $key = 'current_stay';
        }

        $items = 'App\MacAddress'::orderBy('hide', 'asc')
            ->orderBy($key, $order)
            ->get();
        return view('admin_mac_address.index', [
            'items' => $items,
            'order' => $order,
            'key' => $key,
        ]);
    }

    public function edit(Request $request)
    {
        // 不正なrequestはひとまず /へ飛ばす
        if (!$request->id || !ctype_digit($request->id)) {
            return redirect('/');
        }

        $item = 'App\MacAddress'::where('id', $request->id)->first();
        if (!$item) {
            return redirect('/');
        }
        $users = DB::table('users')->get(['id', 'name']);

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
