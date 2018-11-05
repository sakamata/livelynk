@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>デバイス削除</h2></div>
                <div class="card-body">
                    <form action="/admin_mac_address/remove" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <div>
                            <p>&nbsp;</p>
                            <h3>このデバイスを削除しても良いですか？</h3>
                            <p>&nbsp;</p>
                        </div>
                        <div>
                            <h3>MAC Address:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->mac_address}}</h3>
                        </div>
                        <div>
                            <h3>メーカー:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->vendor}}</h3>
                        </div>
                        <div>
                            <h3>デバイスメモ:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->device_name}}</h3>
                        </div>

                        <div>
                            <h3>登録ユーザー:&nbsp;&nbsp;&nbsp;&nbsp; {{$person->name}}</h3>
                        </div>
                        <div>
                            <h3>Email:&nbsp;&nbsp;&nbsp;&nbsp; {{$person->email}}</h3>
                        </div>
                        @if($item->current_stay == 1)
                        <div>
                            <h3>滞在中のデバイス</h3>
                        </div>
                        @endif
                        @if($item->hide == 1)
                        <div>
                            <h3>非表示中のデバイス</h3>
                        </div>
                        @endif

                        <div>
                            もっとも最近: {{$item->posted_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{$item->updated_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            登録日時: {{$item->created_at->format('n月j日 G:i:s')}}
                        </div>
                        @can('superAdmin')
                        <div>
                            community_user_id : {{$item->community_user_id}}
                        </div>
                        <div>
                            service name : {{$item->service_name}}
                        </div>
                        <div>
                            community name :
                        </div>
                        @endcan
                        <hr>
                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">
                                削除
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
