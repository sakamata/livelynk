<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Service\UserService;
use App\Service\MacAddressService;
use App\UserTable; //これを消す位の勢いでリファクタリング

// app\Http\Controllers\Auth\RegisterController.php にある use を単純に追加したのみ
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class AdminUserController extends Controller
{
    // app\Http\Controllers\Auth\RegisterController.php にある use を単純に追加したのみ
    use RegistersUsers;

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

    // view URL は admin_users_edit としている
    // normal user は閲覧できない
    public function index(Request $request)
    {
        $request->validate([
            'id' => ['nullable','regex:/asc|desc/'],
            'role' => ['nullable','regex:/asc|desc/'],
            'name' => ['nullable','regex:/asc|desc/'],
            'hide' => ['nullable','regex:/asc|desc/'],
            's_last_access' => ['nullable','regex:/asc|desc/'],
            's_created_at' => ['nullable','regex:/asc|desc/'],
            's_updated_at' => ['nullable','regex:/asc|desc/'],
        ]);

        // ***ToDo*** もう少しスマートに書けないものか?
        // 何故 $order['column'] 的なキー使うのに気づかなかったのか？
        // そのうち書き換える
        if ($request->id) {
            $order = $request->id;
            $key = 'id';
        }
        elseif ($request->role) {
            $order = $request->role;
            $key = 'role';
        }
        elseif ($request->name) {
            $order = $request->name;
            $key = 'name';
        }
        elseif ($request->hide) {
            $order = $request->hide;
            $key = 'hide';
        }
        elseif ($request->s_last_access) {
            $order = $request->s_last_access;
            $key = 's_last_access';
        }
        elseif ($request->s_created_at) {
            $order = $request->s_created_at;
            $key = 's_created_at';
        }
        elseif ($request->s_updated_at) {
            $order = $request->s_updated_at;
            $key = 's_updated_at';
        } else {
            // default order
            $order = 'desc';
            $key = 'id';
        }

        $user = Auth::user();
        // normalAdmin,readerAdmin は同コミュニティ内のみを抽出
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') {
            $items = $this->call_user->SelfCommunityUsersGet(
                (string)$key,
                (string)$order,
                (int)$user->community_id
            );
        }
        // superAdminは全て表示
        if ($user->role == 'superAdmin') {
            $items = $this->call_user->AllCommunityUsersGet(
                (string)$key,
                (string)$order
            );
        }

        return view('admin_user.index',[
            'items' => $items,
            'order' => $order,
            'key' => $key,
        ]);
    }

    protected function add(Request $request)
    {
        $request->validate([
            'community_id' => 'integer',
        ]);
        $user = Auth::user();
        // superAdminはコミュニティをプルダウン選択
        if ($user->role == 'superAdmin') {
            $community_id = $request->community_id;
            if (!$community_id) { $community_id = 1;}
            $reader_id = $this->getReaderIDParam($community_id);
        }
        // 通常管理者は自コミュニティ固定
        if ($user->role == 'normalAdmin' ||  $user->role == 'readerAdmin') {
            $community_id = $user->community_id;
            $reader_id = $this->getReaderID();
        }

        $mac_addresses = $this->call_mac->PersonHavingGet(
            (int)$reader_id,
            (int)$community_id
        );
        $item = $this->call_user->PersonGet($reader_id);

        // ***ToDo*** user role 非表示のフォーム追加
        $communities = DB::table('communities')->get();
        return view('admin_user.add', [
            'community_id' => $community_id,
            'mac_addresses' => $mac_addresses,
            'item' => $item,
            'communities' => $communities,
            'view' => 'add',
        ]);
    }

    // app\Http\Controllers\Auth\RegisterController.php をコピーしたのみ
    // ***ToDo*** 処理の一本化（同じを2か所に書いてしまっている）
    protected function create(Request $request)
    {
        $request->validate([
            'community_id' => 'required|integer',
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:170|unique:users',
            'password' => 'required|string|min:6|max:100|confirmed',
            'mac_address.*.check' => 'boolean',
            'mac_address.*.hide' => 'boolean',
            'mac_address.*.vendor' => 'nullable|string|max:40',
            'mac_address.*.device_name' => 'nullable|string|max:40',
        ]);
        // user roleは作成時はDBデフォルト値"normal"に固定となる
        DB::beginTransaction();
        try{
            // Laravel のregisterは User::create の返り値 $user を
            // 最後に return で渡せば登録完了となるらしい。
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
            // 連携したtableに必要な値をinsertする
            $community_id = $request['community_id'];
            // 中間tableに値を入れる
            $community_user_id = DB::table('community_user')->insertGetId([
                'community_id' => $community_id,
                'user_id' => $user->id,
            ]);
            $now = Carbon::now();
            // user status管理のtableに値を入れる
            // role_id デフォルト値 "normal" = 1 に固定
            DB::table('communities_users_statuses')->insert([
                'id' => $community_user_id,
                'role_id' => 1,
                'hide' => 0,
                'last_access' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // mac_address 編集項目の変更
            foreach ((array)$request->mac_address as $mac_id => $value) {
                if ($value['check'] == 1) {
                    // 作成された userの community_user_id に変更してupdate
                    $this->call_mac->UpdateChangeOwner(
                        $mac_id,
                        $value['vendor'],
                        $value['device_name'],
                        $value['hide'],
                        $now,
                        $community_user_id
                    );
                } else {
                    // チェック無しでも更新内容を反映する
                    $this->call_mac->Update(
                        $mac_id,
                        $value['vendor'],
                        $value['device_name'],
                        $value['hide'],
                        $now
                    );
                }
            }
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }
        if ($success) {
            return redirect('/admin_user')->with('message', '新規ユーザーを作成しました。');
        }
    }

    // 廃止予定 現在はDB再構築前のcodeのままで403となる
    public function edit(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $item = $this->call_user->PersonGet($request->id);
        if (!$item) { return view('errors.403');}

        $user = Auth::user();
        // normal は自分以外は閲覧不可
        if ($user->role == 'normal' && $user->id != $request->id) {
            return view('errors.403');
        }
        // normalAdmin,readerAdminで自コミュニティ以外は403
        if (
            ( $user->role == 'normalAdmin' ||  $user->role == 'readerAdmin' ) &&
            $item->community_id != $user->community_id
        ) {
            return view('errors.403');
        }
        $mac_addresses = $this->call_mac->PersonHavingGet(
            (int)$request->id,
            (int)$user->community_id
        );
        $communities = DB::table('communities')->get();
        $taget_role = $this->call_user->IDtoRoleGet($request->id);
        $taget_role_int = 'App\Role'::where('role', $taget_role)->pluck('id')->first();
        $user_role_int = 'App\Role'::where('role', $user->role)->pluck('id')->first();

        return view('admin_user.edit', [
            'item' => $item,
            'mac_addresses' => $mac_addresses,
            'communities' => $communities,
            'view' => 'edit',
            'taget_role_int' => $taget_role_int,
            'user_role_int' => $user_role_int,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'community_id' => 'required|integer',
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:170',
            'role' => ['required', 'regex:/normal|normalAdmin|readerAdmin|superAdmin/'],
            'hide' => 'required|boolean',
            'mac_address.*.hide' => 'boolean',
            'mac_address.*.vendor' => 'nullable|string|max:40',
            'mac_address.*.device_name' => 'nullable|string|max:40',
        ]);

        $user = Auth::user();
        // normal userが自分以外のuserを編集しようとした場合は403
        if ($user->role == 'normal' && $user->id != $request->id) {
            log::warning(print_r("normalユーザーが異常な値でusersのupdateを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ( $user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でusersのupdateを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        $now = Carbon::now();
        // users tableの更新
        $param_user = [
            'name' => $request->name,
            'email' => $request->email,
            'updated_at' => $now,
        ];
        'App\UserTable'::where('id', $request->id)->update($param_user);

        // role で取得した文字列を id int に変換
        $role_id = $this->roleNameToIdChange($request->role);
        // user status tableの更新
        $param_status = [
            'role_id' => $role_id,
            'hide' => $request->hide,
            'updated_at' => $now,
        ];
        'App\CommunityUserStatus'::where('id', $request->id)->update($param_status);

        // mac_address 編集項目の変更
        foreach ((array)$request->mac_address as $mac_id => $value) {
            $this->call_mac->Update($mac_id, $value['vendor'], $value['device_name'], $value['hide'], $now);
        }

        if ($user->role == 'normal') {
            return redirect('/admin_user/edit?id='. $user->id)->with('message', 'ユーザープロフィールを編集しました。');
        } else {
            return redirect('/admin_user')->with('message', 'ユーザープロフィールを編集しました。');
        }
    }

    public function delete(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }

        $item = $this->call_user->PersonGet($request->id);
        if (!$item) { return view('errors.403');}

        $user = Auth::user();
        // normal は自分以外は閲覧不可
        if ($user->role == 'normal' && $user->id != $request->id) {
            return view('errors.403');
        }
        // normalAdmin,readerAdminで自コミュニティ以外は403
        if (
            ( $user->role == 'normalAdmin' ||  $user->role == 'readerAdmin' ) &&
            $item->community_id != $user->community_id
        ) {
            return view('errors.403');
        }
        // readerAdmin, superAdmin は削除不可
        if ( $item->role == 'readerAdmin' || $item->role == 'superAdmin' ) {
            return view('errors.403');
        }

        $mac_addresses = $this->call_mac->PersonHavingGet(
            (int)$request->id,
            (int)$user->community_id
        );

        return view('admin_user.delete', [
            'item' => $item,
            'mac_addresses' => $mac_addresses,
        ]);
    }

    public function remove(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $user = Auth::user();
        $taget = $this->call_user->PersonGet($request->id);
        // normal userが自分以外のuserを編集しようとした場合は403
        if ($user->role == 'normal' && $user->id != $request->id) {
            log::warning(print_r("normalユーザーが異常な値でuserのdeleteを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }
        // reader,normal管理者で自分のコミュニティと異なる場合は撥ねる
        if (
            ( $user->role == 'normalAdmin' || $user->role == 'readerAdmin' ) && $request->community_id != $user->community_id
        ) {
            log::warning(print_r("Adminユーザーが異常な値でuserのdeleteを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }
        // readerAdmin, superAdmin は削除不可
        if ( $taget->role == 'readerAdmin' || $taget->role == 'superAdmin' ) {
            log::warning(print_r("Adminユーザーが異常な値でAdmin userのdeleteを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        DB::beginTransaction();
        try {
            // 他のコミュニティに該当ユーザーの登録があるか確認。
            // 他の登録が無ければ、 users table のrecordも削除
            $user_id = DB::table('community_user')
                ->where('id', $request->id)->pluck('user_id')
                ->first();
            $count = DB::table('community_user')
                ->where('user_id', $user_id)->count();
            // 他のコミュニティにアカウントが無い場合はuserTableの該当アカウント削除
            log::debug(print_r('$count>>>'.$count,1));
            if ($count <= 1) {
                log::debug(print_r('user delete start!!!',1));
                DB::table('users')->where('id', $request->id)->delete();
            }
            DB::table('community_user')->where('id', $request->id)->delete();
            DB::table('communities_users_statuses')->where('id', $request->id)->delete();
            $remove_ids =  (array)$request->mac_address_id;
            if ($remove_ids) {
                foreach ($remove_ids as $remove_id) {
                    DB::table('mac_addresses')->where('id', $remove_id)->delete();
                }
            }
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
            return redirect('admin_user/delete?id='. $request->id)->with('message', '処理が行われませんでした。再度お試しください。');
        }
        if ($success) {
            if (Auth::user()->id == $request->id) {
                Auth::logout();
                return redirect('/')->with('message', '退会が完了しました。ご利用ありがとうございました');
            } else {
                return redirect('/admin_user')->with('message', 'ユーザーを退会させました');
            }
        }
    }
}
