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
                        <p>このコミュニティから退会してもよろしいですか？画面下の『退会する』ボタンを押すと、登録情報が削除されます。</p>
                        @else
                        <p>このユーザーを退会させてもよろしいですか？画面下の『退会する』ボタンを押すと、登録情報が削除されます。</p>
                        @endif
                        <p>&nbsp;</p>
                        <div>
                            <h3>名前: {{$item->name}}</h3>
                        </div>
                        <div>
                            <h3>Email: {{$item->email}}</h3>
                        </div>
                        <div>
                            登録日時: {{$item->s_created_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{$item->s_updated_at->format('n月j日 G:i:s')}}
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="InputTextarea">デバイス（退会するとデバイス情報も削除されます）</label>
                            <table class="table table-hover">
                                <tr class="info thead-light">
                                    <th>デバイス名</th>
                                    <th>Vendor</th>
                                    <th>MAC Address</th>
                                    <th>来訪日時</th>
                                    <th>登録日時</th>
                                </tr>
                        @foreach($mac_addresses as $mac_add)
                            <input type="hidden" name="mac_address_id[]" value="{{$mac_add->id}}">
                                <tr>
                                    <td>{{$mac_add->device_name}}</td>
                                    <td>{{$mac_add->vendor}}</td>
                                    <td>{{$mac_add->mac_address}}</td>
                                    <td>{{Carbon\Carbon::parse($mac_add->arraival_at)->format('n月j日 G:i')}}</td>
                                    <td>{{Carbon\Carbon::parse($mac_add->created_at)->format('n月j日 G:i')}}</td>
                                </tr>
                        @endforeach
                            </table>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">
                                退会する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
