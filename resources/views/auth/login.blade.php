@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                @if($logItems)
                <div class="card">
                    <div class="card-header">
                        <h2>これはあなたですか？</h2>
                    </div>
                    <div class="card-body">
                            <h3>訪問履歴</h3>
                            <p class="mb-3">MACアドレス:&nbsp;{{$logItems[0]->mac_address[0]->mac_address_omission}}</p>
                            <p class="mb-3">端末メーカー:&nbsp;{{$logItems[0]->mac_address[0]->vendor}}</p>
                            <table class="table table-hover">
                            <tr class="info thead-light">
                                <th>来訪日時</th>
                                <th>帰宅日時</th>
                            </tr>
                            @foreach ($logItems as $item)
                            <tr class="table-default">
                            <td>
                                @if($item->arraival_at)
                                {{$item->arraival_at->format('n月d日') }}<br>
                                {{$item->arraival_at->format('H:i') }}
                                {{$item->arraival_at->formatLocalized('(%a)')}}
                                @endif
                            </td>
                            <td>
                                @if($item->departure_at)
                                {{$item->departure_at->format('n月d日') }}<br>
                                {{$item->departure_at->format('H:i') }}
                                {{$item->departure_at->formatLocalized('(%a)')}}
                                @endif
                            </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                @endif
                <div class="card mt-4">
                <div class="card-header"><h2>{{ __('auth.Login') }}</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf
                        @if($provisional_name)
                        <p class="mb-3">あなたならこのままログイン・訪問のお知らせが共有できます</p>
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
