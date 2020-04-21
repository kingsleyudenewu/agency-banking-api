<?php

namespace App\Http\Requests;

use App\Koloo\User;
use Illuminate\Foundation\Http\FormRequest;

class AgentDocumentUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = User::find($this->user()->id);
        return $user->canManageAgent();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $mimesType = settings('document_storage_mime_types');;
        $maxSize = settings('document_storage_max_size');
        $validDocumentFields = settings('valid_document_fields');

        return [
            'doc' => 'required|mimes:'.$mimesType.'|max:' . $maxSize,
            'id' => 'required|uuid|exists:users,id',
            'document_type' => 'required|in:'.$validDocumentFields
        ];
    }
}
