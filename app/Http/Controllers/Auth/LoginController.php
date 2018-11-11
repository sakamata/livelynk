<?php

namespace App\Http\Controllers\Auth;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\ThisCommunityExist;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $community;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest')->except('logout');
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
        return view('auth.login',[
            'community' => $community,
        ]);
    }

    // ログイン時に使用するユニークであるカラムを指定
    public function username()
    {
        return 'unique_name';
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'unique_name' => ['required', 'string', 'min:6', 'max:40',  'regex:/^[a-zA-Z0-9@_\-.]{6,40}$/u', new ThisCommunityExist($request->community_id, $request->unique_name)],
            'password' => 'required|string|min:6|max:100',
        ]);
        // これを使用すると異なるコミュニティでログインできなくなる。
        // new ThisCommunityExist($request->community_id, $request->unique_name)]

/*
        // ログインしようとしているコミュニティにいるuserか確認する
        $community = DB::table('community_user')->where('community_id', $request->community_id);
        $exists = DB::table('users')
            ->JoinSub($community, 'community_user', function($join) {
                $join->on('users.id', '=', 'community_user.user_id');
            })->where('unique_name', $request->unique_name)->exists();

        log::debug(print_r('exists',1));
        log::debug(print_r($exists,1));

        // 既存ユーザーが新たなコミュニティにログインしようとした場合の処理
        $new_community_user_id = "";
        // このコミュに存在しない場合は community_user に登録 community_user_id を取得
        if (!$exists) {
            DB::beginTransaction();
            try{
                $user_id = DB::table('users')
                    ->where('unique_name', $request->unique_name)
                    ->pluck('id')->first();
                    log::debug(print_r('user_id',1));
                    log::debug(print_r($user_id,1));

                $new_community_user_id = DB::table('community_user')->insertGetId([
                    'user_id' => $user_id,
                    'community_id' => $request->community_id,
                ]);
                log::debug(print_r('new_community_user_id',1));
                log::debug(print_r($new_community_user_id,1));

                $now = Carbon::now();
                DB::table('communities_users_statuses')->insert([
                    'id' => $new_community_user_id,
                    'role_id' => 1,
                    'hide' => 0,
                    'last_access' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->withErrors(array('unique_name' => '認証に失敗しました。もう一度試してみてください'))->withInput();
            }
        }
*/
        // ↓コメントアウトにもある定義、一時的にここに書く
        $new_community_user_id = "";
        // 認証された場合は community_user で必要な値を取得 session に入れる
        $community_user = DB::table('community_user')
            ->select('community_user.id as id')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->where([
                ['unique_name', $request->unique_name],
                ['community_id', $request->community_id],
        ])->first();

        // sessionに既存のIDを使うか、今登録したIDを使うか判定
        if (!$new_community_user_id) {
            $community_user_id = $community_user->id;
        } else {
            $community_user_id = $new_community_user_id;
        }

        $credentials  = array(
            'unique_name' => $request->unique_name,
            'password' => $request->password,
        );
        // 'community_user.id' => $community_user_id,

        // 認証許可
        if (Auth::attempt($credentials)) {
            // session にcommunity値保存
            $request->session()->put('community_id', $request->community_id);
            $request->session()->put('community_user_id', $community_user_id);
            return redirect('/')->with('message', 'ログインしました');
        } else {
            return redirect()->back()->withErrors(array('unique_name' => 'ユーザーIDかPasswordが正しくありません'))->withInput();
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $community_id = session('community_id');
        $community = DB::table('communities')
            ->where('id', $community_id)->first();
        $path = '/index/?path=' . $community->url_path;
        // session のcommunity値削除
        $request->session()->forget('community_id');
        $request->session()->forget('community_user_id');
        Auth::logout();
        return redirect($path)->with('message', 'ログアウトしました。');
    }
}
