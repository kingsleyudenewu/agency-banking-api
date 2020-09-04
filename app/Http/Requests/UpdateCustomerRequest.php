<?php

namespace App\Http\Requests;

use App\Koloo\PhoneNumber;
use App\Rules\BannedName;
use App\User;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends BaseRequest
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
        return  [
            'id'        => 'required|uuid|exists:users,id',
            'name'      => 'required|max:255',
            'phone'     => [
                'required',
                 Rule::unique('users', 'phone')->ignore($this->id),
            ],
            'next_of_kin_phone' => 'required|max:150',
            'next_of_kin_name' => 'required|max:255',
            'passport_photo'   => 'nullable|image|max:10240',
            'has_bank_account'  => ['required', Rule::in(['true', 'false'])],
            'occupation'   => 'required',
            'dob' => 'required|date|older_than',
            'gender' => [
                'required',
                Rule::in(['female', 'male'])
            ],
            'address' => 'required',
        ];


    }

    protected function prepareForValidation()
    {
        $data['phone'] = PhoneNumber::format($this->cleanPhone($this->phone), $this->user()->country_code);

        return $this->merge($data);
    }
}
