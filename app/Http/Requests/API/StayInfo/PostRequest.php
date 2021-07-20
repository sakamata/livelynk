<?php

namespace App\Http\Requests\API\StayInfo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use \Symfony\Component\HttpFoundation\Response;

class PostRequest extends FormRequest
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
            'hashKey' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'min:4', 'max:64'],
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required','string', 'max:255'],
            'globalIp' => ['required', 'ip', 'min:1', 'max:255'],
        ];
    }

    /**
     * [override] バリデーション失敗時ハンドリング
     *
     * @param Validator $validator
     * @throw HttpResponseException
     * @see FormRequest::failedValidation()
     */
    protected function failedValidation(Validator $validator)
    {
        $response['data']    = [];
        $response['status']  = 'NG';
        $response['summary'] = 'Failed validation.';
        $response['errors']  = $validator->errors()->toArray();

        throw new HttpResponseException(
            response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
