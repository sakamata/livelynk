{{-- 仮ユーザーと端末の関係は仕様上常に1対1となる為以下の方法でforeach で1端末のidを必ず取得できる--}}
@foreach ($item->mac_addresses as $mac_add)
@php
if($mac_add->id){ $mac_add_id = $mac_add->id; }
@endphp
@endforeach
<form action="/admin_user/owner_update" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="community_id" value="{{$community_id}}">
    <input type="hidden" name="mac_id" value="{{$mac_add_id}}">
    <input type="hidden" name="old_community_user_id" value="{{$item->id}}">
    <div class="form-inline">
        <div class="form-group col-md-10">
            <label>ユーザー変更</label>
            <select name="new_community_user_id" class="form-control">
                <option value="{{$item->id}}" selected>{{$item->id}}&nbsp;:&nbsp;{{$item->name}}</option>
                @foreach($users as $user)
                    @if($user->id == $reader_id)
                    <option value="{{$user->id}}">{{$user->id}}&nbsp;:&nbsp;未登録デバイス</option>
                    @else
                    <option value="{{$user->id}}">{{$user->id}}&nbsp;:&nbsp;{{$user->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <button name="{{$user->id}}" type="button submit" class="btn btn-primary">統合</button>
        </div>
    </div>
</form>
