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
            'community_id' => ['nullable', 'integer'],
            'id' => ['nullable','regex:/asc|desc/'],
            'current_stay' => ['nullable','regex:/asc|desc/'],
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
        // ユーザーロールで表示範囲を変える
        if ($user->role == 'superAdmin') {
            // コミュニティをプルダウンで切り替え
            // サービス全管理者はプルダウン切り替えで表示
            $communities = DB::table('communities')->get();
            $community_id = $request->community_id;
            if (!$community_id) { $community_id = 1;}
            $reader_id = DB::table('communities')->where('id', $community_id)
                ->pluck('user_id')->first();
        } else {
            $communities = "";
            $community_id = $user->community_id;
            $reader_id = $this->getReaderID();
        }

        switch ($request->path()) {
            // 登録済み端末のみ呼び出す
            case 'admin_mac_address/index':
                $case = 'index';
                break;
            // 未登録端末のみ呼び出す
            case 'admin_mac_address/regist':
                $case = 'regist';
                break;
            default:
                $case = 'index';
                break;
        }

        // 第5引数 $case を流し込んで表示を切り替え
        $items = $this->call_mac->CommunityHavingMac($community_id, $reader_id, $order, $key, $case);
        // communityのユーザーlistを取得
        $users = $this->call_user
            ->SelfCommunityUsersGet('user_id', 'desc', (int)$community_id, $case=null);

        return view('admin_mac_address.index', [
            'order' => $order,
            'key' => $key,
            'view' => $case,
            'user' => $user,
            'communities' => $communities,
            'community_id' => $community_id,
            'reader_id' => $reader_id,
            'items' => $items,
            'users' => $users,
        ]);
    }

    // 廃止予定 デバイス単体で表示し編集する必要がない為 テスト期間が完了したら削除予定
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
            'view' => ['required','regex:/index|regist/'],
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
        return redirect('/admin_mac_address/'. $request->view)->with('message', 'デバイスを編集しました。');
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
        $person = $this->call_user->PersonGet($item->community_user_id);
        return view('admin_mac_address.delete', [
            'item' => $item,
            'person' => $person,
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
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') {
            $device_community_id = $this->call_mac->MacIDtoGetCommunityID($request->id);
            if($user->community_id != $device_community_id) {
                log::warning(print_r("Adminユーザーが異常な値でmac_addressのdeleteを試みる>>>", 1));
                log::warning(print_r($user, 1));
                return view('errors.403');
            }
        }
        'App\MacAddress'::find($request->id)->delete();
        if ($user->role == 'normal') {
            return redirect('admin_user/edit?id=' . $user->id)->with('message', 'デバイスを削除しました。');
        } else {
            return redirect($request->previous)->with('message', 'デバイスを削除しました。');
        }
    }

    public function DatabaseMACHashChanger()
    {
        $macTable = DB::table('mac_addresses')->get();
        foreach ($macTable as $mac) {
            $mac_address_hash = CahngeCrypt($mac->mac_address);
            DB::table('mac_addresses')->where('id', $mac->id)
                ->update(['mac_address_hash' => $mac_address_hash]);
        }
    }

    public function CahngeCrypt($mac_address)
    {
        return crypt($mac_address, '$2y$10$' . env('CRYPT_SALT') . '$');
    }
}
