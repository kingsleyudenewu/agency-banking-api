<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        parent::failedValidation($validator);
        $data = [
            'status' => 'error',
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($data, 400));
    }

    protected function cleanPhone($str) : string {
        $phone = trim(preg_replace("/[^0-9]/", "", $str));
        return Str::after($phone, "+");
    }
}
