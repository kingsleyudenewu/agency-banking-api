<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() ? false : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'identity' => 'required',
            'password' => 'required',
            'country' => [
                    'required',
                    'max:2',
                    Rule::exists('countries', 'code')
                            ->where('code', $this->country)
                ]
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $data = [
            'status' => 'error',
            'message' => _('The given data was invalid.'),
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($data, 400));
    }

}
