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
        $password_rules = [
            'min:6',
            'confirmed',
        ];

        if($this->input('config_regex_password_user') ?? false)
        {
            $password_rules = array_merge($password_rules, [
                'regex:/[a-z]/',      
                'regex:/[A-Z]/',   
                'regex:/[0-9]/',
                'regex:/[@.$!%*#?&-]/',
            ]);
        }

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
            'password' => $password_rules,
            
            'establishment_id' => [
                'required'
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