@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>登録ユーザー一覧</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-striped table-hover">
                        <tr class="info">
                            <th>id</th>
                            <th>名前</th>
                            <th>Email</th>
                            <th>管理者</th>
                            <th>最終来訪</th>
                            <th>登録日時</th>
                            <th>更新日時</th>
                        </tr>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->email}}</td>
                            <td>{{$item->admin_user}}</td>
                            <td>{{$item->last_access}}</td>
                            <td>{{$item->created_at}}</td>
                            <td>{{$item->updated_at}}</td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
