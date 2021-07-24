@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>グローバルIP編集</h2></div>
                <div class="card-body">
                    <form action="/admin_global_ip/update" method="post">
                        {{ csrf_field() }}
                        <div>
                            <h3>ID&nbsp;:&nbsp;&nbsp;{{$item->id}}</h3>
                        </div>
                        <div>
                            登録日時: {{@$item->created_at->format('n月j日 G:i:s')}}
                        </div>
                        <div>
                            更新日時: {{@$item->updated_at->format('n月j日 G:i:s')}}
                        </div>
                        <hr>
                        <input type="hidden" name="id" value="{{$item->id}}">
                        @component('components.error')
                        @endcomponent
                        @can('communityAdmin')
                        <input type="hidden" name="community_id" value="{{$user->community_id}}">
                        @endcan
                        @can('superAdmin')
                        <div class="form-group">
                            <label for="InputTextarea">登録コミュニティ</label>
                            <select name="community_id" class="form-control form-control-lg">
                                @foreach($communities as $community)
                                    @if($item->community->id == $community->id)
                                    <?php $selected = 'selected'; ?>
                                    @else
                                    <?php $selected = ''; ?>
                                    @endif
                                    <option value="{{$community->id}}" {{ $selected }}>{{$community->id}}&nbsp;:&nbsp;{{$community->name}}&nbsp;&nbsp;:&nbsp;&nbsp;{{$community->service_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endcan
                        <div class="form-group">
                            <label for="InputTextarea">アクセスポイント名</label>
                            <input type="text" class="form-control form-control-lg" name="name" value="{{old('name', $item->name)}}">
                            <p>(任意) 例:オフィス ラウンジ 等</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">グローバルIP</label>
                            <input type="text" class="form-control form-control-lg" name="global_ip" value="{{old('global_ip', $item->global_ip)}}">
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
