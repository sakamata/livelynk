@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>グローバルIP一覧</h2></div>
                <div class="card-body">
                @can('superAdmin')
                <div class="blockquote text-left">
                    <a href="/admin_global_ip/create" class="btn btn-info" role="button">グローバルIP新規登録</a>
                </div>
                @endcan
                <ul class="pagination justify-content-end mb-3">
                        {{ $items->links() }}
                    </ul>
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>ID</th>
                            <th>community id</th>
                            <th>community名</th>
                            <th>グローバルIP</th>
                            <th>アクセスポイント名</th>
                            <th>created_at</th>
                            <th>updated_at</th>
                            <th>操作</th>
                        </tr>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->community_id}} : {{$item->community->name}}</td>
                            <td>{{$item->community->service_name}}</td>
                            <td>{{$item->global_ip}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{@$item->created_at->format('n月j日 G:i')}}</td>
                            <td>{{@$item->updated_at->format('n月j日 G:i')}}</td>
                            <td>
                                <a href="/admin_global_ip/{{$item->id}}/edit" class="btn btn-info" role="button">編集</a>
                            </td>
                        </tr>
                    @endforeach
                    </table>
                    <ul class="pagination justify-content-center">
                    {{ $items->links() }}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
