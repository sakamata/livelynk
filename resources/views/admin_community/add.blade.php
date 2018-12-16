@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
@component('components.form_js')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Community新規登録</h2></div>
                <div class="card-body">
                    <form action="/admin_community/create" method="post">
                        {{ csrf_field() }}
                        @component('components.error')
                        @endcomponent
                        <div class="form-group">
                            <label for="InputTextarea">コミュニティ名称(3～32文字)</label>
                            <input type="text" class="form-control form-control-lg" name="service_name" value="{{old('service_name')}}">
                        </div>
                        <div class="form-group">
                            <label for="InputTextarea">コミュニティ よみがな（任意）</label>
                            <input type="text" class="form-control form-control-lg" name="service_name_reading" value="{{old('service_name_reading')}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">コミュニティID（半角英数字,アンダーバーのみ 3～32文字まで）</label>
                            <input type="text" pattern="^\w{3,32}$" class="form-control form-control-lg" name="name" value="{{old('name')}}"  style=”ime-mode:disabled;”>
                        </div>

                        <h2>管理者ユーザー登録</h2>
                        <!-- この辺のレイアウト統一されてないのすみません。取り急ぎ張り付けただけです… -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('auth.Name') }}</label>

                            <div class="col-md-6">
                                <input id="user_name" type="text" class="form-control{{ $errors->has('user_name') ? ' is-invalid' : '' }}" name="user_name" value="{{ old('user_name') }}" required  autofocus placeholder="30文字まで">

                                @if ($errors->has('user_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('user_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="unique_name" class="col-md-4 col-form-label text-md-right">{{ __('auth.unique_name') }}</label>

                            <div class="col-md-6">
                                <input id="unique_name" type="text" class="form-control{{ $errors->has('unique_name') ? ' is-invalid' : '' }}" name="unique_name" value="{{ old('unique_name') }}" required  style=”ime-mode:disabled;”>

                                @if ($errors->has('unique_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('unique_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail(いずれオーナーは必須に)') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" style=”ime-mode:disabled;”>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('auth.Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('auth.Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">アクセス用URL</label>
                            <p>{{ url("/index?path=" . old('url_path', $url_path)) }}</p>
                            <input type="text" class="form-control form-control-lg" name="url_path" value="{{old('url_path', $url_path)}}" onInput="checkForm(this)">
                            <p>自動生成された乱数がURLに使用されます</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">secret</label>
                            <input type="text" class="form-control form-control-lg" name="hash_key" value="{{old('hash_key', $secret)}}">
                            <p>自動生成されたこの乱数をRaspberryPI本体の環境変数 "secret" に適用させます。</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">IFTTT Event Name</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_event_name" value="{{old('ifttt_event_name')}}"  onInput="checkForm(this)">
                            <p>(任意)通知設定の為のIFTTTのEvent Nameを登録します</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">IFTTT Webhooks key</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_webhooks_key" value="{{old('ifttt_webhooks_key')}}"  onInput="checkForm(this)">
                            <p>(任意)通知設定の為のIFTTTのWebhooks keyを入力します</p>
                        </div>
                        <hr>
                        <div class="form-elem">
                            <label for="InputTextarea">GoogleHome</label>
                            <input id="google_home_enable_show" type="radio" value="1" name="google_home_enable" @if (old('google_home_enable') == "1") checked @endif>
                            <label for="google_home_enable_show">有効</label>
                            <input id="google_home_enable_hide" type="radio" value="0" name="google_home_enable" @if (old('google_home_enable') == "0") checked @endif>
                            <label for="google_home_enable_hide">無効</label>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">GoogleHome デバイスの名前</label>
                            <input type="text" class="form-control form-control-lg" name="google_home_name" value="{{old('google_home_name')}}">
                            <p>(任意)GoogleHomeのデバイス名を入力します 例:リビング 等</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">GoogleHome MACアドレス</label>
                            <input type="text" class="form-control form-control-lg" name="google_home_mac_address" value="{{old('google_home_mac_address')}}">
                            <p>(任意)GoogleHomeのMACアドレスを入力します</p>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                登録
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
