<form method="GET" action="#" aria-label="{{ __('ユーザー切替') }}">
    <div class="form-group row">
        @component('components.form.parts.check_provisional', [
            'provisionalArr' => $provisionalArr,
        ])
        @endcomponent
        @component('components.form.parts.submit_button', [
            'button_text' => '検索',
        ])
        @endcomponent
    </div>
</form>
<hr>
