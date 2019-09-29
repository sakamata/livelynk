@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>滞在ログ 一覧</h2>
                </div>
                <div class="card-body">
                    @can('superAdmin')
                    @component('components.form.super_log_index', [
                        'communities' => $communities,
                        'community_id' => $community_id,
                        'provisionalArr' => $provisionalArr,
                    ])
                    @endcomponent
                    @endcan
                    @can('communityAdmin')
                    @component('components.form.commAdmin_log_index', [
                        'communities' => $communities,
                        'community_id' => $community_id,
                        'provisionalArr' => $provisionalArr,
                    ])
                    @endcomponent
                    @endcan
                    @component('components.error')
                    @endcomponent
                    <p>
                        last_log_check : {{$lastTime}}
                    </p>
                    <p>
                      now : {{$now}}
                    </p>
                    <ul class="pagination justify-content-end mb-3">
                        {{ $items->links() }}
                    </ul>
                    <table class="table table-hover table-bordered">
                      <tr class="info thead-light">
                          <th>id</th>
                          <th>community_user_id</th>
                          <th>仮</th>
                          <th>name</th>
                          <th>mac_address</th>
                          <th>arraival_at</th>
                          <th>last_datetime</th>
                          <th>departure_at</th>
                      </tr>
                      @foreach ($items as $item)
                      <tr class="table-default">
                        <td>{{$item->log_id}}</td>
                      <td>{{$item->community_user_id}} :comm_id{{$item->community_id}}</td>
                        <td>{{$item->community_user->user->provisional}}</td>
                        <td>{{$item->community_user->user->name}}</td>
                        <td>
                          @foreach ($item->mac_address as $mac)
                          {{$mac->mac_address_omission}}<br>
                          @endforeach
                        </td>
                        <td>{{$item->arraival_at}}</td>
                        <td>{{$item->last_datetime}}</td>
                        <td>{{$item->departure_at}}</td>
                      </tr>
                      @endforeach
                    </table>
                    <ul class="pagination justify-content-end mb-3">
                        {{ $items->links() }}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection