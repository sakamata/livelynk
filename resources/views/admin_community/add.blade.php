@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Community新規登録</h2></div>
                <div class="card-body">
                @component('components.message')
                @endcomponent
                    <form action="/admin_community/create" method="post">
                        {{ csrf_field() }}
                        @component('components.error')
                        @endcomponent
                        <div class="form-group">
                            <label for="InputTextarea">コミュニティID（半角英数字とアンダーバー 3～32文字まで）</label>
                            <input type="text" pattern="^\w{3,32}$" class="form-control form-control-lg" name="name_id" value="{{old('name_id')}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">コミュニティ名称(3～32文字)</label>
                            <input type="text" class="form-control form-control-lg" name="service_name" value="{{old('service_name')}}">
                        </div>
                        <h2>管理者ユーザー登録</h2>
                        <!-- この辺のレイアウト統一されてないのすみません。取り急ぎ張り付けただけです… -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="未登録" required autofocus  disabled>
                                <p>管理者ユーザーには未登録の端末が最初に登録されます。この名前は変更しないでください。 ***ToDo*** 任意の名前可能にします</p>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

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
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">url_path</label>
                            <input type="text" class="form-control form-control-lg" name="url_path" value="{{old('url_path', $hash)}}">
                            <p>自動生成された乱数がURLに使用されます</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">ifttt_event_name</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_event_name" value="{{old('ifttt_event_name')}}">
                            <p>(任意)通知設定の為のIFTTTのEvent Nameを登録します</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">ifttt_webhooks_key</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_webhooks_key" value="{{old('ifttt_webhooks_key')}}">
                            <p>(任意)通知設定の為のIFTTTのWebhooks keyを入力します</p>
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
