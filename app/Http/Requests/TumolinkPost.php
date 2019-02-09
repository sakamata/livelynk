<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TumolinkPost extends FormRequest
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
            'community_user_id' => 'required|integer|exists:community_user,id',
            'maybe_arraival' => 'date_format:Y-m-d H:i:s|after:now',
            'maybe_departure' => 'date_format:Y-m-d H:i:s|after:now',
            'google_home_push' => 'required|boolean',
        ];
    }
}
