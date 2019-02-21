@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('auth.Login') }}&nbsp;&nbsp;{{ $community->service_name }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf
                        @if($provisional_name)
                        <p>初登録の際はこのままログインできます</p>
                        @endif
                        <input type="hidden" name="community_id" value="{{$community->id}}">
                        <div class="form-group row">
                            <label for="unique_name" class="col-sm-4 col-form-label text-md-right">{{ __('auth.unique_name') }}</label>

                            <div class="col-md-6">
                                <input id="unique_name" type="text" class="form-control{{ $errors->has('unique_name') ? ' is-invalid' : '' }}" name="unique_name" value="{{ $provisional_name ? $provisional_name : old('unique_name') }}" required autofocus>

                                @if ($errors->has('unique_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('unique_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('auth.Password') }}</label>

                            <div class="col-md-6">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ $provisional_name ? $provisional_name : '' }}"required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} checked="checked">

                                    <label class="form-check-label" for="remember">
                                        {{ __('auth.Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('auth.Login') }}
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    <!-- {{ __('auth.Forgot Your Password?') }} -->
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
