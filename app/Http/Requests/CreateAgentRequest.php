<?php

namespace App\Http\Requests;


use App\Koloo\PhoneNumber;
use App\Koloo\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Rules\BannedName;

class CreateAgentRequest extends BaseRequest
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

        //dd();
        $validationRules =  [
            'country_code' => [
                'required',
                'max:2',
                Rule::exists('countries', 'code')
                    ->where('code', $this->country_code)
            ],
            'phone' => [
                'required',
                function($attribute, $value, $fail) {
                    if(User::findByPhone($value))
                    {
                        $fail(__('phone already taken'));
                    }
                }
            ],
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'other_name' => 'nullable|max:255',
            'email'     => [
                'required',
                'email',
                new BannedName,
                'unique:users',
            ],
            'address' => 'required',
            'dob' => 'required|date',
            'gender' => [
                'required',
                Rule::in(['female', 'male'])
            ],
            'bank_account_number' => 'required|numeric',
            'bank_name' => 'required',
            'secondary_phone' => 'nullable',
            'business_name' => 'required|max:255',
            'business_address' => 'required|max:255',
            'business_phone' => 'nullable|max:255',
            'business_email'    => 'nullable|email',
            'bvn'   => 'required|numeric',
            'emergency_phone' => 'nullable|max:255',
            'emergency_name' => 'nullable|max:255'

        ];


        if($this->path() === "api/v1/customers" && $this->isMethod("post"))
        {

            $validationRules['lga'] = 'required|max:255';
            $validationRules['next_of_kin_phone'] = 'required|max:150';
            $validationRules['marital_status'] = ['required', Rule::in('married','single','unknown')];
            $validationRules['state_id']  = 'required|uuid';

        }

        if(request('password'))
            $validationRules['password'] = 'required|min:6|strong_password';

        return $validationRules;
    }



    protected function prepareForValidation()
    {
        $data = [ ];

       if($this->country && strlen($this->country) === 2)
       {
           if($this->phone)
               $data['phone'] = PhoneNumber::format($this->cleanPhone($this->phone), $this->country);

           if($this->business_phone)
               $data['business_phone'] =  PhoneNumber::format($this->cleanPhone($this->business_phone), $this->country);

           if($this->next_of_kin_phone)
               $data['next_of_kin_phone'] = PhoneNumber::format($this->cleanPhone($this->next_of_kin_phone), $this->country);

           if($this->secondary_phone)
               $data['secondary_phone'] = PhoneNumber::format($this->cleanPhone($this->secondary_phone), $this->country);

       }

        if(!request('password'))
            $data['password'] = 'S.$a' . str_random(60);

        return $this->merge($data);
    }
}
