@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h2>新規ユーザー登録</h2></div>
                <div class="card-body">
                    @can('superAdmin')
                    @component('components.community_changer', [
                        'communities' => $communities,
                        'community_id' => $community_id,
                    ])
                    @endcomponent
                    @endcan
                    <form method="POST" action="/admin_user/create" aria-label="{{ __('Register') }}">
                        @csrf
                        <input type="hidden" name="community_id" value="{{$community_id}}">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('auth.Name') }}</label>

                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus placeholder="30文字まで">

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('auth.Name') }}ふりがな（任意）</label>

                            <div class="col-md-8">
                                <input id="name_reading" type="text" class="form-control{{ $errors->has('name_reading') ? ' is-invalid' : '' }}" name="name_reading" value="{{ old('name_reading') }}" autofocus placeholder="30文字まで">

                                @if ($errors->has('name_reading'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name_reading') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="unique_name" class="col-md-4 col-form-label text-md-right">{{ __('auth.unique_name') }}</label>

                            <div class="col-md-8">
                                <input id="unique_name" type="text" class="form-control{{ $errors->has('unique_name') ? ' is-invalid' : '' }}" name="unique_name" value="{{ old('unique_name') }}" required>

                                @if ($errors->has('unique_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('unique_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('auth.E-Mail Address') }}</label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('auth.Password') }}</label>

                            <div class="col-md-8">
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

                            <div class="col-md-8">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('ユーザー登録') }}
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="form-elem admin-box-holder clearfix">
                          <label class="comp-ui">未登録デバイス一覧</label>
                          <p>（チェックをすると作成するユーザーと紐づけされます）</p>
                          <hr>
                          @component('components.device_edit', [
                            'mac_addresses' => $mac_addresses,
                            'item' => $item,
                            'view' => $view,
                          ])
                          @endcomponent
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
