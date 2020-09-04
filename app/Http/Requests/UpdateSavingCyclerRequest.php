<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSavingCyclerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasRole(User::ROLE_ADMIN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|uuid|exists:saving_cycles,id',
            'title' => [
                'required',
                Rule::unique('saving_cycles')->ignore(request('id')),
            ],
            'description' => 'nullable|max:255',
            'duration' => 'required|numeric|min:1',
            'min_saving_frequent' => 'required|min:0|max:100',
            'min_saving_amount' => 'required|numeric|min:0',
            'percentage_to_charge' => 'required_if:charge_type,percent|min:0|max:100'
        ];
    }
}
