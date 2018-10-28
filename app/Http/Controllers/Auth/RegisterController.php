<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo;
    protected $community;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');
        $this->redirectTo = '/';

        $community = $this->GetCommunityFromPath($request->path);
        if (!$community) {
            $this->community = "";
        } else {
            $this->community = $community;
        }
    }

    public function show(Request $request)
    {
        $community = $this->GetCommunityFromPath($request->path);
        if (!$community) {
            return redirect('/')->with('message', '存在しないページです');
        }
        return view('auth.register',[
            'community' => $community,
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'community_id' => 'required|integer',
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:170|unique:users',
            'password' => 'required|string|min:6|max:100|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // ***ToDo*** 処理の一本化（同じを2か所に書いてしまっている）
        // app\Http\Controllers\Auth\RegisterController.php にコピーあり
        DB::beginTransaction();
        try{
            // Laravel のregisterは User::create の返り値 $user を
            // 最後に return で渡せば登録完了となるらしい。
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            // 連携したtableに必要な値をinsertする
            $community_id = $data['community_id'];
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
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }
        if ($success) {
            // session にcommunity値保存
            session([
                'community_id' => $community_id,
                'community_user_id' => $community_user_id
            ]);
            return $user;
        }
    }
}
