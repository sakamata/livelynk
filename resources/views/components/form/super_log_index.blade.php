<form method="GET" action="#" aria-label="{{ __('コミュニティ切替') }}">
    <div class="form-group row">
        @component('components.form.parts.check_provisional', [
            'provisionalArr' => $provisionalArr,
        ])
        @endcomponent
    </div>
    <div class="form-group row">
        @component('components.form.parts.community_changer', [
            'communities' => $communities,
            'community_id' => $community_id,
        ])
        @endcomponent
        @component('components.form.parts.submit_button', [
            'button_text' => 'コミュニティ切替',
        ])
        @endcomponent
    </div>
</form>
<hr>
