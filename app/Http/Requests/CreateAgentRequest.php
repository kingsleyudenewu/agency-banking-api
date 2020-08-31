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

        ];


        if($this->path() === "api/v1/customers" && $this->isMethod("post"))
         {

            $validationRules['lga'] = 'nullable|max:255';
            $validationRules['next_of_kin_phone'] = 'required|max:150';
            $validationRules['next_of_kin_name'] = 'required|max:255';
            $validationRules['passport_photo'] = 'nullable|image|max:10240'; // 10mb largest
            $validationRules['has_bank_account'] = 'boolean';
            $validationRules['occupation'] = 'required';

        }  elseif ($this->canChangeCommission())
        {
              // TODO: move that super to a constant variable
            if($this->type  === 'super') {
                $systemCommission = intval(settings('min_commission'));
                $maxCommission = intval(settings('max_commission')) - $systemCommission;

                $validationRules['commission'] = [
                    'required_if:type,super',
                    'numeric',
                    function ($attribute, $value, $fail) use ($maxCommission) {
                        if ($value < 0) {
                            $fail('The commission must be a negative value');
                        } else if ($value > $maxCommission) {
                            $fail('You can not set commission greater than ' . $maxCommission / 100 . '%');
                        }
                    }
                ];


                $validationRules['commission_for_agent'] = [
                    'required_if:type,super',
                    'numeric',
                    function ($attribute, $value, $fail) use ($maxCommission, $systemCommission) {
                        $newMax = $maxCommission - $this->commission;
                        if($newMax < $value) {
                            $msg = sprintf("You have set %%%s for system and %%%s for agent. You can NOT set an additional %%%s as commission-for-agent",
                                $systemCommission/100, $this->commission / 100, $value/100);
                            $fail($msg);
                        }

                        $sysMax = intval(settings('max_commission'));
                        $newMin =  ($sysMax - $newMax) + $value;

                        if($newMin < $sysMax)
                        {
                            $msg  = ( $sysMax  -  $newMin) / 100;
                            $fail(sprintf('You must add an additional %%%s or adjust the agent commission.', $msg));
                        }

                        if($newMin > $sysMax)
                        {
                            $fail(sprintf('The total commission is more than %%%s. You now have %%%s', $sysMax / 100, $newMin / 100));
                        }


                    }
                ];
            }

        }

        if($this->path() === 'api/v1/agents' && $this->isMethod("post"))
        {
            $validationRules['bvn'] =  [
                'required',
                function($attribute, $value, $fail) {
                    if(strlen($value) !== 11 || !is_numeric($value)) {
                        $fail('BVN must be only numbers and 11 characters long.');
                    }
                }
            ];

            $validationRules['business_type'] = 'required';
        }

        $validationRules['state_id'] = 'nullable|uuid';
        $validationRules['business_name'] = 'nullable|max:255';
        $validationRules['business_address'] = 'nullable|max:255';
        $validationRules['password'] = 'required|min:6|strong_password';
        $validationRules['type'] = 'nullable';

        return $validationRules;
    }



    protected function prepareForValidation()
    {
        $data = [ ];
        $data['password'] = str_random(32) . 'S.32.$a' . str_random(60);

        if(!request('email'))
        {
            $data['email'] = str_random(32).$this->phone.'@email-place.koloo.ng';
        }

        if ($this->canChangeCommission())
        {
            $data['commission'] = number_format($this->commission, '2') *  100;
            $data['commission_for_agent'] =  number_format($this->commission_for_agent, 2) * 100;
        }

        if(!request('country_code') && $user = auth()->user())
        {
            // Grap the country from the logged in user
            $data['country_code'] =   $user->country ? $user->country->code : config('koloo.default_country', 'NG'); // Nigeria by default
        } else {
            $data['country_code'] = request('country_code');
        }



        $data['phone'] = PhoneNumber::format($this->cleanPhone($this->phone), $data['country_code']);


        return $this->merge($data);
    }

    /**
     * @return bool
     */
    private function canChangeCommission(): bool
    {
        return $this->path() === 'api/v1/agents' && $this->isMethod("post") && $this->user()->hasRole(\App\User::ROLE_ADMIN);
    }


}
