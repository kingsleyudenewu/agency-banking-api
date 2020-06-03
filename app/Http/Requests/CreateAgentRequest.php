<?php

namespace App\Http\Requests;


use App\Koloo\PhoneNumber;
use App\Koloo\User;
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
            'name' => 'required|max:255',
            'email'     => [
                'required',
                'email',
                new BannedName,
                'unique:users',
            ],
            'address' => 'required',
            'dob' => 'required|date|older_than',
            'gender' => [
                'required',
                Rule::in(['female', 'male'])
            ],
            'bank_account_number' => 'nullable|numeric',
            'bank_name' => 'nullable',

        ];


        if($this->path() === "api/v1/customers" && $this->isMethod("post"))
         {

            $validationRules['lga'] = 'nullable|max:255';
            $validationRules['next_of_kin_phone'] = 'required|max:150';
            $validationRules['next_of_kin_name'] = 'required|max:255';
            $validationRules['passport_photo'] = 'nullable|image|max:10240'; // 10mb largest
            $validationRules['has_bank_account'] = 'boolean';

        }  elseif ($this->path() === 'api/v1/agents' && $this->isMethod("post"))
        {

            $validationRules['commission'] = [
                'required',
                'numeric',
                'min:'. intval(settings('min_commission')),
                function($attribute, $value, $fail) {

                    $maxCommission = auth()->user()->hasRole(\App\User::ROLE_ADMIN) ?
                        settings('max_commission') : intval(auth()->user()->profile->commission);

                    if($value > $maxCommission) {
                        $fail('You can not set commission greater than ' . $maxCommission / 100);
                    }

                }
            ];
        }


       $validationRules['password'] = 'required|min:6|strong_password';

        return $validationRules;
    }



    protected function prepareForValidation()
    {
        $data = [ ];

       if($this->country_code && strlen($this->country_code) === 2)
       {
           if($this->phone)
               $data['phone'] = PhoneNumber::format($this->cleanPhone($this->phone), $this->country_code);
       }



       //$data['password'] = 'S.$a' . str_random(60);
        //TODO: update this password to random string
        $data['password'] = 'S.$a2S1221sm0223';

        if(!request('email'))
        {
            $data['email'] = str_random(32).$this->phone.'@email-place.koloo.ng';
        }

        if ($this->path() === 'api/v1/agents' && $this->isMethod("post"))
        {
            $data['commission'] = intval(number_format($this->commission, '2') * 100);
        }

        if(!request('country_code') && $user = auth()->user())
        {
            // Grap the country from the logged in user
            $data['country_code'] =   $user->country ? $user->country->code : config('koloo.default_country', 'NG'); // Nigeria by default
        }

        return $this->merge($data);
    }
}
