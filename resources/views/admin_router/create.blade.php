@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>ルーター新規登録</h2></div>
                <div class="card-body">
                    <form action="/admin_router" method="post">
                        {{ csrf_field() }}
                        @component('components.error')
                        @endcomponent
                        @can('communityAdmin')
                        <input type="hidden" name="community_id" value="{{$user->community_id}}">
                        @endcan
                        @can('superAdmin')
                        <div class="form-group">
                            <label for="InputTextarea">登録コミュニティ</label>
                            <select name="community_id" class="form-control form-control-lg">
                                @foreach($communities as $community)
                                    @if($user->community_id == $community->id)
                                    <?php $selected = 'selected'; ?>
                                    @else
                                    <?php $selected = ''; ?>
                                    @endif
                                    <option value="{{$community->id}}" {{ $selected }}>{{$community->id}}&nbsp;:&nbsp;{{$community->name}}&nbsp;&nbsp;:&nbsp;&nbsp;{{$community->service_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endcan
                        <div class="form-group">
                            <label for="InputTextarea">ルーター（Wi-Fiのネットワーク名や機種名等）</label>
                            <input type="text" class="form-control form-control-lg" name="name" value="{{old('name')}}">
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
