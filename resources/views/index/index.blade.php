@extends('layouts.app')

@section('content')

@component('components.error')
@endcomponent
<h2 class="comp-title">{{ $community->service_name }}</h2>
@if(Auth::check())
@if(Auth::user()->id == $reader_id)
    <span>あなたは現在コミュニティ管理者でログイン中です。この画面には表示されません。</span>
@endif
@endif
@if(Auth::check() && $community->tumolink_enable)
  @component('components.tumoli_form', [
      'willgoUsers'         => $willgoUsers,
      'willgoCount'         => $willgoCount,
      'willgoPullDownList'  => $willgoPullDownList,
      'community'           => $community
      ])
  @endcomponent
  @if($community->google_home_enable == 1  &&  Auth::user()->provisional == 0)
  @component('components.temporary_taking_form', [
      'community'   => $community,
      'router'      => $router,
  ])
  @endcomponent
  @endif
  @elseif(Auth::check() == false && $community->tumolink_enable)
<p>始めての方は自分の来訪時間に近い仮ユーザーを選択</p>
@elseif(!$community->tumolink_enable)
{{-- <p>新機能ツモリンクをリリースしました</p> --}}
@endif

@component('components.calendar', ['community' => $community])
@endcomponent

<div class="comp-box-container clearfix">
@foreach ($todayWillgoUsers as $item)
  @if ($item->from_datetime == null)
    @continue
  @endif
<div class="comp-box clearfix tumolist">
  @can('normalAdmin')
  <a href="/admin_user/edit?id={{$item->community_user_id}}">
  @endcan
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->name}}</div>
    </div>
    <div class="arrival">
      <div class="head">予定</div>
      @if ($item->from_datetime->hour == 0 && $item->from_datetime->minute == 0)
      <div class="time">きょう</div>
      @else
      <div class="time">{{date('G:i', strtotime($item->from_datetime))}}</div>
      @endif
      <div class="accuracy tumoli_icon">予</div>
    </div>
    @can('normalAdmin')
    </a>
    @endcan
  </div>
@endforeach
@php $i = 0; @endphp
@foreach ($items as $item)
  <div class="comp-box clearfix">
  @can('normalAdmin')
  <a href="/admin_user/edit?id={{$item->id}}">
  @endcan
  @guest
  <a href="/login/?path={{$url_path}}&provisional_name={{$item->unique_name}}">
  @endguest
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->name}}</div>
    </div>
    <div class="arrival">
      <div class="head">IN</div>
      <div class="time">{{date('n/j G:i', strtotime($item->min_arraival_at))}}</div>
      <div class="accuracy">{{ $rate[$i] }}</div>
    </div>
    <div class="flag sp-none">
      <img src="{{asset("img/icon/newcomer.png")}}" width="46"  alt="Newcomer!">
    </div>
    @guest
    </a>
    @endguest
    @can('normalAdmin')
    </a>
    @endcan
  </div>
  @php $i++; @endphp
@endforeach
@php $i = 0; @endphp
@foreach ($items1 as $item)
  <div class="comp-box clearfix">
    @can('normalAdmin')
    <a href="/admin_user/edit?id={{$item->id}}">
    @endcan
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->name}}</div>
    </div>
    <div class="arrival">
      <div class="head">IN</div>
      <div class="time">{{date('n/j G:i', strtotime($item->min_arraival_at))}}</div>
      <div class="accuracy">{{ $rate1[$i] }}</div>
    </div>
    <div class="flag sp-none">
      <img src="{{asset("img/icon/im_here.png")}}" width="46"  alt="I'm here!">
    </div>
    @can('normalAdmin')
    </a>
    @endcan
  </div>
  @php $i++; @endphp
@endforeach
@foreach ($items2 as $item)
  <div class="comp-box clearfix absence">
    @can('normalAdmin')
    <a href="/admin_user/edit?id={{$item->id}}">
    @endcan
    @guest
    @if($item->provisional == 1)
    <a href="/login/?path={{$url_path}}&provisional_name={{$item->unique_name}}">
    @endif
    @endguest
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->name}}</div>
    </div>
    <div class="arrival">
      <div class="head">OUT</div>
      <div class="time">{{date('n/j G:i', strtotime($item->last_access))}}</div>
    </div>
    @guest
    @if($item->provisional == 1)
    </a>
    @endguest
    @endif
    @can('normalAdmin')
    </a>
    @endcan
  </div>
@endforeach
</div>
@if(empty($items[0]) && empty($items1[0]) && empty($items2[0]))
<p>端末と紐づけされたユーザーがまだいません</p>
@endif
@endsection
