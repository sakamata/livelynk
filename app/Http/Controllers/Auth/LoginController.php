<?php

namespace App\Http\Controllers\Auth;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    public function logout()
    {
        $user = Auth::user();
        $community = DB::table('communities')->where('id', $user->community_id)->first();
        $path = '/index/?path=' . $community->url_path;
        Auth::logout();
        return redirect($path)->with('message', 'ログアウトしました。');
    }
}
