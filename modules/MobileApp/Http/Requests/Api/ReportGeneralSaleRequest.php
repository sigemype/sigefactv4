<?php

namespace Modules\MobileApp\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportGeneralSaleRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'period' => [
                'required',
            ],
            'date_start' => [
                'required',
            ],
            'date_end' => [
                'required',
            ],
            'month_start' => [
                'required',
            ],
            'month_end' => [
                'required',
            ],
        ];
    }

}