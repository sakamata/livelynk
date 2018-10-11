@extends('layouts.app')

@section('content')
@component('components.message')
@endcomponent
<h2 class="space-name">{{ $community->service_name }}</h2>
@if(Auth::check())
@if(Auth::user()->role == 'readerAdmin')
    <span>あなたは現在コミュニティ管理者でログイン中です。この画面には表示されません。</span>
@endif
@endif
@component('components.GOE_calendar', ['community' => $community])
@endcomponent
<div class="comp-box-container clearfix">
@php $i = 0; @endphp
@foreach ($items as $item)
  <div class="comp-box">
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->vendor}}</div>
    </div>
    <div class="arrival">
      <span class="time-head">IN</span>
      <span class="time-body">{{date('n/j G:i', strtotime($item->arraival_at))}}</span>
      <!-- 在席の可能性をパーセンテージで表す値 -->
      <span class="time-body">{{ $rate1[$i] }}%</span>
    </div>
    <div class="flag">
      <img src="{{asset("img/icon/newcomer.png")}}" width="46"  alt="Newcomer!">
    </div>
  </div>
  @php $i++; @endphp
@endforeach
@php $i = 0; @endphp
@foreach ($items1 as $item)
  <div class="comp-box">
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->name}}</div>
    </div>
    <div class="arrival">
      <span class="time-head">IN</span>
      <span class="time-body">{{date('n/j G:i', strtotime($item->max_arraival_at))}}</span>
      <!-- 在席の可能性をパーセンテージで表す値 -->
      <span class="time-body">{{ $rate1[$i] }}%</span>
    </div>
    <div class="flag">
      <img src="{{asset("img/icon/im_here.png")}}" width="46"  alt="I'm here!">
    </div>
  </div>
  @php $i++; @endphp
@endforeach
@foreach ($items2 as $item)
  <div class="comp-box absence">
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->name}}</div>
    </div>
    <div class="depature">
      <span class="time-head">OUT</span>
      <span class="time-body">{{date('n/j G:i', strtotime($item->last_access))}}</span>
    </div>
  </div>
@endforeach
</div>
@if(empty($items[0]) && empty($items1[0]) && empty($items2[0]))
<p>端末と紐づけされたユーザーがまだいません</p>
@endif
@endsection
