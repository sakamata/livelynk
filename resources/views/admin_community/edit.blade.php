@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Community編集</h2></div>
                <div class="card-body">
                    <form action="/admin_community/update" method="post">
                        {{ csrf_field() }}
                        @component('components.error')
                        @endcomponent
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <h2>ID : {{$item->id}} &nbsp;&nbsp; {{$item->service_name}}</h2>
                        <div>
                            登録日時: {{$item->created_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{$item->updated_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            代表管理者 : No. : {{$item->owner->id}}
                        </div>
                        <div>
                            代表管理者 : 名前 : {{$item->owner->name}}
                        </div>
                        <div>
                            代表管理者 : ID : {{$item->owner->unique_name}}
                        </div>
                        <div>
                            代表管理者 : Email : {{$item->owner->email}}
                        </div>
                        <div>
                            <p>ホームページURL</p>
                            <h3>{{ url("/index?path=" . $item->url_path) }}</h3>
                        </div>
                        <hr>
                        @if(Auth::user()->role == 'superAdmin' && Auth::user()->community_id != $item->id)
                        <p>Livelynk全体管理者権限</p>
                        <div class="form-group">
                            <label for="InputTextarea">有効/無効&nbsp;&nbsp;</label>
                            <input type="radio" value="1" name="enable" @if (old('enable', $item->enable) == "1") checked @endif>有効&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="0" name="enable" @if (old('enable', $item->enable) == "0") checked @endif>無効&nbsp;&nbsp;&nbsp;
                        </div>
                        <hr>
                        @else
                        <input type="hidden" name="enable" value="{{$item->enable}}">
                        @endif
                        <div class="form-group">
                            <label for="InputTextarea">コミュニティ名称(3～32文字)</label>
                            <input type="text" class="form-control form-control-lg" name="service_name" value="{{old('service_name', $item->service_name)}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">コミュニティID（半角英数字,アンダーバーのみ 3～32文字まで）</label>
                            <input type="text" pattern="^\w{3,32}$" class="form-control form-control-sm" name="name" value="{{old('name', $item->name)}}">
                            <p>注意：編集すると端末情報が受信できなくなります。変更の際はRaspBerryPiの設定も同様の変更が必要です。</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">url_path</label>
                            <input type="text" class="form-control form-control-sm" name="url_path" value="{{old('url_path', $item->url_path)}}" onInput="checkForm(this)">
                            <p>注意：編集すると在席確認ページやログインのページのリンク等が変更され再度周知が必要となります。</p>
                        </div>

                        @can('superAdmin')
                        <div class="form-group">
                            <label for="InputTextarea">secret</label>
                            <input type="text" class="form-control form-control-sm" name="hash_key" value="{{old('hash_key', $item->hash_key)}}">
                            <span id="passwordHelpBlock" class="help-block">通常編集禁止(superAdminのみ変更可能)</span>
                        </div>
                        @endcan
                        
                        <div class="form-group">
                            <label for="InputTextarea">IFTTT Event Name</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_event_name" value="{{old('ifttt_event_name', $item->ifttt_event_name)}}">
                            <p>(任意)通知設定の為のIFTTTのEvent Nameを登録します</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">IFTTT Webhooks key</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_webhooks_key" value="{{old('ifttt_webhooks_key', $item->ifttt_webhooks_key)}}">
                            <p>(任意)通知設定の為のIFTTTのWebhooks keyを入力します</p>
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
                    <p>コミュニティ名 : {{$item->service_name}}</p>
                    <p>コミュニティID : {{$item->id}}</p>
                    <hr>
                    <h3>community_id</h3>
                    <p>{{$item->name}}</p>
                    <h3>router_id</h3>
                    @forelse ($item->router as $router)
                    <p>ID : <b>{{$router->id}}</b> : {{$router->name}}</p>
                    @empty
                    <P>Routerが登録されていません</P>
                    @endforelse
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
