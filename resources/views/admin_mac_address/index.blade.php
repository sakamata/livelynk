@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>MAC Address一覧</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>id</th>
                            <th>滞在中</th>
                            <th>非表示</th>
                            <th>MAC Address</th>
                            <th>vendor</th>
                            <th>デバイス名</th>
                            <th>登録ユーザー</th>
                            <th>ルーターID</th>
                            <th>来訪日時</th>
                            <th>退出日時</th>
                            <th>登録日時</th>
                            <th>操作</th>
                        </tr>
                    @foreach ($items as $item)
                        @if($item->current_stay == 1)
                        <tr class="table-info">
                        @else
                        <tr>
                        @endif
                            <td>{{$item->id}}</td>
                            <td>{{$item->current_stay}}</td>
                            <td>{{$item->hide}}</td>
                            <td>{{$item->mac_address}}</td>
                            <td>{{$item->vendor}}</td>
                            <td>{{$item->device_name}}</td>
                            <td>{{$item->user->name}}</td>
                            <td>{{$item->router_id}}</td>
                            <td>{{$item->arraival_at->format('n月j日 G:i:s')}}</td>
                        @if($item->departure_at != null)
                            <td>{{$item->departure_at->format('n月j日 G:i:s')}}</td>
                        @else
                            <td></td>
                        @endif
                            <td>{{$item->created_at->format('n月j日 G:i:s')}}</td>
                            <td>
                                <a href="/admin_mac_address/edit?id={{$item->id}}" class="btn btn-info" role="button">編集</a>
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
