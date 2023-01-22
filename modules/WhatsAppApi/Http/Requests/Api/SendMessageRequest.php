<?php

namespace Modules\WhatsAppApi\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class SendMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $send_type = $this->input('send_type');

        $validations = [];

        $general_validations = [
            'send_type' => [
                'required',
            ],
            'phone_number' => [
                'required',
                'numeric',
            ]
        ];
        

        if($send_type === 'document')
        {
            $validations = [
                'document.link' => [
                    'required',
                ],
                'document.filename' => [
                    'required',
                ],
            ];
        }
        else
        {
            $validations = [
                'message' => [
                    'required',
                ],
            ];

        }

        return array_merge($general_validations, $validations);

    }


}
