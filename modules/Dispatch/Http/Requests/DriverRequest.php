<?php

namespace Modules\Dispatch\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $id = $this->input('id');

        return [
            'number' => [
                'required',
                Rule::unique('tenant.drivers')->ignore($id),
            ],
            'identity_document_type_id' => [
                'required',
            ],
            'name' => [
                'required',
            ],
            'license' => [
                'required'
            ],
            'telephone' => [
                'required',
            ],

        ];
    }

}
