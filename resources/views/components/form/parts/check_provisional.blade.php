@php
    $provisional = 'checked="checked"';
    $regular     = 'checked="checked"';
@endphp
    @if (isset($provisionalArr['prov']))
        @if ($provisionalArr['prov'] == 1)
            @php $provisional = 'checked="checked"'; @endphp
        @else
            @php $provisional = ""; @endphp
        @endif
    @endif
    @if (isset($provisionalArr['regl']))
        @if ($provisionalArr['regl'] == 1)
            @php $regular = 'checked="checked"'; @endphp
        @else
            @php $regular = ""; @endphp
        @endif
    @endif

    @php
    var_dump('仮'. $provisional);
    var_dump('一般'.$regular);
    @endphp
<label for="community_id" class="col-md-2 col-form-label text-md-right">仮ユーザー</label>
<div class="col-md-7">
    <input type="hidden"   name="provisional[regl]" value="0">
    <input type="checkbox" name="provisional[regl]" value="1" {{ $regular }}>一般ユーザー
    <input type="hidden"   name="provisional[prov]" value="0">
    <input type="checkbox" name="provisional[prov]" value="1" {{ $provisional }}>仮ユーザー
</div>
