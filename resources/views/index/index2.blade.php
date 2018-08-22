@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>index2! Who's There? @ Geek Office Ebisu</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>id</th>
                            <th>名前</th>
                            <th>ステータス / デバイス</th>
                            <th>到着日時</th>
                            <th>帰宅日時</th>
                            <th>更新日時</th>
                        </tr>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>いる / いない :  {{$item->device_name}}</td>
                            <td>
                                {{$item->arraival_at}}
                            </td>
                            <td>
                                {{$item->departure_at}}
                            </td>
                            <td>
                                {{$item->last_access}}
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
