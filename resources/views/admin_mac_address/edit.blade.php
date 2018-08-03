@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>MAC Address編集</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="/admin_mac_address/update" method="post">
                        {{ csrf_field() }}
                        <div>
                            ID: {{$item->id}}
                        </div>
                        <div>
                            <h2>MAC Address:&nbsp;&nbsp;&nbsp;&nbsp; {{$item->mac_address}}</h2>
                        </div>
                        <div>
                            滞在中: {{$item->current_stay}}
                        </div>
                        <div>
                            非表示: {{$item->hide}}
                        </div>
                        <div>
                            ルーターID: {{$item->router_id}}
                        </div>
                        <div>
                            来訪日時: {{$item->arraival_at}}
                        </div>
                        <div>
                            退出日時: {{$item->departure_at}}
                        </div>
                        <div>
                            登録日時: {{$item->created_at}}
                        </div>
                        <div>
                            更新日時: {{$item->updated_at}}
                        </div>
                        <div>
                            user ID: {{$item->user->id}}
                        </div>
                        <div>
                            ユーザ名: {{$item->user->name}}
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="InputTextarea">デバイス名</label>
                            <input type="text" class="form-control" name="device_name" value="{{$item->device_name}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">vendor</label>
                            <input type="text" class="form-control" name="vendor" value="{{$item->vendor}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">登録ユーザー</label>
                            <select name="user_id" class="form-control">
                                <option value="">$users->name 1 taro</option>
                                <option value="">$users->name 2 jiro</option>
                                <option value="">$users->name 3 saburo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">表示設定</label>
                            <input type="radio" name="hide" value="0">表示する
                            <input type="radio" name="hide" value="1">表示しない
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                編集
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
