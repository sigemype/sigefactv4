<?php

namespace Modules\Dispatch\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OriginAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $id = $this->input('id');

        return [
            'address' => [
                'required',
            ],
            'location_id' => [
                'required',
            ],
        ];
    }

}
