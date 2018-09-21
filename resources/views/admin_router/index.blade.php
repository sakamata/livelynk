@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Router一覧</h2></div>
                <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
                <div class="blockquote text-left">
                    <a href="/admin_router/add" class="btn btn-info" role="button">Router新規登録</a>
                </div>
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>ID</th>
                            <th>community id</th>
                            <th>community名</th>
                            <th>router名</th>
                            <th>hash_key</th>
                            <th>created_at</th>
                            <th>updated_at</th>
                            <th>操作</th>
                        </tr>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->community_id}} : {{$item->community->name}}</td>
                            <td>{{$item->community->service_name}}</td>
                            <td>{{$item->name}}</td>
                            <td>**********</td>
                            <td>{{$item->created_at->format('n月j日 G:i')}}</td>
                            <td>{{$item->updated_at->format('n月j日 G:i')}}</td>
                            <td>
                                <a href="/admin_router/edit?id={{$item->id}}" class="btn btn-info" role="button">編集</a>
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
