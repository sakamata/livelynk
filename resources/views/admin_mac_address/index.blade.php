@extends('layouts.app')

@section('content')
<h2 class="comp-title">デバイス一覧</h2>
<div class="admin-box-holder clearfix">
  @foreach ($items as $item)
  @if($item->hide == true)
  @elseif($item->current_stay == true && $item->user_id == 1)
  @elseif($item->current_stay == true)
  @else
  @endif
  <div class="admin-box">
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'id',
          'firld' => 'ID',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      <div class="body">{{$item->id}}</div>
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'current_stay',
          'firld' => '滞在中',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      <div class="body">{{$item->current_stay}}</div>
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'community_id',
          'firld' => 'community',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      <div class="body">{{$item->community_id}} : {{$item->community->name}}<br>{{$item->community->service_name}}</div>
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'router_id',
          'firld' => 'router',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      <div class="body">{{$item->router_id}} : {{$item->router->name}}</div>
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'mac_address',
          'firld' => 'MAC Address',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      <div class="body">{{$item->mac_address}}</div>
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'vendor',
          'firld' => 'vendor',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      <div class="body">{{$item->vendor}}</div>
    </div>
    <div class="line">
      <div class="head">デバイス名</div>
      <div class="body">{{$item->device_name}}</div>
    </div>
    <div class="line">
      <div class="head">登録ユーザー</div>
      <div class="body">{{$item->user->name}}</div>
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'arraival_at',
          'firld' => '来訪日時',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      <div class="body">{{$item->arraival_at->format('n月j日 G:i')}}</div>
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'departure_at',
          'firld' => '退出日時',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      @if($item->departure_at != null)
      <div class="body">{{$item->departure_at->format('n月j日 G:i')}}</div>
      @else
      <div class="body"></div>
      @endif
    </div>
    <div class="line">
      <div class="head">
        @component('components.order', [
          'name' => 'posted_at',
          'firld' => 'posted_at',
          'key' => $key,
          'order' => $order,
          'action' => 'admin_mac_address',
        ])
        @endcomponent
      </div>
      @if($item->posted_at != null)
      <div class="body">{{$item->posted_at->format('n月j日 G:i')}}</div>
      @else
      <div class="body"></div>
      @endif
    </div>
    <div class="line">
      <div class="head">登録日時</div>
      @if($item->created_at != null)
      <div class="body">{{$item->created_at->format('n月j日 G:i')}}</div>
      @else
      <div class="body"></div>
      @endif
    </div>
    <div class="line">
      <div class="head">更新日時</div>
      @if($item->updated_at != null)
      <div class="body">{{$item->updated_at->format('n月j日 G:i')}}</div>
      @else
      <div class="body"></div>
      @endif
    </div>
    <div class="line">
      <div class="head">操作</div>
      <div class="body">
        <a href="/admin_mac_address/edit?id={{$item->id}}" class="btn btn-info" role="button">編集</a>
        @if(Auth::user()->role != 'normal')
        <a href="/admin_mac_address/delete?id={{$item->id}}" class="btn btn-danger" role="button">削除</a>
        @endif
        @if(Auth::user()->role == 'normal' && Auth::user()->id == $item->user_id)
        <a href="/admin_mac_address/delete?id={{$item->id}}" class="btn btn-danger" role="button">削除</a>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
