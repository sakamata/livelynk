@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Router新規登録</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="/admin_router/create" method="post">
                        {{ csrf_field() }}
                        @component('components.error')
                        @endcomponent
                        <div class="form-group">
                            <!-- *** ToDo *** 選択フィールドは出さない。コミュニティ画面からルーター登録/追加画面に移動させコミュニティ固定とさせる。 -->
                            <label for="InputTextarea">登録コミュニティ</label>
                            <select name="community_id" class="form-control form-control-lg">
                                @foreach($communities as $community)
                                    <option value="{{$community->id}}">{{$community->id}}&nbsp;:&nbsp;{{$community->name}}&nbsp;&nbsp;:&nbsp;&nbsp;{{$community->service_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="InputTextarea">ルーター（Wi-Fiのネットワーク名や機種名等）</label>
                            <input type="text" class="form-control form-control-lg" name="name" value="{{old('name')}}">
                        </div>
                        <div class="form-group">
                            <label for="InputTextarea">secret</label>
                            <input type="text" class="form-control form-control-lg" name="hash_key" value="{{old('hash_key', $hash)}}">
                            <p>自動生成されたこの乱数をRaspberryPI本体の環境変数 "secret" に適用させます。</p>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                登録
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
