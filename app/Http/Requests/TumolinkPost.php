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
            'hour' => 'required|integer|between:0,23',
            'minute' => 'required|integer|in:0,10,20,30,40,50',
            'direction' => 'required|in:arriving,leaving',
            'google_home_push' => 'required|boolean',
        ];
    }
}
