<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
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
            'admin_user' => ['nullable','regex:/asc|desc/'],
            'name' => ['nullable','regex:/asc|desc/'],
            'hide' => ['nullable','regex:/asc|desc/'],
            'last_access' => ['nullable','regex:/asc|desc/'],
            'created_at' => ['nullable','regex:/asc|desc/'],
            'updated_at' => ['nullable','regex:/asc|desc/'],
        ]);

        // ***ToDo*** もう少しスマートに書けないものか?
        if ($request->id) {
            $order = $request->id;
            $key = 'id';
        }
        elseif ($request->admin_user) {
            $order = $request->admin_user;
            $key = 'admin_user';
        }
        elseif ($request->name) {
            $order = $request->name;
            $key = 'name';
        }
        elseif ($request->hide) {
            $order = $request->hide;
            $key = 'hide';
        }
        elseif ($request->last_access) {
            $order = $request->last_access;
            $key = 'last_access';
        }
        elseif ($request->created_at) {
            $order = $request->created_at;
            $key = 'created_at';
        }
        elseif ($request->updated_at) {
            $order = $request->updated_at;
            $key = 'updated_at';
        } else {
            // default order
            $order = 'desc';
            $key = 'id';
        }

        $items = 'App\UserTable'::orderBy($key, $order)->get();
        return view('admin_user.index',[
            'items' => $items,
            'order' => $order,
            'key' => $key,
        ]);
    }

    public function edit(Request $request)
    {
        $item = 'App\UserTable'::where('id', $request->id)->first();
        $mac_addresses = DB::table('mac_addresses')->where('user_id', 1)
            ->orwhere('user_id', $request->id)
            ->orderBy('hide','asc')
            ->orderBy('user_id', $request->id)
            ->orderBy('arraival_at','desc')
            ->get();

        return view('admin_user.edit', [
            'item' => $item,
            'mac_addresses' => $mac_addresses,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:255',
            'admin_user' => 'required|boolean',
            'hide' => 'required|boolean',
            'mac_addres_id' => 'nullable|array',
        ]);
        $now = Carbon::now();
        // users tableの更新
        $param_user = [
            'name' => $request->name,
            'email' => $request->email,
            'admin_user' => $request->admin_user,
            'hide' => $request->hide,
            'updated_at' => $now,
        ];
        'App\UserTable'::where('id', $request->id)->update($param_user);

        // 該当 user_id が所有or解除した mac_addresses idを配列で受け取りDBを更新
        // checkbox から受けたデータ形式 => mac_addres_id['$mac_add->id']

        // チェックの付いたmac_address id を更新
        foreach ((array)$request->mac_addres_id as $mac_id) {
            // 配列要素が整数であるか確認
            if (ctype_digit(strval($mac_id))) {
                DB::table('mac_addresses')->where('id', $mac_id)->update([
                    'user_id' => $request->id,
                    'updated_at' => $now,
                ]);
            }
        }

        // 現状オーナーである mac_addresses id のarrayを抽出
        $before_ids = DB::table('mac_addresses')->where('user_id', $request->id)->pluck('id');
        $before_ids = json_decode(json_encode($before_ids), true);

        // チェックを外したarrayを抽出
        $remove_ids = array_diff($before_ids, (array)$request->mac_addres_id);

        // チェックを外した id を id=1 の未登録（管理ユーザー）扱いに変更する
        if ($remove_ids) {
            foreach ($remove_ids as $remove_id) {
                DB::table('mac_addresses')->where('id', $remove_id)->update([
                    'user_id' => 1,
                    'updated_at' => $now,
                ]);
            }
        }
        return redirect('/admin_user');
    }

    protected function add(Request $request)
    {
        return view('admin_user.add', [
        ]);
    }

    // app\Http\Controllers\Auth\RegisterController.php をコピーしたのみ
    // ***ToDo*** 処理の一本化（バリデートを2か所に書いてしまっている）
    protected function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);
        return redirect('/admin_user');
    }
}
