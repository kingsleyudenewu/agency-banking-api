<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSavingCyclerRequest extends FormRequest
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
            'title' => 'required|max:255|unique:saving_cycles,title',
            'description' => 'nullable|max:255',
            'duration' => 'required|numeric|min:1',
            'min_saving_frequent' => 'required|min:0|max:100',
            'min_saving_amount' => 'required|numeric|min:0',
            'percentage_to_charge' => 'required|min:0|max:100'
        ];
    }
}
