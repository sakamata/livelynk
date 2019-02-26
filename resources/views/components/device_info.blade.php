@foreach($item->mac_addresses as $mac_add)
@php
if($mac_add->id){ $mac_add_id = $mac_add->id; }
@endphp
@if($mac_add->hide == true)
<tr class="table-secondary">
    @elseif($mac_add->current_stay == true)
<tr class="table-info">
    @else
<tr>
    @endif
    <td>
        ID:{{$mac_add->id}} &nbsp;&nbsp;
        {{$mac_add->current_stay == 1 ? '滞在' : '不在'}}
    </td>
    <td>{{$mac_add->hide == 1 ? '隠' : ''}}</td>
    <td>{{$mac_add->mac_address_omission}}</td>
    <td>{{$mac_add->device_name}}</td>
    <td>{{$mac_add->vendor}}</td>
    @if($view == 'index')
    <td class="blockquote text-right">
        <a href="/admin_mac_address/delete?id={{$mac_add->id}}" class="btn btn-danger" role="button">削除</a>
    </td>
    @endif
</tr>
@endforeach 