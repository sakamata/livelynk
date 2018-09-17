@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div>
    <div><h2>Community一覧</h2></div>
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
    <div class="blockquote text-left">
        <a href="/admin_community/add" class="btn btn-info" role="button">Community新規登録</a>
    </div>
        <table>
            <tr>
                <th>ID</th>
                <th>owner user_id</th>
                <th>有効/無効</th>
                <th>community名称</th>
                <th>service name</th>
                <th>hash_key</th>
                <th>ifttt_event_name</th>
                <th>ifttt_key</th>
                <th>created_at</th>
                <th>updated_at</th>
                <th>操作</th>
            </tr>
        @foreach ($items as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->enable}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->service_name}}</td>
                <td>{{$item->ifttt_event_name}}</td>
                <td>{{$item->ifttt_webhooks_key}}</td>
                <td>{{$item->created_at->format('n月j日 G:i')}}</td>
                <td>{{$item->updated_at->format('n月j日 G:i')}}</td>
                <td>
                    <a href="/admin_community/edit?id={{$item->id}}">編集</a>
                </td>
            </tr>
        @endforeach
        </table>
</div>
@endsection
