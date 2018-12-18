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
                @component('components.community_changer', [
                    'communities' => $communities,
                    'community_id' => $community_id,
                ])
                @endcomponent
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
                        <tr  class="table-secondary">
                        @endif
                            <td>No.{{$item->id}}</br>
                            {{$item->hide == 1 ? '非表示' : ''}}</td>
                            <td>ID:{{$item->unique_name}}</br>
                                @if($view == 'index')
                                {{$item->name}}</br>{{$item->email}}</td>
                                @endif
                            <td>
                                <table class="table table-hover table-sm table-borderless">
                                    <tbody>
                                @if($item->mac_addresses != null)
                                @php $mac_add_id = ""; @endphp
                                @foreach($item->mac_addresses as $mac_add)
                                    @php
                                    if($mac_add->id){ $mac_add_id = $mac_add->id; }
                                    @endphp
                                    @if($mac_add->hide == true)
                                        <tr class="table-secondary">
                                    @elseif($mac_add->current_stay == true)
                                        <tr class="table-info">
                                    @else
                                        <tr>
                                    @endif
                                            <td>ID:{{$mac_add->id}} &nbsp;&nbsp;
                                            {{$mac_add->current_stay == 1 ? '滞在' : '不在'}}</td>
                                            <td>{{$mac_add->hide == 1 ? '隠' : ''}}
                                            </td>
                                            <td>{{$mac_add->device_name}}</td>
                                            <td>{{$mac_add->vendor}}</td>
                                            @if($view == 'index')
                                            <td class="blockquote text-right">
                                                <a href="/admin_mac_address/delete?id={{$mac_add->id}}" class="btn btn-danger" role="button">削除</a>
                                            </td>
                                            @endif
                                        </tr>
                                @endforeach
                                    </tbody>
                                </table>
                                @if($view == 'provisional' && $mac_add_id)
                                <form action="/admin_user/owner_update" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="community_id" value="{{$community_id}}">
                                    <input type="hidden" name="mac_id" value="{{$mac_add_id}}">
                                    <input type="hidden" name="old_community_user_id" value="{{$item->id}}">
                                    <div class="form-inline">
                                        <div class="form-group col-md-10">
                                            <label>ユーザー</label>
                                            <select name="new_community_user_id" class="form-control">
                                                @foreach($users as $user)
                                                    @if($item->id == $user->id)
                                                    <?php $selected = 'selected'; ?>
                                                    @else
                                                    <?php $selected = ''; ?>
                                                    @endif
                                                    @if($user->id == $reader_id)
                                                    <option value="{{$user->id}}" {{ $selected }}>{{$user->id}}&nbsp;:&nbsp;未登録デバイス</option>
                                                    @else
                                                    <option value="{{$user->id}}" {{ $selected }}>{{$user->id}}&nbsp;:&nbsp;{{$user->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button name="{{$user->id}}" type="button submit" class="btn btn-primary">編集</button>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            @endif
                            </td>
                            <td>{{$item->role}}</td>
                            @can('superAdmin')
                            <td>{{$item->community_id}} : {{$item->community_name}}<br>{{$item->community_service_name}}</td>
                            @endcan
                            <td>{{$item->s_last_access->format('n月j日 G:i')}}</td>
                            <td>{{$item->s_created_at->format('n月j日 G:i')}}</td>
                            <td>{{$item->s_updated_at->format('n月j日 G:i')}}</td>
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
