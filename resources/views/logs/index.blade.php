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
                    <ul class="pagination justify-content-end mb-3">
                        {{ $items->appends(request()->input())->links() }}
                    </ul>
                    <table class="table table-hover">
                      <tr class="info thead-light">
                          <th class="text-right">ID</th>
                          <th class="text-center">区分</th>
                          <th>MACアドレス</th>
                          <th>名前</th>
                          <th>来訪日時</th>
                          <th>帰宅日時</th>
                      </tr>
                      @foreach ($items as $item)
                      <tr class="table-default">
                        <td class="text-right">
                            {{$item->community_user_id}}
                        </td>
                        <td class="text-center">
                        @if($item->community_user->user->provisional == 1)
                        @php $link ="admin_user_provisional"; @endphp
                            <span class="badge badge-warning">仮</span></td>
                        @else
                        @php $link ="admin_user"; @endphp
                            <span class="badge badge-light">一般</span></td>
                        @endif
                        <td>
                          @if ($item->community_user->user->provisional == 1)
                            @foreach ($item->mac_address as $mac)
                            {{$mac->mac_address_omission}}<br>
                            @endforeach
                          @endif
                        </td>
                        <td>
                            <a href="/{{$link}}#id_{{$item->community_user->id}}" class="text-primary">
                                {{$item->community_user->user->name}}
                            </a>
                        </td>
                        <td>
                            @if($item->arraival_at)
                            {{$item->arraival_at->format('n月d日 H:i ') }}
                            {{$item->arraival_at->formatLocalized('(%a)')}}
                            @endif
                        </td>
                        <td>
                            @if($item->departure_at)
                            {{$item->departure_at->format('n月d日 H:i ') }}
                            {{$item->departure_at->formatLocalized('(%a)')}}
                            @endif
                        </td>
                      </tr>
                      @endforeach
                    </table>
                    <ul class="pagination justify-content-end mb-3">
                        {{ $items->appends(request()->input())->links() }}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
