<div class="accbox">
    <label for="label_taking" class="accordion tumoli-label">ヒトコト</label>
    <input type="checkbox" id="label_taking" class="cssacc">
    <ul class="space-list">
        <li>
            <div class="data action">
                <form method="post" action="/temporary_taking/post">
                    <div class="availabilities">
                        <div class="head">GoogleHomeでメッセージを送信</div>

                        {{ csrf_field() }}
                        <input type="hidden" name="community_user_id" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="router_id" value="{{ $router->id }}">
                        <input type="text" name="talking_message" class="comp-ui form-control mt-3 mb-3 talking_form {{ $errors->has('talking_message') ? ' is-invalid' : '' }}" value="{{old('talking_message')}}" id="talking_message">
                        <button type="submit" name="action" value="tumoli" class="tumoli-button comp-ui">伝える</button>
                    </div>
                </form>
            </div>
        </li>
    </ul>
</div>
