@extends('layouts.app')

@section('content')
@component('components.header_menu')
@endcomponent
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h2>グローバルIP新規登録</h2></div>
                <div class="card-body">
                    <form action="/admin_global_ip" method="post">
                        {{ csrf_field() }}
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
                                    @if($user->community_id == $community->id)
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
                            <input type="text" class="form-control form-control-lg" name="name" value="{{old('name')}}">
                            <p>(任意) 例:オフィス ラウンジ 等</p>
                        </div>

                        <div class="form-group">
                            <label for="InputTextarea">グローバルIP</label>
                            <input type="text" class="form-control form-control-lg" name="global_ip" value="{{old('global_ip')}}">
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
