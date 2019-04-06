@if($community->calendar_enable && $community->calendar_public_iframe)
  {!! $community->calendar_public_iframe !!}
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
  </ul>
</div>
@endif
