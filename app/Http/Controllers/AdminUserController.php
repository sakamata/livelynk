<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\UserTable;

class AdminUserController extends Controller
{
    // view URL は admin_users_edit としている
    public function index(Request $request)
    {
        $items = 'App\UserTable'::get();
        return view('admin_user.index',[
            'items' => $items,
        ]);
    }

    public function edit(Request $request)
    {
        $item = 'App\UserTable'::where('id', $request->id)->first();
        $mac_addresses = DB::table('mac_addresses')->where('user_id', 1)
            ->orwhere('user_id', $request->id)
            ->orderBy('user_id','desc')->get();

        return view('admin_user.edit', [
            'item' => $item,
            'mac_addresses' => $mac_addresses,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:32',
            'email' => 'required|email|max:256',
            'admin_user' => 'required|boolean',
            'mac_addres_id' => 'array',
        ]);
        $now = Carbon::now();
        // users tableの更新
        $param_user = [
            'name' => $request->name,
            'email' => $request->email,
            'admin_user' => $request->admin_user,
            'updated_at' => $now,
        ];
        'App\UserTable'::where('id', $request->id)->update($param_user);

        // 該当 user_id が所有or解除した mac_addresses idを配列で受け取りDBを更新
        // checkbox から受けたデータ形式 => mac_addres_id['$mac_add->id']

        // チェックの付いたmac_address id を更新
        foreach ((array)$request->mac_addres_id as $mac_id) {
            DB::table('mac_addresses')->where('id', $mac_id)->update([
                'user_id' => $request->id,
                'updated_at' => $now,
            ]);
        }

        // 現状オーナーである mac_addresses id のarrayを抽出
        $before_ids = DB::table('mac_addresses')->where('user_id', $request->id)->pluck('id');
        $before_ids = json_decode(json_encode($before_ids), true);

        // チェックを外したarrayを抽出

        $remove_ids = array_diff($before_ids, (array)$request->mac_addres_id);

        Log::debug(print_r($remove_ids, 1));
        Log::debug(print_r((array)$request->mac_addres_id, 1));

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

}
