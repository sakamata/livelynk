@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Who is There? ユーザー一覧</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-striped table-hover">
                        <tr class="info">
                            <th>id</th>
                            <th>名前</th>
                            <th>ステータス / デバイス</th>
                            <th>最終来訪</th>
                            <th>登録日時</th>
                            <th>更新日時</th>
                        </tr>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>
                            @if($item->mac_addresses != null)
                                    @foreach($item->mac_addresses as $mac_add)
                                        @if($mac_add->current_stay == 1)
                                    <div>滞在中&nbsp;:&nbsp;{{$mac_add->device_name}}</div>
                                        @endif
                                    @endforeach
                            @endif
                            </td>
                            <td>{{$item->last_access}}</td>
                            <td>{{$item->created_at}}</td>
                            <td>{{$item->updated_at}}</td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
