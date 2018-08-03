@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>登録ユーザー一覧</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-striped table-hover">
                        <tr class="info">
                            <th>id</th>
                            <th>管理者</th>
                            <th>名前&nbsp;/&nbsp;Email</th>
                            <th>デバイス一覧</th>
                            <th>最終来訪</th>
                            <th>登録日時</th>
                            <th>更新日時</th>
                            <th>操作</th>
                        </tr>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->admin_user}}</td>
                            <td>{{$item->name}}</br>
                            {{$item->email}}</td>
                            <td>
                            @if($item->mac_addresses != null)
                                    @foreach($item->mac_addresses as $mac_add)
                                    <div>ID:{{$mac_add->id}}&nbsp;&nbsp;{{$mac_add->device_name}}</div>
                                    @endforeach
                            @endif
                            </td>
                            <td>{{$item->last_access}}</td>
                            <td>{{$item->created_at}}</td>
                            <td>{{$item->updated_at}}</td>
                            <td>
                                <a href="/admin_user/edit?id={{$item->id}}" class="btn btn-info" role="button">編集</a>
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
