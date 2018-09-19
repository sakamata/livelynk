@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>Community編集</h2></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="/admin_community/update" method="post">
                        {{ csrf_field() }}
                        @component('components.error')
                        @endcomponent
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <p>superAdminのみ表示</p>
                        <div class="form-group">
                            <label for="InputTextarea">有効/無効&nbsp;&nbsp;</label>
                            <!-- カッコ悪いけどひとまず速度重視 -->
                        @if($item->enable == 0)
                            <input type="radio" name="enable" value="1">有効&nbsp;&nbsp;
                            <input type="radio" name="enable" value="0" checked="checked">無効
                        @else
                            <input type="radio" name="enable" value="1" checked="checked">有効&nbsp;&nbsp;
                            <input type="radio" name="enable" value="0">無効
                        @endif
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">コミュニティID（半角英数字とアンダーバー 3～32文字まで）</label>
                            <input type="text" pattern="^\w{3,32}$" class="form-control form-control-lg" name="name" value="{{old('name', $item->name)}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">コミュニティ名称(3～32文字)</label>
                            <input type="text" class="form-control form-control-lg" name="service_name" value="{{old('service_name', $item->service_name)}}">
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">url_path</label>
                            <input type="text" class="form-control form-control-sm" name="url_path" value="{{old('url_path', $item->url_path)}}">
                            <p>注意：編集すると在席確認ページやログインのページのリンク等が変更され再度周知が必要となります。</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">ifttt_event_name</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_event_name" value="{{old('ifttt_event_name', $item->ifttt_event_name)}}">
                            <p>(任意)通知設定の為のIFTTTのEvent Nameを登録します</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">ifttt_webhooks_key</label>
                            <input type="text" class="form-control form-control-lg" name="ifttt_webhooks_key" value="{{old('ifttt_webhooks_key', $item->ifttt_webhooks_key)}}">
                            <p>(任意)通知設定の為のIFTTTのWebhooks keyを入力します</p>
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
