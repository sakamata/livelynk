<form name="{{$name}}" action="admin_mac_address" method="get">
@if($key != $name || $order == 'desc')
    @php
    $value = 'asc';
    $icon = '▲';
    @endphp
@else
    @php
    $value = 'desc';
    $icon = '▼';
    @endphp
@endif
    <input type='hidden' name='{{$name}}' value='{{$value}}'>
    {{$firld}}<a href='javascript:{{$name}}.submit()'>{{$icon}}</a>
</form>
