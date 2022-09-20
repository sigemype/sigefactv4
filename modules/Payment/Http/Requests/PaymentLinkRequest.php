<?php

namespace Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Tenant\DocumentPaymentRequest;


class PaymentLinkRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $without_payment = $this->input('without_payment');
        $payment_id = $this->input('payment_id');

        // para registrar links de pago desde el listado (no proviene de un pago)
        if($without_payment)
        {
            return [
                'payment_link_type_id' => [
                    'required',
                ],
                'total' => [
                    'required',
                    'gt:0',
                ],
            ];
        }


        // para links de pago que son a partir de un cpe y se registra junto al pago
        if(!$payment_id)
        {
            return $this->validateWithDocumentPayment();
        }


        // para links de pago que son a partir de un cpe
        return [
            'payment_link_type_id' => [
                'required',
            ],
            'payment_id' => [
                'required',
            ],
            'instance_type' => [
                'required',
            ],
            'total' => [
                'required',
                'gt:0',
            ],
        ];


    }
    

    /**
     * 
     * Retornar validaciones para el link de pago y el pago del documento
     *
     * @return array
     */
    public function validateWithDocumentPayment()
    {
        $document_payment_request = (new DocumentPaymentRequest)->rules();

        $general =  [
            'payment_link_type_id' => [
                'required',
            ],
            'instance_type' => [
                'required',
            ],
            'document_id' => [
                'required',
            ],
            'total' => [
                'required',
                'gt:0',
            ],
        ];

        return array_merge($document_payment_request, $general);
    }

}