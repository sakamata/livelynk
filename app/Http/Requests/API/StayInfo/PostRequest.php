<?php

namespace App\Http\Requests\API\StayInfo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
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
            'hash_key' => [
                'required', 'regex:/^[a-zA-Z0-9-]+$/', 'max:64',
                Rule::exists('communities')->where(function ($query) {
                    $query->where('id', $this->community_id)
                        ->where('hash_key', $this->hash_key);
                })
            ],
            'domain' => [
                'required','string', 'max:191',
                Rule::exists('communities', 'mail_box_domain')->where(function ($query) {
                    $query->where('id', $this->community_id)
                        ->where('mail_box_domain', $this->domain);
                })
            ],
            'global_ip' => ['required', 'ip', 'max:191'],
            'name' => ['required', 'string', 'max:191'],
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
