@can('superAdmin')
<form method="GET" action="#" aria-label="{{ __('コミュニティ切替') }}">
    <div class="form-group row">
        <label for="community_id" class="col-md-2 col-form-label text-md-right">コミュニティ</label>
        <div class="col-md-7">
            <select id="community_id" name="community_id" class="form-control form-control-lg">
            @foreach($communities as $community)
                @if($community->id == $community_id)
                <?php $selected = 'selected'; ?>
                @else
                <?php $selected = ''; ?>
                @endif
                <option value="{{$community->id}}" {{ $selected }}>{{$community->id}}&nbsp;:&nbsp;{{$community->name}}&nbsp;:&nbsp;{{$community->service_name}}</option>
            @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">
                {{ __('コミュニティ切替') }}
            </button>
        </div>
    </div>
</form>
<hr>
@endcan
