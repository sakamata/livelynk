@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                @if($view == 'provisional')
                <div class="card-header"><h2>仮ユーザー一覧</h2></div>
                @else
                <div class="card-header"><h2>ユーザー一覧</h2></div>
                @endif
                <div class="card-body">
                @can('superAdmin')
                @component('components.community_changer', [
                    'communities' => $communities,
                    'community_id' => $community_id,
                ])
                @endcomponent
                @endcan
                @component('components.error')
                @endcomponent
                <div class="blockquote text-left">
                    <a href="/admin_user/add" class="btn btn-info" role="button">新規ユーザー作成</a>
                @if($view == 'provisional')
                <p>仮ユーザーは最終来訪から一か月以上経過すると自動的に削除されます。</p>
                @endif
                </div>
                    <ul class="pagination justify-content-end mb-3">
                        {{-- {{ $items->links() }} --}}
                    </ul>
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>
                                @component('components.order', [
                                    'name' => 'id',
                                    'firld' => 'No',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @if($view == 'index')
                                    @php $firld_title = 'ID/名前/Email'; @endphp
                                @else
                                    @php $firld_title = 'ID'; @endphp
                                @endif
                                @component('components.order', [
                                    'name' => 'name',
                                    'firld' => $firld_title,
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>デバイス情報</th>
                            <th>
                                @component('components.order', [
                                    'name' => 'role',
                                    'firld' => '権限',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            @can('superAdmin')
                            <th>
                                @component('components.order', [
                                    'name' => 'community',
                                    'firld' => 'community',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            @endcan
                            <th>
                                @component('components.order', [
                                    'name' => 's_last_access',
                                    'firld' => '最終来訪',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 's_created_at',
                                    'firld' => '登録日時',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => 'admin_user',
                                ])
                                @endcomponent
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 's_updated_at',
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
                        <tr class="table-secondary">
                        @endif
                            <td>
                                No.{{$item->id}}</br>
                                {{$item->hide == 1 ? '非表示' : ''}}
                            </td>
                            <td>
                                ID:{{$item->unique_name}}</br>
                                @if($view == 'index')
                                {{$item->name}}</br>
                                {{$item->email}}
                                @endif
                            </td>
                            <td>
                            @if($item->mac_addresses != null)
                            @php $mac_add_id = ""; @endphp
                                <table class="table table-hover table-sm table-borderless">
                                    <tbody>
                                    @component('components.device_info', [
                                        'item' => $item,
                                        'view' => $view,
                                        'mac_add_id' => $mac_add_id,
                                        ])
                                    @endcomponent
                                    </tbody>
                                </table>
                                @if($view == 'provisional')
                                @component('components.provisional_owner_update', [
                                    'item' => $item,
                                    'users' => $users,
                                    'community_id' => $community_id,
                                    'mac_add_id' => $mac_add_id,
                                    'reader_id' => $reader_id,
                                    ])
                                @endcomponent
                                @endif
                            @endif
                            </td>
                            <td>{{$item->role}}</td>
                            @can('superAdmin')
                            <td>
                                {{$item->community_id}} : {{$item->community_name}}<br>
                                {{$item->community_service_name}}
                            </td>
                            @endcan
                            <td>
                                @if($item->s_last_access)
                                {{$item->s_last_access->format('n月j日')}}
                                <nobr>
                                    {{$item->s_last_access->format('G:i')}}
                                    {{$item->s_last_access->formatLocalized('(%a)')}}
                                </nobr>
                                @endif
                            </td>
                            <td>
                                @if($item->s_created_at)
                                {{$item->s_created_at->format('n月j日')}}
                                <nobr>
                                    {{$item->s_created_at->format('G:i')}}
                                    {{$item->s_created_at->formatLocalized('(%a)')}}
                                </nobr>
                                @endif
                            </td>
                            <td>
                                @if($item->s_updated_at)
                                {{$item->s_updated_at->format('n月j日')}}
                                <nobr>
                                    {{$item->s_updated_at->format('G:i')}}
                                    {{$item->s_updated_at->formatLocalized('(%a)')}}
                                </nobr>
                                @endif
                            </td>
                            <td>
                                <a href="/admin_user/edit?id={{$item->id}}" class="btn btn-info" role="button">編集</a>
                                <a href="admin_user/delete?id={{$item->id}}" class="btn btn-danger" role="button">退会</a>
                            </td>
                        </tr>
                    @endforeach
                    </table>
                    <ul class="pagination justify-content-center">
                    {{-- {{ $items->links() }} --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
