@extends('layouts.app')

@section('content')
<h2 class="comp-title">プロフィール編集</h2>
<div class="user-edit">
  @if(Auth::user()->provisional == false)
  <div class="comp-information">
    <div class="elem">
      <span class="head">名前</span>
      <span class="body">{{$item->name}}</span>
    </div>
    @if($user_community->google_home_enable == true)
    <div class="elem">
      <span class="head">名前 ふりがな</span>
      <span class="body">{{$item->name_reading}}</span>
    </div>
    @endif
    <div class="elem">
      <span class="head">ユーザーID</span>
      <span class="body">{{$item->unique_name}}</span>
    </div>
    @can('superAdmin')
    <div class="elem">
      <span class="head">コミュニティID</span>
      <span class="body">{{$item->community_id}}</span>
    </div>
    <div class="elem">
      <span class="head">コミュニティコード</span>
      <span class="body">{{$item->community_name}}</span>
    </div>
    <div class="elem">
      <span class="head">コミュニティ名</span>
      <span class="body">{{$item->community_service_name}}</span>
    </div>
    @endcan
    <div class="elem">
      <span class="head">登録日時</span>
      <span class="body">{{$item->s_created_at->format('n月j日 G:i:s')}}</span>
    </div>
    <div class="elem">
      <span class="head">更新日時</span>
      <span class="body">{{$item->s_updated_at->format('n月j日 G:i:s')}}</span>
    </div>
    <div class="elem">
      <span class="head">最終来訪</span>
      <span class="body">{{$item->s_last_access->format('n月j日 G:i:s')}}</span>
    </div>
    @if(Auth::user()->id == $item->id || $taget_role_int <= $user_role_int)
    <a href="/password/edit?id={{$item->id}}" class="comp-ui">パスワード変更</a>
    @endif
    @if($item->role != 'readerAdmin' && $item->role != 'superAdmin')
    <a href="/admin_user/delete?id={{$item->id}}" class="comp-ui">退会</a>
    @endif
  </div>
  @endif
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
      <input type="hidden" name="user_id" value="{{$item->user_id}}">
      @component('components.error')
      @endcomponent
      <div class="form-elem">
        <label for="user_name" class="comp-ui">名前</label>
        <input type="text" class="comp-ui form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{old('name', $item->name)}}" id="user_name">
        @if ($errors->has('name'))
        <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
      </div>
      @if($user_community->google_home_enable == true || Auth::user()->role == 'superAdmin')
      <div class="form-elem">
        <label for="user_reading" class="comp-ui">名前 ふりがな（任意）</label>
        <input type="text" class="comp-ui form-control {{ $errors->has('name_reading') ? ' is-invalid' : '' }}" name="name_reading" value="{{old('name_reading', $item->name_reading)}}" id="user_reading">
        @if ($errors->has('name_reading'))
        <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('name_reading') }}</strong>
        </span>
        @endif
      </div>
      @endif

      <div class="form-elem">
        <label for="unique_name" class="comp-ui">ユーザーID(ログインに必要です)</label>
        <input type="text" class="comp-ui form-control {{ $errors->has('unique_name') ? ' is-invalid' : '' }}" name="unique_name" value="{{old('unique_name', $item->unique_name)}}" id="unique_name" required>
        @if ($errors->has('unique_name'))
        <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('unique_name') }}</strong>
        </span>
        @endif
      </div>

      @if(Auth::user()->provisional == true)
      <div class="form-elem">
        <label for="password" class="comp-ui">{{ __('auth.Password') }}</label>
        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
        @if ($errors->has('password'))
        <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif
      </div>
      <div class="form-elem">
        <label for="password-confirm" class="comp-ui">{{ __('auth.Confirm Password') }}</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
      </div>
      @endif

      @if(Auth::user()->provisional == false)
      <div class="form-elem">
        <label for="email" class="comp-ui">Email(任意)</label>
        <input type="text" class="comp-ui form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{old('email', $item->email)}}" id="email">
        @if ($errors->has('email'))
        <span class="invalid-feedback" role="alert">
          <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
      </div>
      @endif
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
      @if($item->role =='readerAdmin')
      <input type="hidden" name="hide" value="0">
      @else
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
      @endif
      <div class="form-elem admin-box-holder clearfix">
        <label class="comp-ui">デバイス</label>
        @component('components.device_edit', [
          'mac_addresses' => $mac_addresses,
          'item' => $item,
          'view' => $view,
          'errors' => $errors,
        ])
        @endcomponent
      </div>
      <div class="form-elem">
        <button type="submit" class="comp-ui">ユーザー情報を更新</button>
      </div>
    </form>
  </div>
</div>
@endsection
