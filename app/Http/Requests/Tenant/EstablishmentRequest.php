<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Tenant\Configuration;


class EstablishmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        $id = $this->get('id');

        $validate_email = Configuration::getRecordIndividualColumn('remove_validation_email_establishments') ? '' : 'email';

        return [
            'description' => [
                'required',
                Rule::unique('tenant.establishments')->ignore($id),
            ],
            'department_id' => [
                'required',
            ],
            'province_id' => [
                'required',
            ],
            'district_id' => [
                'required',
            ],
            'address' => [
                'required',
            ],
            'email' => [
                'nullable',
                'max:255',
                $validate_email
            ],
            'telephone' => [
                'required',
            ],
            'code' => [
                'required',
            ],
        ];
    }
}