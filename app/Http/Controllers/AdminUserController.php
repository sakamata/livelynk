<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\UserTable;

// app\Http\Controllers\Auth\RegisterController.php にある use を単純に追加したのみ
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class AdminUserController extends Controller
{
    // app\Http\Controllers\Auth\RegisterController.php にある use を単純に追加したのみ
    use RegistersUsers;

    // view URL は admin_users_edit としている
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
        // normalAdmin,readerAdmin はコミュニティ内のみでソート
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') {
            $items = 'App\UserTable'::UsersGet($key, $order)
                ->MyCommunity($user->community_id)
                ->get();
        }
        // superAdminは全て表示
        if ($user->role == 'superAdmin') {
            $items = 'App\UserTable'::UsersGet($key, $order)
                ->get();
        }

        return view('admin_user.index',[
            'items' => $items,
            'order' => $order,
            'key' => $key,
        ]);
    }

    protected function add(Request $request)
    {
        // superAdminはコミュニティ選択OK,その他は自コミュニティ固定で作成
        // user roleは作成時はnormal固定
        $user = Auth::user();
        $communities = DB::table('communities')->get();
        return view('admin_user.add', [
            'item' => $user,
            'communities' => $communities,
        ]);
    }

    // app\Http\Controllers\Auth\RegisterController.php をコピーしたのみ
    // ***ToDo*** 処理の一本化（バリデートを2か所に書いてしまっている）
    protected function create(Request $request)
    {
        // *****ToDo***** emailの独自バリデート、コミュニティ内でのユニーク確認（registerも同様）
        $request->validate([
            'id' => 'required|integer',
            'community_id' => 'required|integer',
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:170|unique:users',
            'password' => 'required|string|min:6|max:100|confirmed',
        ]);
        $email = $request['email'];
        $login_id = $email . '@' . $request->community_id;
        // user roleは作成時はDBデフォルト値"normal"に固定となる
        User::create([
            'community_id' => $request->community_id,
            'name' => $request['name'],
            'email' => $request['email'],
            'login_id' => $login_id,
            'password' => Hash::make($request['password']),
        ]);
        return redirect('/admin_user')->with('message', '新規ユーザーを作成しました。');
    }

    public function edit(Request $request)
    {
        // 不正なrequestは403
        if (!$request->id || !ctype_digit($request->id)) {
            return view('errors.403');
        }
        $item = 'App\UserTable'::UsersGet('community_user.id', 'asc')->where('community_user.id', $request->id)->first();
        if (!$item) {
            return view('errors.403');
        }

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

        $reader_id = $this->getReaderID();
        $mac_addresses = 'App\MacAddress'::UserHaving($request->id)
            ->MyCommunity($user->community_id)
            ->orderBy('hide','asc')
            ->orderBy('user_id', 'desc')
            ->orderBy('arraival_at','desc')
            ->get();

        $communities = DB::table('communities')->get();
        return view('admin_user.edit', [
            'item' => $item,
            'mac_addresses' => $mac_addresses,
            'communities' => $communities,
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
        foreach ((array)$request->mac_address as $no_use => $mac_id) {
            DB::table('mac_addresses')->where('id', $mac_id)
                ->update([
                    'vendor'      => $mac_id['vendor'],
                    'device_name' => $mac_id['device_name'],
                    'hide'        => $mac_id['hide'],
                    'updated_at'  => $now,
            ]);
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
        $item = 'App\UserTable'::where('id', $request->id)->first();
        if (!$item) {
            return view('errors.403');
        }

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

        $mac_addresses = DB::table('mac_addresses')
            ->where('user_id', $request->id)
            ->orderBy('hide','asc')
            ->orderBy('user_id', $request->id)
            ->orderBy('arraival_at','desc')
            ->get();

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

        $user = DB::table('users')->where('id', $request->id)->first();

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
        if ( $user->role == 'readerAdmin' || $user->role == 'superAdmin' ) {
            log::warning(print_r("Adminユーザーが異常な値でAdmin userのdeleteを試みる>>>", 1));
            log::warning(print_r($user, 1));
            return view('errors.403');
        }

        DB::beginTransaction();
        try {
            'App\UserTable'::find($request->id)->delete();
            $remove_ids =  (array)$request->mac_addres_id;
            // チェックを外した id を readerAdmin 扱いに変更する
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
        }
        if ($success) {
            if (Auth::user()->id == $request->id) {
                Auth::logout();
                return redirect('/')->with('message', '退会が完了しました。ご利用ありがとうございました');
            } else {
                return redirect('/admin_user')->with('message', 'ユーザー ' . $user->name . ' さんをを退会させました');
            }
        }
    }
}
