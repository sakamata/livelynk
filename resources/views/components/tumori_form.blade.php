@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<ul class="space-list">
    <li>
        <div class="data">
            <div class="availabilities">
            <div class="head">今日のツモリスト<span>{{ $tumolist->count() }} 人</span></div>
                <ul class="body">
                    @foreach ($tumolist as $item)
                    <li class="availability afterBegin">
                        <div class="icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="name">
                            id:{{ $item->id }} 
                            cu_id:{{ $item->community_user_id }} :
                            {{ $item->name }}
                        </div>
                        <div class="arriving">
                            @if($item->maybe_arraival != null)
                            {{ date('G:i', strtotime($item->maybe_arraival)) }}
                            @else
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                            -
                            @if($item->maybe_departure != null)
                            {{ date('G:i', strtotime($item->maybe_departure)) }}
                            @else
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                        </div>
                        @if ($loop->first)
                        <span class="now">Now</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
                <i class="fas fa-chevron-down btn-more"></i>
            </div>
        </div>
        <div class="action">
            <form method="post" action="/tumolink/post">
                {{ csrf_field() }}
                <input type="hidden" name="community_user_id" value="{{ Auth::user()->id }}">
                <div class="plans">
                    <div class="value hour">
                        <select name="hour" class="comp-ui">
                        @for($i = 0; $i < 24; $i++)
                        <option value="{{$i}}">{{$i}}</option>
                        @endfor
                        </select>
                    </div>
                    <div class="unit hour">時間</div>
                    <div class="value minute">
                        <select name="minute" class="comp-ui">
                        <option value="0">0</option>
                        @for($i = 1; $i < 6; $i++)
                        <option value="{{$i}}0">{{$i}}0</option>
                        @endfor
                        </select>
                    </div>
                    <div class="unit minute">分後</div>
                </div>

                <div class="radio-block">
                    <input type="radio" name="direction" value="arriving" checked="checked" id="direction_arriving">
                    <label for="direction_arriving">行く</label>
                    <input type="radio" name="direction" value="leaving" id="direction_leaving">
                    <label for="direction_leaving">帰る</label>
                </div>
                <p class="label-text">GoogleHome通知</p>
                <div class="radio-block">
                    <input type="radio" name="google_home_push" value="1" checked="checked" id="google_home_on">
                    <label for="google_home_on">ON</label>
                    <input type="radio" name="google_home_push" value="0" id="googlehome_off">
                    <label for="googlehome_off">OFF</label>
                </div>
                <button type="submit" name="action" value="tumoli" class="tumoli-button comp-ui">ツモリ</button>
            </form>
        </div>
    </li>
</ul>