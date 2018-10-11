@extends('layouts.app')

@section('content')
<h2 class="comp-title">プロフィール編集</h2>
@component('components.message')
@endcomponent
<div class="user-edit">
  <div class="comp-information">
    <div class="elem">
      <span class="head">ID</span>
      <span class="body">{{$item->id}}</span>
    </div>
    <div class="elem">
      <span class="head">名前</span>
      <span class="body">{{$item->name}}</span>
    </div>
    <div class="elem">
      <span class="head">コミュニティID</span>
      <span class="body">{{$item->community_id}}</span>
    </div>
    <div class="elem">
      <span class="head">コミュニティコード</span>
      <span class="body">{{$item->community->name}}</span>
    </div>
    <div class="elem">
      <span class="head">コミュニティ名</span>
      <span class="body">{{$item->community->service_name}}</span>
    </div>
    <div class="elem">
      <span class="head">登録日時</span>
      <span class="body">{{$item->created_at->format('n月j日 G:i:s')}}</span>
    </div>
    <div class="elem">
      <span class="head">更新日時</span>
      <span class="body">{{$item->updated_at->format('n月j日 G:i:s')}}</span>
    </div>
    <div class="elem">
      <span class="head">最終来訪</span>
      <span class="body">{{$item->last_access->format('n月j日 G:i:s')}}</span>
    </div>
    @if(Auth::user()->id == $item->id || Auth::user()->role == 'superAdmin')
    <a href="/password/edit?id={{$item->id}}" class="comp-ui">パスワード変更</a>
    @endif
    @if($item->role != 'readerAdmin' && $item->role != 'superAdmin')
    <a href="/admin_user/delete?id={{$item->id}}" class="comp-ui">退会</a>
    @endif
  </div>
  <div class="comp-edit">
    <form action="/admin_user/update" method="post">
      {{ csrf_field() }}
      @cannot('superAdmin')
      <input type="hidden" name="community_id" value="{{$item->community_id}}">
      @endcannot
      @can('superAdmin')
      <!-- 変えたら大変なので、readerAdmin,superAdminの場合はコミュ編集は出さない！ -->
      @if(Auth::user()->role == 'superAdmin')
      <div class="form-elem">
        <label for="community_id" class="comp-ui">コミュニティ</label>
        <select id="community_id" name="community_id" class="comp-ui">
        @foreach($communities as $community)
          @if($item->community_id == $community->id)
          <?php $selected = 'selected'; ?>
          @else
          <?php $selected = ''; ?>
          @endif
          <option value="{{$community->id}}" {{ $selected }}>{{$community->id}}&nbsp;:&nbsp;{{$community->name}}&nbsp;:&nbsp;{{$community->service_name}}</option>
        @endforeach
        </select>
        <p class="caution"><i class="fas fa-exclamation-circle"></i>ユーザーの所属コミュニティを変更する場合は、デバイスのチェックは全て外してください</p>
      </div>
      @endif
      @endcan
      <input type="hidden" name="id" value="{{$item->id}}">
      @component('components.error')
      @endcomponent
      <div class="form-elem">
        <label for="user_name" class="comp-ui">名前</label>
        @php
        if ($item->role == 'readerAdmin') { $readonly = 'readonly';}
        else { $readonly = '';}
        @endphp
        <input type="text" class="comp-ui" name="name" value="{{old('name', $item->name)}}" {{$readonly}} id="user_name">
        @if($item->role == 'readerAdmin')
        <span>コミュニティ管理者は、名前の変更ができません。</span>
        @endif
      </div>
      <div class="form-elem">
        <label for="email" class="comp-ui">Email</label>
        <input type="text" class="comp-ui" name="email" value="{{old('email', $item->email)}}" id="email">
      </div>
      @if(Auth::user()->role != 'normal')
      <div class="form-elem">
        <label for="administrator" class="comp-ui">管理権限</label>
        @if($item->role != 'superAdmin' && $item->role != 'readerAdmin')
        @php
          $disabled = "";
          // 委託管理者が一般ユーザーを閲覧した際は 権限「無し」を無効に
          if (Auth::user()->role == 'normalAdmin') { $disabled ='disabled'; }
          // 委託管理者が一般ユーザーを閲覧した際は 権限「無し」を有効に
          if (Auth::user()->role == 'normalAdmin' && $item->role == 'normal') { $disabled =''; }
        @endphp
        <input type="radio" value="normal" name="role" @if (old('role', $item->role) == "normal") checked @endif {{$disabled}}>無し&nbsp;&nbsp;&nbsp;
        <input type="radio" value="normalAdmin" name="role" @if (old('role', $item->role) == "normalAdmin") checked @endif>委託管理者&nbsp;&nbsp;&nbsp;
        @endif
        @if($item->role == 'readerAdmin')
        <input type="hidden" name="role" value="readerAdmin">
        <p>コミュニティ管理者</p>
        <p class="caution"><i class="fas fa-exclamation-circle"></i>このユーザーは権限の変更ができません。</p>
        @endif
        @if($item->role == 'superAdmin')
        <input type="hidden" name="role" value="superAdmin">
        <p>Livelynk全体管理者</p>
        <p class="caution"><i class="fas fa-exclamation-circle"></i>このユーザーは権限の変更ができません。</p>
        @endif
      </div>
      @endif
      @if(Auth::user()->role == 'normal')
      <input type="hidden" name="role" value="normal">
      @endif
      <div class="form-elem">
        <label for="configuration" class="comp-ui">表示設定</label>
        <div class="form-line">
          <div class="form-block">
            <input id="configuration_show" type="radio" value="0" name="hide" @if (old('hide', $item->hide) == "0") checked @endif>
            <label for="configuration_show">表示</label>
          </div>
          <div class="form-block">
            <input id="configuration_hide" type="radio" value="1" name="hide" @if (old('hide', $item->hide) == "1") checked @endif>
            <label for="configuration_hide">非表示</label>
          </div>
        </div>
      </div>
      <div class="form-elem">
        <button type="submit" class="comp-ui">ユーザー情報を更新</button>
      </div>
    </form>
  </div>
</div>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                @component('components.message')
                @endcomponent
                    <form action="/admin_user/update" method="post">
                        {{ csrf_field() }}

                        

                    

                        <div class="form-group">
                            <label for="InputTextarea">デバイス（所有するデバイスをチェックして登録）</label>
                            <table class="table table-hover">
                                <tr class="info thead-light">
                                    <th>owner</th>
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
                            @if($mac_add->user_id == $item->id)
                                    <td>
                                        <input type="checkbox" name="mac_addres_id[]" value="{{$mac_add->id}}" checked="checked">
                                    </td>
                            @else
                                    <td>
                                        <input type="checkbox" name="mac_addres_id[]" value="{{$mac_add->id}}">
                                    </td>
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
                                        @if($mac_add->user_id == $item->id)
                                        <a href="/admin_mac_address/delete?id={{$item->id}}" class="btn btn-danger" role="button">削除</a>
                                        @endif
                                    </td>
                                </tr>
                        @endforeach
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
