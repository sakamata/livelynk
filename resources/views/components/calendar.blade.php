@if($community->calendar_enable && $community->calendar_public_iframe)
<div class="accbox">
  <label for="calendar_public" class="accordion calendar-label">カレンダー</label>
  <input type="checkbox" id="calendar_public" class="cssacc">
  <ul class="space-list">
    <li>
      <div class="data">
        <div class="availabilities">
        {!! $community->calendar_public_iframe !!}
        </div>
      </div>
    </li>
    <li>現在テスト中。登録・確認はギークオフィスWebサービスでお願いします。</li>
  </ul>
</div>
@endif
@if(Auth::check() && $community->calendar_enable && $community->calendar_secret_iframe)
<div class="accbox">
  <label for="calendar_secret" class="accordion calendar-label">カレンダー</label>
  <input type="checkbox" id="calendar_secret" class="cssacc">
  <ul class="space-list">
    <li>
      <div class="data">
        <div class="availabilities">
        {!! $community->calendar_secret_iframe !!}
        </div>
      </div>
    </li>
    <li>現在テスト中。登録・確認はギークオフィスWebサービスでお願いします。</li>
  </ul>
</div>
@endif
