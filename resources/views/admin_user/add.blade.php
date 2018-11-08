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
                    <form method="GET" action="/admin_user/add" aria-label="{{ __('コミュニティ切替') }}">
                        <div class="form-group row">
                            <label for="community_id" class="col-md-2 col-form-label text-md-right">コミュニティ</label>
                            <div class="col-md-7">
                                <select id="community_id" name="community_id" class="form-control form-control-lg">
                                @foreach($communities as $community)
                                    @if($community->id == $community_id)
                                    <?php $selected = 'selected'; ?>
                                    @else
                                    <?php $selected = ''; ?>
                                    @endif
                                    <option value="{{$community->id}}" {{ $selected }}>{{$community->id}}&nbsp;:&nbsp;{{$community->name}}&nbsp;:&nbsp;{{$community->service_name}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('コミュニティ切替') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr>
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
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('auth.E-Mail Address') }}</label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

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
