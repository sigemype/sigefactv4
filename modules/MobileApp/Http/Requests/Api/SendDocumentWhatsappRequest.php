<?php

namespace Modules\MobileApp\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendDocumentWhatsappRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => [
                'required',
            ],
            'document_type_id' => [
                'required',
            ],
            'phone_number' => [
                'required',
                'numeric',
                'digits: 9',
            ],
            'format' => [
                'required',
            ],
        ];
    }

}