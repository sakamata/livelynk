@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Who's There? @ Geek Office Ebisu</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-hover">
                        <tr class="info thead-light">
                            <th>ステータス</th>
                            <th>名前</th>
                            <th>到着日時</th>
                            <th>帰宅日時</th>
                        </tr>
                    @foreach ($items as $item)
                    @php
                        $no = $item->id % 16;
                        $png = $no . '.png';
                    @endphp
                        <tr class="table-warning">
                            <td><b><span style="display: inline-block;">new</span><span style="display: inline-block;">comer!</span></b></td>
                            <td><img src="{{asset("img/icon/$png")}}" height="50" alt="animal_icon">{{$item->vendor}}</td>
                            <td>
                                {{date('n/j G:i', strtotime($item->arraival_at))}}
                            </td>
                            <td>...</td>
                        </tr>
                    @endforeach
                    @foreach ($items1 as $item)
                        <tr>
                            <td><b>I'm here!</b></td>
                            <td>{{$item->name}}</td>
                            <td>
                                {{date('n/j G:i', strtotime($item->max_arraival_at))}}
                            </td>
                            <td>...</td>
                        </tr>
                    @endforeach
                    @foreach ($items2 as $item)
                        <tr class="table-secondary">
                            <td></td>
                            <td>{{$item->name}}</td>
                            <td>...</td>
                            <td>
                                {{date('n/j G:i', strtotime($item->max_departure_at))}}
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
