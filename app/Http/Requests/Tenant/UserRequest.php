<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required'
            ],
            'email' => [
                'required',
                'email',
            ],
            'type' => [
                'required'
            ],
            'establishment_id' => [
                'required'
            ],
            'password' => [
                'min:6',
                'confirmed',
            ],

            
            'personal_email' => [
                'nullable',
                'email',
            ],
            'corporate_email' => [
                'nullable',
                'email',
            ],
            'personal_cell_phone' => [
                'nullable',
                // 'numeric',
            ],
            'corporate_cell_phone' => [
                'nullable',
                // 'numeric',
            ],
            'number' => [
                'nullable',
                'numeric',
            ],
            
            'default_document_types' => 'required_if:multiple_default_document_types, "1"|array',
            'default_document_types.*.document_type_id' => 'required',
            'default_document_types.*.series_id' => 'required',

        ];
    }
}