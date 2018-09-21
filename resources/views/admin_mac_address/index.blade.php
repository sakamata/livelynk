@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
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
                            <th>
                                @component('components.order', [
                                    'name' => 'id',
                                    'firld' => 'ID',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'community_id',
                                    'firld' => 'com id',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'router_id',
                                    'firld' => 'router',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'current_stay',
                                    'firld' => '滞在中',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>非表示</th>
                            <th>
                                @component('components.order', [
                                    'name' => 'mac_address',
                                    'firld' => 'MAC Address',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'vendor',
                                    'firld' => 'vendor',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>デバイス名</th>
                            <th>登録ユーザー</th>
                            <th>
                                @component('components.order', [
                                    'name' => 'arraival_at',
                                    'firld' => '来訪日時',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'departure_at',
                                    'firld' => '退出日時',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent

                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'posted_at',
                                    'firld' => 'posted_at',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_mac_address',
                                ])
                                @endcomponent
                            </th>
                            <th>登録日時</th>
                            <th>更新日時</th>
                            <th>操作</th>
                        </tr>
                    @foreach ($items as $item)
                        @if($item->hide == true)
                        <tr class="table-secondary">
                        @elseif($item->current_stay == true && $item->user_id == 1)
                        <tr class="table-warning">
                        @elseif($item->current_stay == true)
                        <tr class="table-info">
                        @else
                        <tr>
                        @endif
                            <td>{{$item->id}}</td>
                            <td>{{$item->community_id}}</td>
                            <td>{{$item->router_id}}</td>
                            <td>{{$item->current_stay}}</td>
                            <td>{{$item->hide}}</td>
                            <td>{{$item->mac_address}}</td>
                            <td>{{$item->vendor}}</td>
                            <td>{{$item->device_name}}</td>
                            <td>{{$item->user->name}}</td>
                            <td>{{$item->arraival_at->format('n月j日 G:i')}}</td>
                        @if($item->departure_at != null)
                            <td>{{$item->departure_at->format('n月j日 G:i')}}</td>
                        @else
                            <td></td>
                        @endif
                        @if($item->posted_at != null)
                            <td>{{$item->posted_at->format('n月j日 G:i')}}</td>
                        @else
                            <td></td>
                        @endif
                        @if($item->created_at != null)
                            <td>{{$item->created_at->format('n月j日 G:i')}}</td>
                        @else
                            <td></td>
                        @endif
                        @if($item->updated_at != null)
                            <td>{{$item->updated_at->format('n月j日 G:i')}}</td>
                        @else
                            <td></td>
                        @endif
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
