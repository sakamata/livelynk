<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // 何故 $order['column'] 的なキー使うのに気づかなかったのか？
        // そのうち書き換える
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

        $user = Auth::user();
        $reader_id = $this->getReaderID();

        // ユーザーロールで表示範囲を変える
        switch ($user->role) {
            // 通常ユーザーは自分のIDとリーダーIDのみ表示
            case 'normal':
                $items = 'App\MacAddress'::Self($user->id)
                    ->orWhere('user_id', $reader_id)
                    ->orderBy('hide', 'asc')
                    ->orderBy($key, $order)->get();
            break;

            // サービス全管理者は全て表示
            case 'superAdmin':
                $items = 'App\MacAddress'::orderBy('hide', 'asc')
                    ->orderBy($key, $order)->get();
            break;

            default:
            // normalAdmin & readerAdmin はcommunityの範囲で表示
                $items = 'App\MacAddress'::MyCommunity($user->community_id)
                    ->orderBy('hide', 'asc')
                    ->orderBy($key, $order)->get();
            break;
        }
        return view('admin_mac_address.index', [
            'items' => $items,
            'order' => $order,
            'key' => $key,
        ]);
    }

    public function edit(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $item = 'App\MacAddress'::where('id', $request->id)->first();
        if (!$item) {
            return view('errors.403');
        }

        $user = Auth::user();
        $reader_id = $this->getReaderID();
        // normal userが自分かreaderのID以外を編集しようとした場合は403
        if ( $user->role == 'normal' &&
            $item->user_id != $user->id &&
            $item->user_id != $reader_id
        ) {
            return view('errors.403');
        }
        // normalAdmin,readerAdminで自コミュニティ以外は403
        if (
            ( $user->role == 'normalAdmin' ||  $user->role == 'readerAdmin' ) &&
            $item->community_id != $user->community_id
        ) {
            return view('errors.403');
        }

        // 自分と管理者（未登録）のみをリストアップ
        if ($user->role == 'normal') {
            $users = DB::table('users')->where('id', $user->id)
                ->orWhere('id', $reader_id)->get(['id', 'name']);
        }
        // 自分のコミュニティに紐づいたものをリストアップ
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') {
            $users = DB::table('users')
                ->where('community_id', $user->community_id)
                ->get(['id', 'name']);
        }
        // 全部リストアップ
        if ($user->role == 'superAdmin') {
            $users = DB::table('users')->get(['id', 'name']);
        }

        return view('admin_mac_address.edit', [
            'item' => $item,
            'users' => $users,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'community_id' => 'required|integer',
            'device_name' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:100',
            'hide' => 'required|boolean',
            'user_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $reader_id = $this->getReaderID();

        // normal userが自分かreaderのID以外を編集しようとした場合は403
        if ( $user->role == 'normal' &&
            $request->user_id != $user->id &&
            $request->user_id != $reader_id
        ) {
            log::warning(print_r("normalユーザーが異常な値でmac_addressをupdateを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ( $user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でmac_addressをupdateを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

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
