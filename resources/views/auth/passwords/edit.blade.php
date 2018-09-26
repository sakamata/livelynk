@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h2>Password変更</h2></div>
                <div class="card-body">
                @component('components.message')
                @endcomponent
                    <!-- ステータスはdebug用表示 -->
                    <div>
                        <h3>ID: {{$item->id}}&nbsp;&nbsp;{{$item->name}}</h3>
                    </div>
                    <div>
                        community ID: {{$item->community_id}}&nbsp;&nbsp;{{$item->community->name}}
                    </div>
                    <div>
                        community name: {{$item->community->service_name}}
                    </div>
                    <div>
                        role: {{$item->role}}
                    </div>
                    <!-- debug用表示 ここまで -->

                    <form method="POST" action="/password/update" aria-label="{{ __('Register') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <div class="form-group row">
                            <label for="now_password" class="col-md-4 col-form-label text-md-right">{{ __('現在のPassword') }}</label>

                            <div class="col-md-6">
                                <input id="now_password" type="password" class="form-control{{ $errors->has('now_password') ? ' is-invalid' : '' }}" name="now_password" value="{{old('now_password')}}" required>

                                @if ($errors->has('now_password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('now_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('新しい Password') }}</label>

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
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('【確認】新しい Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Password変更') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
