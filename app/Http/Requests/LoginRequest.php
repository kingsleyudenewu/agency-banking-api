<?php

namespace App\Http\Requests;


use Illuminate\Validation\Rule;

class LoginRequest extends BaseRequest
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


}
