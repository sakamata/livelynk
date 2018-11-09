<?php

namespace App\Http\Controllers\Auth;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\ThisCommunityExist;
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
        return 'email';
    }

    public function authenticate(Request $request)
    {
        $credentials  = array(
            'email' => $request->email,
            'password' => $request->password,
        );
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:170', new ThisCommunityExist($request->community_id, $request->email)],
            'password' => 'required|string|min:6|max:100',
        ]);

        if (Auth::attempt($credentials)) {
            // 認証された場合は community_user で必要な値を取得 session に入れる
            $community_user = DB::table('community_user')
                ->select('community_user.id as id')
                ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
                ->where([
                    ['email', $request->email],
                    ['community_id', $request->community_id],
            ])->first();
            // session にcommunity値保存
            $request->session()->put('community_id', $request->community_id);
            $request->session()->put('community_user_id', $community_user->id);
            return redirect('/')->with('message', 'ログインしました');
        } else {
            return redirect()->back()->withErrors(array('email' => 'E-mailかPasswordが正しくありません'))->withInput();
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
