<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
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
            ],
            'pricing' => [
                'required',
                'numeric' ,
                'min:0'  
            ],
            'limit_users' => [
                'required',
                'numeric',
                'integer', 
            ],
            'limit_documents' => [
                'required',
                'numeric' ,
                'integer', 
            ],
            'plan_documents' => [
                // 'required'
            ],
            
            'establishments_limit' => $this->validationEstablishmentsLimit(),
            'sales_limit' => $this->validationSalesLimit(),
        ];
    }

    
    /**
     * 
     * Validacion para limite de ventas
     *
     * @return array
     */
    private function validationSalesLimit()
    {

        if(!$this->input('sales_unlimited'))
        {
            return [
                'required',
                // 'integer', 
                'numeric' ,
                'gt:0', 
            ];
        }

        return [];
    }


    /**
     * 
     * Validacion para limite establecimiento
     *
     * @return array
     */
    private function validationEstablishmentsLimit()
    {

        if(!$this->input('establishments_unlimited'))
        {
            return [
                'required',
                'integer', 
                'numeric' ,
                'gt:0', 
            ];
        }

        return [];
    }

}