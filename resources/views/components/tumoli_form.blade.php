<div class="accbox">
    <label for="label_tomoli" class="accordion tumoli-label">ヨテイ</label>
    <input type="checkbox" id="label_tomoli" class="cssacc">
    <ul class="space-list">
        <li>
            <div class="data">
                <div class="availabilities">
                <div class="head">ヨテイ宣言<span>{{ $tumolist->count() }} 件</span></div>
                @foreach ($willgoUsers as $when => $willgoUsers)
                    @if (count($willgoUsers) > 0)
                    <hr>
                    <span class="when">{{$when}}</span>
                    <ul class="body">
                        @foreach ($willgoUsers as $willgoUser)
                        <li class="availability afterBegin">

                            <div class="icon">
                                <i class="fas fa-user-circle"></i>
                            </div>

                            <div class="name">{{ $willgoUser->name }}</div>
                            <div class="arriving">
                                @if ($willgoUser->from_datetime->hour != 0 ||
                                     $willgoUser->from_datetime->minute != 0
                                )
                                {{ date('G:i', strtotime($willgoUser->from_datetime)) }}
                                &emsp;&emsp;&nbsp;
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                @endforeach
                <i class="fas fa-chevron-down btn-more"></i>
                </div>
            </div>
            <div class="action">
                <form name="plans" method="post" action="/willgo/post">
                    {{ csrf_field() }}
                    <input type="hidden" name="community_user_id" value="{{ Auth::user()->id }}">
                    <div class="plans">
                        <div class="value when">
                            <select name="when" class="comp-ui" id="tumori_when" onchange="timeDisplayChange();">
                            <option value="soon">これから</option>
                            <option value="today">きょう</option>
                            <option value="tomorrow">あした</option>
                            <option value="dayAfterTomorrow">あさって</option>
                            <option value="thisWeek">今週</option>
                            <option value="weekend">土日</option>
                            <option value="nextWeek">来週</option>
                            <option value="thisMonth">今月</option>
                            <option value="nextMonth">来月</option>
                            </select>
                        </div>
                    </div>
                    <div id="timeDisplay">
                        <div class="plans">
                            <div class="value hour">
                                <select name="hour" class="comp-ui">
                                @for($i = 0; $i < 24; $i++)
                                <option value="{{$i}}">{{$i}}</option>
                                @endfor
                                </select>
                            </div>
                            <div class="unit hour">時</div>
                            <div class="value minute">
                                <select name="minute" class="comp-ui">
                                <option value="0">00</option>
                                @for($i = 1; $i < 6; $i++)
                                <option value="{{$i}}0">{{$i}}0</option>
                                @endfor
                                </select>
                            </div>
                            <div class="unit minute">分</div>
                        </div>
                        {{--
                        <div class="radio-block">
                            <input type="radio" name="direction" value="arriving" checked="checked" id="direction_arriving">
                            <label for="direction_arriving">行く</label>
                            <input type="radio" name="direction" value="leaving" id="direction_leaving">
                            <label for="direction_leaving">帰る</label>
                        </div>
                         --}}
                    </div>

                    <button type="submit" name="action" value="willgo" class="tumoli-button comp-ui">行くヨテイ</button>
                    @if($tumoli_declared == true)
                    <button type="submit" name="action" value="cancel" class="tumoli-button bel-button comp-ui">取り消し</button>
                    @endif

                    @if($community->google_home_enable)
                    <p class="label-text">GoogleHome通知</p>
                    <div class="radio-block">
                        <input type="radio" name="google_home_push" value="1" checked="checked" id="google_home_on">
                        <label for="google_home_on">ON</label>
                        <input type="radio" name="google_home_push" value="0" id="googlehome_off">
                        <label for="googlehome_off">OFF</label>
                    </div>
                    @else
                    <input type="hidden" name="google_home_push" value="0">
                    @endif

                </form>
            </div>
            <script src="{{ asset('js/bundle.js') }}"></script>
        </li>
    </ul>
</div>
