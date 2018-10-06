@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>退会の確認</h2></div>
                <div class="card-body">
                @component('components.message')
                @endcomponent
                    <form action="/admin_user/remove" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <input type="hidden" name="community_id" value="{{$item->community_id}}">

                        <p>&nbsp;</p>
                        @if($item->id == Auth::user()->id)
                        <p>本当に退会してもよろしいですか？画面下の退会実行ボタンを押すと、登録情報は全て削除されます。</p>
                        @else
                        <p>このユーザーを退会させてもよろしいですか？画面下の退会実行ボタンを押すと、登録情報は全て削除されます。</p>
                        @endif
                        <p>&nbsp;</p>
                        <div>
                            <h3>ID: {{$item->id}}&nbsp;&nbsp;{{$item->name}}</h3>
                        </div>
                        <div>
                            <h3>Email: {{$item->email}}</h3>
                        </div>

                        <div>
                            community ID: {{$item->community_id}}&nbsp;&nbsp;{{$item->community->name}}
                        </div>
                        <div>
                            community name: {{$item->community->service_name}}
                        </div>
                        <div>
                            最終来訪: {{$item->last_access->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            登録日時: {{$item->created_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{$item->updated_at->format('n月j日 G:i:s')}}
                        </div>


                        <hr>
                        <div class="form-group">
                            <label for="InputTextarea">デバイス（退会すると所有するデバイスもすべて削除されます）</label>
                            <table class="table table-hover">
                                <tr class="info thead-light">
                                    <th>ID</th>
                                    <th>滞在中</th>
                                    <th>MAC Address</th>
                                    <th>Vendor</th>
                                    <th>デバイス名</th>
                                    <th>ルーターID</th>
                                    <th>来訪日時</th>
                                    <th>posted_at</th>
                                    <th>登録日時</th>
                                </tr>
                        @foreach($mac_addresses as $mac_add)
                            <input type="hidden" name="mac_addres_id[]" value="{{$mac_add->id}}">
                            @if($mac_add->hide == true)
                                <tr class="table-secondary">
                            @elseif($mac_add->current_stay == true && $mac_add->user_id == 1)
                                <tr class="table-warning">
                            @elseif($mac_add->current_stay == true)
                                <tr class="table-info">
                            @elseif($mac_add->user_id == $item->id)
                                <tr class="table-info">
                            @else
                                <tr>
                            @endif
                                    <td>{{$mac_add->id}}</td>
                                    <td>{{$mac_add->current_stay}}</td>
                                    <td>{{$mac_add->mac_address}}</td>
                                    <td>{{$mac_add->vendor}}</td>
                                    <td>{{$mac_add->device_name}}</td>
                                    <td>{{$mac_add->router_id}}</td>
                                    <td>{{Carbon\Carbon::parse($mac_add->arraival_at)->format('n月j日 G:i')}}</td>
                                    <td>{{Carbon\Carbon::parse($mac_add->posted_at)->format('n月j日 G:i')}}</td>
                                    <td>
                                        {{Carbon\Carbon::parse($mac_add->created_at)->format('n月j日 G:i')}}
                                    </td>
                                </tr>
                        @endforeach
                            </table>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">
                                退会実行
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
