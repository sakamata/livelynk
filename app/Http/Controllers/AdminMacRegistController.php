<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Service\UserService;
use App\Service\MacAddressService;

// ***ToDo*** Request パラメーターを利用して AdminMacAddressController::index と共通化
// 現状ほとんど一緒のコード
class AdminMacRegistController extends Controller
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
        if ($user->role == 'superAdmin') {
            // ***ToDo*** 表示物と仕様の精査
            // コミュニティをプルダウンで切り替え 等が必要
            // サービス全管理者は全て表示
            $items = $this->call_mac->SuperHavingMac();
            $users = $this->call_user
                ->AllCommunityUsersGet('user_id', 'desc', (int)$user->community_id);
        } else {
            // normalAdmin & readerAdmin はcommunityの範囲で表示
            // 未登録端末のみ呼び出す
            $reader_id = $this->getReaderID();
            $items = $this->call_mac->CommunityHavingMac($user->community_id, $reader_id, $order, $key, $case = 'regist');
            // communityのユーザーlistを取得
            $users = $this->call_user
                ->SelfCommunityUsersGet('user_id', 'desc', (int)$user->community_id);
        }
        return view('admin_mac_address.index', [
            'items' => $items,
            'order' => $order,
            'key' => $key,
            'user' => $user,
            'users' => $users,
            'view' => 'regist',
        ]);
    }
}
