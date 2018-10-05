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
                @component('components.message')
                @endcomponent
                    <form action="/admin_mac_address/remove" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{$item->id}}">

                        <div>
                            <p>&nbsp;</p>
                            <h3>このデバイスを削除しても良いですか？</h3>
                            <p>&nbsp;</p>
                        </div>

                        <div>
                            <h3>登録ユーザー:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->user->name}}</h3>
                        </div>
                        <div>
                            <h3>デバイス名:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->device_name}}</h3>
                        </div>
                        <div>
                            <h3>vendor:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->vendor}}</h3>
                        </div>
                        <div>
                            <h3>MAC Address:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->mac_address}}</h3>
                        </div>

                        <div>
                            ID: {{$item->id}}
                        </div>
                        <div>
                            community ID : {{$item->community_id}}
                        </div>
                        <div>
                            service name : {{$item->community->service_name}}
                        </div>
                        <div>
                            community name : {{$item->community->name}}
                        </div>
                        <div>
                            router ID : {{$item->router_id}}
                        </div>
                        <div>
                            router name : {{$item->router->name}}
                        </div>
                        <div>
                            滞在中: {{$item->current_stay}}
                        </div>
                        <div>
                            非表示: {{$item->hide}}
                        </div>
                        <div>
                            来訪日時: {{$item->arraival_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                        @if($item->departure_at != null)
                            退出日時: {{$item->departure_at->format('n月j日 G:i:s')}}
                        @else
                            退出日時: 滞在中
                        @endif
                        </div>
                        <div>
                            登録日時: {{$item->created_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{$item->updated_at->format('n月j日 G:i:s')}}
                        </div>
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
