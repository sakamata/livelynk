@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                @if($view == 'regist')
                <div class="card-header"><h2>未登録デバイス一覧</h2></div>
                @else
                <div class="card-header"><h2>デバイス一覧</h2></div>
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
                @if($view == 'regist')
                <div class="blockquote text-left">
                    <a href="/admin_user/add" class="btn btn-info" role="button">新規ユーザー登録</a>
                </div>
                @endif
                    <ul class="pagination justify-content-end mb-3">
                        {{-- {{ $items->links() }} --}}
                    </ul>
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>
                                @component('components.order', [
                                    'name' => 'current_stay',
                                    'firld' => 'ステータス',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => '#',
                                ])
                                @endcomponent
                            </th>
                            @can('superAdmin')
                            <th>
                                @component('components.order', [
                                    'name' => 'community_id',
                                    'firld' => 'community',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => '#',
                                ])
                                @endcomponent
                            </th>
                            @endcan
                            <th>
                                MAC Address
                            </th>
                            <th>
                                非表示　
                                メーカー　
                                デバイスメモ　
                                ユーザー
                            <th>
                                ルーター
                            </th>
                            <th>
                                @component('components.order', [
                                    'name' => 'posted_at',
                                    'firld' => 'もっとも最近',
                                    'key' => $key,
                                    'order' => $order,
                                    'action' => '#',
                                ])
                                @endcomponent
                            </th>
                            <th>来訪日時</th>
                            <th>登録日時</th>
                        </tr>
                    @php
                    $i = 0;
                    @endphp
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
                            <td class="align-middle">
                                ID:{{$item->id}}<br>
                                {{$item->current_stay == 1 ? '滞在中' : '不在'}}<br>
                                {{$item->hide == 1 ? '非表示' : ''}}
                            </td>
                            @can('superAdmin')
                            <td class="align-middle">{{$item->community_id}} : {{$item->community_name}}<br>{{$item->service_name}}</td>
                            @endcan
                            <td class="align-middle">
                                {{$item->mac_address_omission}}
                            </td>
                            <td class="align-middle">
                                <form action="/admin_mac_address/update" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                    <input type="hidden" name="view" value="{{$view}}">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>メーカー</label>
                                            <input type="text" class="form-control" name="vendor" value="{{old('vendor.*', $item->vendor)}}" placeholder="40文字まで">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>デバイスメモ</label>
                                            <input type="text" class="form-control" name="device_name" value="{{old('device_name.*', $item->device_name)}}" placeholder="40文字まで">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-2">
                                            <label>非表示</label><br>&nbsp;&nbsp;
                                            <!-- チェックされていない場合は0を送信 -->
                                            <input type="hidden" name="hide" value="0">
                                            @if($item->hide == true)
                                            <input type="checkbox" name="hide" value="1" checked="checked" id="devise-check-{{$item->id}}">
                                            @else
                                            <input type="checkbox" name="hide" value="1" id="devise-check-{{$item->id}}">
                                            @endif
                                        </div>
                                        <div class="form-group col-md-7">
                                            <!-- 端末id:{{$item->id}}<br> -->
                                            <!-- community_user_id:{{$item->community_user_id}}<br> -->
                                            <label>ユーザー</label>
                                            <select name="community_user_id" class="form-control">
                                                @foreach($users as $user)
                                                    @if($item->community_user_id == $user->id)
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
                                        <div class="form-group col-md-3">
                                            <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                            <div class="btn-group" role="group">
                                                <button name="{{$item->id}}" type="button submit" class="btn btn-primary">編集</button>
                                                <a href="/admin_mac_address/delete?id={{$item->id}}" class="btn btn-danger" role="button">削除</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                            <td class="align-middle">{{$item->router_name}}</td>
                        @if($item->posted_at != null)
                            <td class="align-middle">{{$item->posted_at->format('n月j日 G:i')}}</td>
                        @else
                            <td class="align-middle"></td>
                        @endif
                        @if($item->arraival_at != null)
                            <td class="align-middle">{{$item->arraival_at->format('n月j日 G:i')}}</td>
                        @else
                            <td class="align-middle"></td>
                        @endif
                        @if($item->created_at != null)
                            <td class="align-middle">{{$item->created_at->format('n月j日 G:i')}}</td>
                        @else
                            <td class="align-middle"></td>
                        @endif
                        </tr>
                    @php
                    $i++;
                    @endphp
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
