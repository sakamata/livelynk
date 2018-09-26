<?php

namespace App\Http\Controllers;

use DB;
use App\Rules\UniqueCommunity;
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
            'admin_user' => ['nullable','regex:/asc|desc/'],
            'name' => ['nullable','regex:/asc|desc/'],
            'hide' => ['nullable','regex:/asc|desc/'],
            'last_access' => ['nullable','regex:/asc|desc/'],
            'created_at' => ['nullable','regex:/asc|desc/'],
            'updated_at' => ['nullable','regex:/asc|desc/'],
        ]);

        // ***ToDo*** もう少しスマートに書けないものか?
        // 何故 $order['column'] 的なキー使うのに気づかなかったのか？
        // そのうち書き換える
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

        $user = Auth::user();
        // normalAdmin,readerAdmin はコミュニティ内のみでソート
        if ($user->role == 'normalAdmin' || $user->role == 'readerAdmin') {
            $items = 'App\UserTable'::orderBy($key, $order)
                ->MyCommunity($user->community_id)->get();
        }
        // superAdminは全て表示
        if ($user->role == 'superAdmin') {
            $items = 'App\UserTable'::orderBy($key, $order)->get();
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
            'email' => ['required', 'string', 'email', 'max:255', new UniqueCommunity($request->community_id)],
            'password' => 'required|string|min:6|confirmed',
        ]);
        // user roleは作成時はDBデフォルト値"normal"に固定となる
        User::create([
            'community_id' => $request->community_id,
            'name' => $request['name'],
            'email' => $request['email'],
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

        $reader_id = $this->getReaderID();
        $mac_addresses = DB::table('mac_addresses')->where('user_id', $reader_id)
            ->orWhere('user_id', $request->id)
            ->orderBy('hide','asc')
            ->orderBy('user_id', $request->id)
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
            'email' => 'required|string|email|max:255',
            'role' => ['required', 'regex:/normal|normalAdmin|readerAdmin|superAdmin/'],
            'hide' => 'required|boolean',
            'mac_addres_id' => 'nullable|array',
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
            'community_id' => $request->community_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
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
        $reader_id = $this->getReaderID();
        // チェックを外した id を readerAdmin 扱いに変更する
        if ($remove_ids) {
            foreach ($remove_ids as $remove_id) {
                DB::table('mac_addresses')->where('id', $remove_id)->update([
                    'user_id' => $reader_id,
                    'updated_at' => $now,
                ]);
            }
        }
        return redirect('/admin_user')->with('message', 'ユーザープロフィールを編集しました。');
    }
}
