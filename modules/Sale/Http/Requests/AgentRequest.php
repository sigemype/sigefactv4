<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgentRequest extends FormRequest
{
     
    public function authorize()
    {
        return true; 
    }
 
    public function rules()
    { 
        $id = $this->input('id');
        
        return [
            'internal_id' => [
                'nullable',
                Rule::unique('tenant.agents')->ignore($id),
            ],
            'name' => [
                'required',
                Rule::unique('tenant.agents')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
            ],
            'telephone' => [
                'required',
            ],
        ];
    }
}
