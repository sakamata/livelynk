<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class WillgoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'community_user_id' => ['required', 'integer', 'exists:community_user,id'],
            'when'              => [
                                    'required',
                                    'regex:/^(soon|today|tomorrow|dayAfterTomorrow|thisWeek|weekend|nextWeek|thisMonth|nextMonth)$/'
                                ],
            'hour'              => ['nullable', 'integer', 'between:0,23'],
            'minute'            => ['nullable', 'integer', 'in:0,10,20,30,40,50'],
            'action'            => ['required', 'regex:/^(willgo|go_back|cancel)$/'],
            'cancel_id'         => ['nullable', 'integer', 'exists:willgo,id'],
            'google_home_push'  => ['required', 'boolean'],
            'go_back_minute'    => ['nullable', 'integer', 'in:30,60,120,180'],
            'go_back_add_day'   => ['nullable', 'integer', 'min:0,', 'max:2'],
        ];
    }
}
