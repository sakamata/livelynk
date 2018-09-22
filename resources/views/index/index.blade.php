@extends('layouts.app')

@section('content')

<h2 class="space-name">ギークオフィス恵比寿</h2>
<div class="comp-box-container clearfix">
@if (session('status'))
  <div class="alert alert-success" role="alert">
    {{ session('status') }}
  </div>
@endif
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
    </div>
    <div class="depature">
      <span class="time-head">OUT</span>
      <span class="time-body">...</span>
    </div>
    <div class="flag">
      <img src="{{asset("img/icon/newcomer.png")}}" width="46"  alt="Newcomer!">
    </div>
  </div>
@endforeach
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
    </div>
    <div class="depature">
      <span class="time-head">OUT</span>
      <span class="time-body">...</span>
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
    <div class="arrival">
      <span class="time-head">IN</span>
      <span class="time-body">...</span>
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
