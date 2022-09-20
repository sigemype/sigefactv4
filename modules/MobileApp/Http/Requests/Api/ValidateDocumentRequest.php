<?php

namespace Modules\MobileApp\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'series' => [
                'required',
            ],
            'document_type_id' => [
                'required',
            ],
            'number' => [
                'required',
                'numeric',
            ],
            'date_of_issue' => [
                'required',
            ],
            'total' => [
                'required',
            ],
        ];
    }

    
}