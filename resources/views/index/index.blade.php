@extends('layouts.app')

@section('content')

<h2 class="space-name">{{ $community->service_name }}</h2>
@if(Auth::check())
@if(Auth::user()->role == 'readerAdmin')
    <span>あなたは現在コミュニティ管理者でログイン中です。この画面には表示されません。</span>
@endif
@endif
<div class="comp-box-container clearfix">
@php
$judge = env('JUDGE_DEPARTURE_INTERVAL_SECOND');
$now = Carbon\Carbon::now()->timestamp;
$limit = $now - Carbon\Carbon::now()->subSecond($judge)->timestamp;
$i = 0;
@endphp
@foreach ($items as $item)
  @php
  $n[$i] = $now - Carbon\Carbon::parse($item->posted_at)->timestamp;
  $res[$i] = $n[$i]  / $limit;
  $res[$i] = round($res[$i], 2) * 100;
  @endphp
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
      <!-- 帰宅の可能性をパーセンテージで表す値 $res[$i] -->
      <span class="time-body">{{ $res[$i] }}%</span>
    </div>
    <div class="flag">
      <img src="{{asset("img/icon/newcomer.png")}}" width="46"  alt="Newcomer!">
    </div>
  </div>
  @php $i++; @endphp
@endforeach
@foreach ($items1 as $item)
  @php
  $n[$i] = $now - Carbon\Carbon::parse($item->last_access)->timestamp;
  $res[$i] = $n[$i]  / $limit;
  $res[$i] = round($res[$i], 2) * 100;
  @endphp
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
      <!-- 帰宅の可能性をパーセンテージで表す値 $res[$i] -->
      <span class="time-body">{{ $res[$i] }}%</span>
    </div>
    <div class="flag">
      <img src="{{asset("img/icon/im_here.png")}}" width="46"  alt="I'm here!">
    </div>
  </div>
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
