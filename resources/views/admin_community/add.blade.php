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
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="/admin_router/create" method="post">
                        {{ csrf_field() }}
                        @component('components.error')
                        @endcomponent
                        <div class="form-group">
                            <label for="InputTextarea">コミュニティ名称</label>
                            <input type="text" class="form-control form-control-lg" name="name" value="{{old('name')}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">コミュニティID(半角英数字のみ)</label>
                            <input type="text" class="form-control form-control-lg" name="service_name" value="{{old('service_name')}}">
                        </div>

                        <h2>管理者ユーザー登録</h2>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

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
                            <input type="text" class="form-control form-control-lg" name="url_path" value="{{old('url_path')}}">
                            <p>自動生成された乱数がURLに使用されます</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">ittt_event_name</label>
                            <input type="text" class="form-control form-control-lg" name="ittt_event_name" value="{{old('ittt_event_name')}}">
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
