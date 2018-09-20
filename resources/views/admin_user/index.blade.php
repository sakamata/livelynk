@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
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
                <div class="blockquote text-left">
                    <a href="/admin_user/add" class="btn btn-info" role="button">新規ユーザー作成</a>
                </div>
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>
                                @component('components.order', [
                                    'name' => 'id',
                                    'firld' => 'ID',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'community',
                                    'firld' => 'community_id',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'admin_user',
                                    'firld' => 'role',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'name',
                                    'firld' => '名前 / Email',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'hide',
                                    'firld' => '非表示',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                <table class='table table-borderless table-sm'>
                                    <tr>
                                      <th>ID</th>
                                      <th>滞在中</th>
                                      <th>非表示</th>
                                      <th>デバイス名</th>
                                      <th>vendor</th>
                                    </tr>
                                </table>
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'last_access',
                                    'firld' => '最終来訪',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'created_at',
                                    'firld' => '登録日時',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'updated_at',
                                    'firld' => '更新日時',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>操作</th>
                        </tr>
                    @foreach ($items as $item)
                        @if($item->hide == false)
                        <tr>
                        @else
                        <tr  class="table-secondary">
                        @endif
                            <td>{{$item->id}}</td>
                            <td>{{$item->community_id}}</td>
                            <td>{{$item->role}}</td>
                            <td>{{$item->name}}</br>{{$item->email}}</td>
                            <td>{{$item->hide}}</td>
                            <td>
                                <table class="table table-hover table-sm table-borderless">
                                    <tbody>
                            @if($item->mac_addresses != null)
                                @foreach($item->mac_addresses as $mac_add)
                                    @if($mac_add->hide == true)
                                        <tr class="table-secondary"  onclick="window.location='/admin_mac_address/edit?id={{$mac_add->id}}';">
                                    @elseif($mac_add->current_stay == true && $mac_add->user_id == 1)
                                        <tr class="table-warning"  onclick="window.location='/admin_mac_address/edit?id={{$mac_add->id}}';">
                                    @elseif($mac_add->current_stay == true)
                                        <tr class="table-info"  onclick="window.location='/admin_mac_address/edit?id={{$mac_add->id}}';">
                                    @else
                                        <tr onclick="window.location='/admin_mac_address/edit?id={{$mac_add->id}}';">
                                    @endif
                                            <td>ID:{{$mac_add->id}}</td>
                                            <td>{{$mac_add->current_stay}}</td>
                                            <td>{{$mac_add->hide}}</td>
                                            <td>{{$mac_add->device_name}}</td>
                                            <td>{{$mac_add->vendor}}</td>
                                            <td class="blockquote text-right"><a href="/admin_mac_address/edit?id={{$mac_add->id}}" class="btn btn-info" role="button">編集</a>
                                            </td>
                                        </tr>
                                @endforeach
                            @endif
                                    </tbody>
                                </table>
                            </td>
                            <td>{{$item->last_access->format('n月j日 G:i')}}</td>
                            <td>{{$item->created_at->format('n月j日 G:i')}}</td>
                            <td>{{$item->updated_at->format('n月j日 G:i')}}</td>
                            <td>
                                <a href="/admin_user/edit?id={{$item->id}}" class="btn btn-info" role="button">ユーザー編集</a>
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
