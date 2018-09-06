@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Router編集</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="/admin_router/create" method="post">
                        {{ csrf_field() }}
                        <div>
                            <h3>ID: {{$item->id}}</h3>
                        </div>
                        <div>
                            登録日時: {{$item->created_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{$item->updated_at->format('n月j日 G:i:s')}}
                        </div>
                        <hr>
                        @component('components.error')
                        @endcomponent
                        <div class="form-group">
                            <label for="InputTextarea">所属コミュニティ（未実装）</label>
                            <select name="community_id" class="form-control form-control-lg">
                                    <option value="1">GeekOfficeEbisu</option>
                                    <option value="1">GeekOfficeEbisu</option>
                                    <option value="1">GeekOfficeEbisu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="InputTextarea">ルーター名称</label>
                            <input type="text" class="form-control form-control-lg" name="name" value="{{old('name', $item->name)}}">
                        </div>
                        <div class="form-group">
                            <label for="InputTextarea">hash_key</label>
                            <input type="text" class="form-control form-control-sm" name="hash_key" value="{{old('hash_key', $item->hash_key)}}">
                            <span id="passwordHelpBlock" class="help-block">誤った値にするとデータ受信ができなくなります。十分注意してください。</span>
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
