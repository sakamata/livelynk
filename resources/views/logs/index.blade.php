@extends('layouts.app')

@section('content')
<h2>User Stay Log History Test</h2>
<p>
    last_log_check : {{$last_time}}
</p>
<p>
  now : {{$now}}
</p>
<table class="table table-hover table-bordered">
  <tr class="info thead-light">
      <th>id</th>
      <th>community_user_id</th>
      <th>name</th>
      <th>arraival_at</th>
      <th>last_datetime</th>
      <th>departure_at</th>
  </tr>
  @foreach ($res as $item)
  <tr class="table-default">
    <td>{{$item->id}}</td>
    <td>{{$item->community_user_id}}</td>
    <td>{{$item->community_user->user->name}}</td>
    <td>{{$item->arraival_at}}</td>
    <td>{{$item->last_datetime}}</td>
    <td>{{$item->departure_at}}</td>
  </tr>
  @endforeach
</table>
@endsection
