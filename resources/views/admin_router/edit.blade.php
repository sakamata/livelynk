@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>ルーター編集</h2></div>
                <div class="card-body">
                    <form action="/admin_router/update" method="post">
                        {{ csrf_field() }}
                        <div>
                            <h3>ROUTER ID&nbsp;:&nbsp;&nbsp;{{$item->id}}</h3>
                        </div>
                        <div>
                            登録日時: {{$item->created_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{$item->updated_at->format('n月j日 G:i:s')}}
                        </div>
                        <hr>
                        <input type="hidden" name="id" value="{{$item->id}}">
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
                                    @if($item->community->id == $community->id)
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
                            <input type="text" class="form-control form-control-lg" name="name" value="{{old('name', $item->name)}}">
                        </div>
                        <div class="form-group">
                            <label for="InputTextarea">secret</label>
                            <input type="text" class="form-control form-control-sm" name="hash_key" value="{{old('hash_key', $item->hash_key)}}">
                            <!-- *** ToDo *** 通常は Readonly 編集ボタンクリック時のみ入力可能とする -->
                            <span id="passwordHelpBlock" class="help-block">注意：値を変更すると対応するRaspberryPIからデータ受信ができなくなります。十分注意してください。</span>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                編集
                            </button>
                        </div>
                    </form>
                    @can('superAdmin')
                    <hr>
                    <h2>RaspberryPi設定項目</h2>
                    <p>コミュニティ名 : {{$item->community->service_name}}</p>
                    <p>コミュニティID : {{$item->community->name}}</p>
                    <hr>
                    <h3>net</h3>
                    <p>wlan wlan0 等 (wi-fiに繋いだlinux端末で ifcongig コマンド実施。段落部分に分かれた wl から始まる内容を入力)</p>
                    <h3>community_id</h3>
                    <p>{{$item->community->name}}</p>
                    <h3>router_id</h3>
                    <p>{{$item->id}}</p>
                    <h3>secret</h3>
                    <p>{{old('hash_key', $item->hash_key)}}</p>
                    <h3>post_url</h3>
                    <p>https://www.livelynk.jp/inport_post/mac_address</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
