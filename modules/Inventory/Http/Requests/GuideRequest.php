<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuideRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->get('id');
        return [
            'date_of_issue' => [
                'required',
            ],
            'establishment_id' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'inventory_operation_id' => [
                'required',
            ],
        ];
    }
}
