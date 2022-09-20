<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyPseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');

        return [
            'url_signature_pse' => 'required_if:send_document_to_pse, "true"',
            'url_send_cdr_pse' => 'required_if:send_document_to_pse, "true"',
            // 'client_id_pse' => 'required_if:send_document_to_pse, "true"',
            // 'password_pse' => 'required_if:send_document_to_pse, "true"',
            'url_login_pse' => 'required_if:send_document_to_pse, "true"',
            'user_pse' => 'required_if:send_document_to_pse, "true"',
        ];
    }
}