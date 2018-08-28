<form name="{{$name}}_" action="{{$action}}" method="get">
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
    {{$firld}}<a href='javascript:{{$name}}_.submit()'><span class="lead">{{$icon}}</span></a>
</form>
