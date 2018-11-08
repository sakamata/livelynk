  @forelse($mac_addresses as $mac_add)
  @if($mac_add->hide == true)
  <!-- 非表示デバイスの際の表示設定 -->
  @elseif($mac_add->current_stay == true)
  <!-- 滞在中デバイスの際の表示設定 -->
  @else
  <!-- その他通常デバイスの際の表示設定 -->
  @endif
  <div class="admin-box">
    <div class="line check">
      <div class="head">
          <label for="devise-check-{{$mac_add->id}}">
            @if($view == 'add')
            チェック
            @endif
          </label>
      </div>
      <div class="body">
        @if($view == 'add')
        <!-- チェックボックスは新規ユーザー作成画面でのみ使用 -->
        <!-- チェックされていない場合は 0 を送信 -->
        <input type="hidden" name="mac_address[{{$mac_add->id}}][check]" value="0">
        <input type="checkbox" name="mac_address[{{$mac_add->id}}][check]" value="1">
        @endif
      </div>
    </div>
    @can('normalAdmin')
    <!-- デバッグ用 -->
    <div class="line">
      <div class="head">ID</div>
      <div class="body">{{$mac_add->id}}</div>
    </div>
    @endcan
    <div class="line">
      <div class="head">滞在中</div>
      <div class="body">{{$mac_add->current_stay}}</div>
    </div>
    <div class="line">
      <div class="head">MAC Address</div>
      <div class="body">{{$mac_add->mac_address}}</div>
    </div>
    <div class="line">
      <div class="head">メーカー（自動）</div>
      <div class="body">
        <input type="text" class="form-control form-control-lg" name="mac_address[{{$mac_add->id}}][vendor]" value="{{old('mac_address.'.$mac_add->id.'.vendor', $mac_add->vendor)}}" placeholder="40文字まで">
      </div>
    </div>
    <div class="line">
      <div class="head">デバイスメモ</div>
      <div class="body">
        <input type="text" class="form-control form-control-lg" name="mac_address[{{$mac_add->id}}][device_name]" value="{{old('mac_address.'.$mac_add->id.'.device_name', $mac_add->device_name)}}" placeholder="40文字まで">
      </div>
    </div>
    <div class="line">
      <div class="head">もっとも最近</div>
      <div class="body">{{Carbon\Carbon::parse($mac_add->posted_at)->format('n月j日 G:i')}}</div>
    </div>
    <div class="line">
      <div class="head">来訪日時</div>
      <div class="body">{{Carbon\Carbon::parse($mac_add->arraival_at)->format('n月j日 G:i')}}</div>
    </div>
    <div class="line">
      <div class="head">登録日時</div>
      <div class="body">{{Carbon\Carbon::parse($mac_add->created_at)->format('n月j日 G:i')}}</div>
    </div>
    <div class="line">
      <div class="head">非表示にする</div>
        <div class="body">
            @php
            
            old('mac_address.'.$mac_add->id.'.hide') ? $check = "checked='checked'" : $check = '';
            @endphp
            {{$check}}
            {{--old('mac_address.'.$mac_add->id.'.hide') ? "checked" : ''--}}
            <!-- チェックされていない場合は0を送信 -->
            <input type="hidden" name="mac_address[{{$mac_add->id}}][hide]" value="0">
            @if($mac_add->hide == true || $check)
            hide true
            <input type="checkbox" name="mac_address[{{$mac_add->id}}][hide]" value="1" checked="checked" id="devise-check-{{$mac_add->id}}">

            @elseif($mac_add->hide == true && !$check)
            <input type="checkbox" name="mac_address[{{$mac_add->id}}][hide]" value="1"  id="devise-check-{{$mac_add->id}}">

            @else
            hide false
            <input type="checkbox" name="mac_address[{{$mac_add->id}}][hide]" value="1" id="devise-check-{{$mac_add->id}}">

            @endif
            {{-- is_array(old('mac_address.'.$mac_add->id.'.hide')) && in_array("1", old('mac_address.'.$mac_add->id.'.hide'), true)? 'checked="checked"' : '' --}}
            {{-- is_array(old("hide")) && in_array("1", old("hide"), true)? 'checked="checked"' : '' --}}

      </div>
    </div>

    <div class="line line-ui">
      <div class="body">
        @if($view == 'edit')
        @if($mac_add->user_id == $item->id)
        <!-- 現状遷移ボタンですが、いずれクリックで削除確認ダイアログ→削除に -->
        <a href="/admin_mac_address/delete?id={{$mac_add->id}}" class="comp-ui danger">削除</a>
        @endif
        @endif
      </div>
    </div>
  </div>
  @empty
  <!-- デバイス無しの状態の際のメッセージ、余白等デザインお願いします。 -->
  <p>保持しているデバイスはありません。</p>
  @endforelse
