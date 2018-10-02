<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Rules\UniqueCommunity;
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
        log::debug(print_r($community, 1));
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
            'email' => ['required', 'string', 'email', 'max:170', new UniqueCommunity($data['community_id'])],
            'password' => 'required|string|min:6|confirmed',
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
        $email = $data['email'];
        $community_id = $data['community_id'];
        $login_id = $email . '@' . $community_id;
        // user roleは作成時はDBデフォルト値"normal"に固定となる
        return User::create([
            'community_id' => $data['community_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'login_id' => $login_id,
            'password' => Hash::make($data['password']),
        ]);
    }
}
