@php
// viewに直接書いているのは後々共通処理化する予定です。今は特別に処理してます。
!env('GOE_EVENT_URL') ? $events="" : $events = file_get_contents(env('GOE_EVENT_URL'));
$esc_tags =array('&lt;br&gt;', '&lt;div&gt;', '&lt;/div&gt;');
$tags =array('<br>', '<div>', '</div>');
$events_escaped = str_replace($esc_tags, $tags, htmlspecialchars( $events ,ENT_QUOTES) );
@endphp

@if($community->id == 1)
<p>Today's Event</p>
    @if( strlen($events_escaped) > 11 )
    <div>{!! $events_escaped !!}</div>
    @else
    <div>本日予定されているイベントはありません</div>
    @endif
<div>&nbsp;</div>
@endif
