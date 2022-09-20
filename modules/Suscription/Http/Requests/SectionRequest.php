<?php

namespace Modules\Suscription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectionRequest extends FormRequest
{
     
    public function authorize()
    {
        return true; 
    }
 
    public function rules()
    { 
        
        $id = $this->input('id');
        
        return [
             
            'name' => [
                'required',
                Rule::unique('tenant.suscription_section')->ignore($id),
            ]
        ];

    }
}
