<?php

namespace App\Http\Controllers\Auth;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        if ($request->provisional_name) {
            $request->validate([
                'provisional_name' => ['required', 'string', 'min:6', 'max:40', 'regex:/^[a-zA-Z0-9@_\-.]{6,40}$/u'],
            ]);
            $provisional_name = $request->provisional_name;
        } else {
            $provisional_name = null;
        }

        return view('auth.login',[
            'community' => $community,
            'provisional_name' => $provisional_name,
        ]);
    }

    // ログイン時に使用するユニークであるカラムを指定
    public function username()
    {
        return 'id';
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'unique_name' => ['required', 'string', 'min:6', 'max:40',  'regex:/^[a-zA-Z0-9@_\-.]{6,40}$/u'],
            'password' => 'required|string|min:6|max:100',
        ]);

        //  該当の community_user の id を取得
        $user = DB::table('community_user')
            ->select('community_user.id as community_user_id', 'users.*')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->where([
                ['unique_name', $request->unique_name],
                ['community_id', $request->community_id],
        ])->first();
        $community_user_id = $user->community_user_id;

        if (!$community_user_id) {
            // 他のコミュニティで認証が取れるか？
            $result_bool = $this->CheckOtherCommunityExists($request->unique_name, $request->password);
            if ($result_bool) {
                // 他のコミュニティにいる場合中間table等にレコード追加
                $community_user_id = $this->InsertNewStatuses($request->community_id, $request->unique_name);
            } else {
                // 他のコミュニティにいない場合
                return redirect()->back()->withErrors(array('unique_name' => 'ユーザーIDかPasswordが正しくありません'))->withInput();
            }
        }

        // community_user_id を含めた通常の承認フロー
        $credentials  = array(
            'unique_name' => $request->unique_name,
            'password' => $request->password,
            'id' => $community_user_id,
        );
        // 認証許可
        if (Auth::attempt($credentials)) {
            // session にcommunity値保存
            $request->session()->put('community_id', $request->community_id);
            $request->session()->put('community_user_id', $community_user_id);
            // 仮ユーザーならプロフ編集画面に遷移
            if ($user->provisional == true) {
                return redirect('/admin_user/edit?id='. $community_user_id )->with('message', 'ログインしました。最初にプロフィールとパスワードの編集をお願いします。');
            }
            return redirect('/')->with('message', 'ログインしました');
        } else {
            return redirect()->back()->withErrors(array('unique_name' => 'ユーザーIDかPasswordが正しくありません'))->withInput();
        }
    }

    // return bool 他のコミュニティに存在するかを判定する
    public function CheckOtherCommunityExists($unique_name, $password)
    {
        $hash_password = DB::table('users')->where([
            ['unique_name', $unique_name],
        ])->pluck('password')->first();

        if ($hash_password) {
            if (Hash::check($password, $hash_password)) {
                return true;
            }
        }
        return false;
    }

    // return $community_user_id
    // 既存ユーザーが新たなコミュニティにログインした場合
    // 中間tableと status のrecordを新規作成
    public function InsertNewStatuses($community_id, $unique_name)
    {
        // このコミュに存在しない場合は community_user & statusesに登録
        // community_user_id を取得する
        DB::beginTransaction();
        try{
            $user_id = DB::table('users')
                ->where('unique_name', $unique_name)
                ->pluck('id')->first();
            $community_user_id = DB::table('community_user')->insertGetId([
                'user_id' => $user_id,
                'community_id' => $community_id,
            ]);
            $now = Carbon::now();
            DB::table('communities_users_statuses')->insert([
                'id' => $community_user_id,
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
        return $community_user_id;
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
