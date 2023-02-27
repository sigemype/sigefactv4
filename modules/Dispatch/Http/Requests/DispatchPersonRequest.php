<?php

namespace Modules\Dispatch\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DispatchPersonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'identity_document_type_id' => [
                'required',
            ],
            'number' => [
                'required',
            ],
            'name' => [
                'required',
            ],
            'location_id' => [
                'required',
            ],
            'address' => [
                'required',
            ],
        ];
    }

}
