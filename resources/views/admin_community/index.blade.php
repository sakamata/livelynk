@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Community一覧</h2></div>
                <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
                <div class="blockquote text-left">
                    <a href="/admin_community/add" class="btn btn-info" role="button">Community新規登録</a>
                </div>
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>ID</th>
                            <th>有効/無効</th>
                            <th>owner user_id</th>
                            <th>community id</th>
                            <th>service name</th>
                            <th>url_hash</th>
                            <th>ifttt_event_name</th>
                            <th>ifttt_webhooks_key</th>
                            <th>created_at</th>
                            <th>updated_at</th>
                            <th>操作</th>
                        </tr>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->enable}}</td>
                            <td>{{$item->user_id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->service_name}}</td>
                            <td>{{$item->url_path}}</td>
                            <td>{{$item->ifttt_event_name}}</td>
                            <td>******</td>
                            <td>{{$item->created_at->format('n月j日 G:i')}}</td>
                            <td>{{$item->updated_at->format('n月j日 G:i')}}</td>
                            <td>
                                <a href="/admin_community/edit?id={{$item->id}}" class="btn btn-info" role="button">編集</a>
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
