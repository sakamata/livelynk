<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\MacAddress;
use App\Service\UserService;
use App\Service\MacAddressService;

// normal user は閲覧、操作権限無し
class AdminMacAddressController extends Controller
{
    private $call_user;
    private $call_mac;

    public function __construct(
        UserService $call_user,
        MacAddressService $call_mac
        )
    {
        $this->call_user = $call_user;
        $this->call_mac = $call_mac;
    }

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
        if ($request->current_stay) {
            $order = $request->current_stay;
            $key = 'current_stay';
        }
        elseif ($request->mac_address) {
            $order = $request->mac_address;
            $key = 'mac_address';
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
            $key = 'posted_at';
        }

        $user = Auth::user();
        $reader_id = $this->getReaderID();
        // ユーザーロールで表示範囲を変える
        switch ($user->role) {
            // サービス全管理者は全て表示
            case 'superAdmin':
                $items = $this->call_mac->SuperHavingMac();
                $users = $this->call_user
                    ->AllCommunityUsersGet('user_id', 'desc', (int)$user->community_id);
            break;

            default:
                // normalAdmin & readerAdmin はcommunityの範囲で表示、未登録端末を除外
                $reader_id = $this->getReaderID();
                $items = $this->call_mac->CommunityHavingMac($user->community_id, $reader_id, $order, $key);
                // communityのユーザーlistを取得
                $users = $this->call_user
                    ->SelfCommunityUsersGet('user_id', 'desc', (int)$user->community_id);
            break;
        }

        return view('admin_mac_address.index', [
            'items' => $items,
            'order' => $order,
            'key' => $key,
            'user' => $user,
            'users' => $users,
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
        // ***ToDo*** view側の old（配列？）の値が取れていない
        $request->validate([
            'id' => 'required|integer',
            'vendor' => 'nullable|string|max:40',
            'device_name' => 'nullable|string|max:40',
            'hide' => 'required|boolean',
            'community_user_id' => 'required|integer',
        ]);
        $user = Auth::user();
        $reader_id = $this->getReaderID();
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) {
            $device_community_id = $this->call_mac->MacIDtoGetCommunityID($request->id);
            if($user->community_id != $device_community_id) {
                log::warning(print_r("Adminユーザーが異常な値でmac_addressをupdateを試みる>>>", 1));
                log::warning(print_r($user, 1));
                return view('errors.403');
            }
        }
        // 端末非表示とした場合 current_stay を false にする
        $stay = 'App\MacAddress'::where('id', $request->id)
            ->pluck('current_stay')->first();
        if ($request->hide == 1) {
            $current_stay = 0;
        } else {
            $current_stay = $stay;
        }
        $now = Carbon::now();
        $param = [
            'vendor' => $request->vendor,
            'device_name' => $request->device_name,
            'hide' => $request->hide,
            'community_user_id' => $request->community_user_id,
            'current_stay' => $current_stay,
            'updated_at' => $now,
        ];
        'App\MacAddress'::where('id', $request->id)->update($param);
        return redirect('/admin_mac_address')->with('message', 'デバイスを編集しました。');
    }

    public function delete(Request $request)
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
        // post id から端末のcommunityのIDを特定
        $device_community_id = $this->call_mac->MacIDtoGetCommunityID($request->id);
        // normalAdmin,readerAdminで自コミュニティ以外は403
        if (
            ( $user->role == 'normalAdmin' ||  $user->role == 'readerAdmin' ) &&
            $device_community_id != $user->community_id
        ) {
            return view('errors.403');
        }
        log::debug(print_r('hoge',1));

        $user = $this->call_user->PersonGet($item->community_user_id);

        return view('admin_mac_address.delete', [
            'item' => $item,
            'user' => $user,
        ]);
    }

    public function remove(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $user = Auth::user();
        $reader_id = $this->getReaderID();
        $owner = DB::table('mac_addresses')
            ->where('id', $request->id)->value('user_id');

        // normal userが自分のID以外を編集しようとした場合は403
        if ($user->role == 'normal') {
            if ($owner != $user->id ) {
                log::warning(print_r("normalユーザーが異常な値でmac_addressをdeleteを試みる>>>", 1));
                log::warning(print_r($user, 1));
                return view('errors.403');
            }
        }
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ( $user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でmac_addressのdeleteを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        'App\MacAddress'::find($request->id)->delete();
        return redirect('/admin_mac_address')->with('message', 'デバイスを削除しました。');
    }
}
