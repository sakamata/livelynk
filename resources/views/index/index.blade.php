@extends('layouts.app')

@section('content')
@component('components.message')
@endcomponent
<h2 class="comp-title">{{ $community->service_name }}</h2>
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
  <div class="comp-box clearfix">
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->vendor}}</div>
    </div>
    <div class="arrival">
      <div class="head">IN</div>
      <div class="time">{{date('n/j G:i', strtotime($item->arraival_at))}}</div>
      <div class="accuracy">{{ $rate[$i] }}</div>
    </div>
    <div class="flag sp-none">
      <img src="{{asset("img/icon/newcomer.png")}}" width="46"  alt="Newcomer!">
    </div>
  </div>
  @php $i++; @endphp
@endforeach
@php $i = 0; @endphp
@foreach ($items1 as $item)
  <div class="comp-box clearfix">
    <div class="name">
      <div class="icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="text">{{$item->name}}</div>
    </div>
    <div class="arrival">
      <div class="head">IN</div>
      <div class="time">{{date('n/j G:i', strtotime($item->max_arraival_at))}}</div>
      <div class="accuracy">{{ $rate1[$i] }}</div>
    </div>
    <div class="flag sp-none">
      <img src="{{asset("img/icon/im_here.png")}}" width="46"  alt="I'm here!">
    </div>
  </div>
  @php $i++; @endphp
@endforeach
@foreach ($items2 as $item)
  <div class="comp-box clearfix absence">
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
  </div>
@endforeach
</div>
@if(empty($items[0]) && empty($items1[0]) && empty($items2[0]))
<p>端末と紐づけされたユーザーがまだいません</p>
@endif
@endsection
