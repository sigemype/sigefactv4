<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRentItemRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			// 'products'                  => 'required|array',
			// 'products.*.payment_status' => 'required|in:PAID,DEBT',

            'products.*.rent_payment.payment_method_type_id' => 'required_if:products.*.payment_status,"PAID"',
            'products.*.rent_payment.payment_destination_id' => 'required_if:products.*.payment_status,"PAID"',
            'products.*.rent_payment.payment' => 'required_if:products.*.payment_status,"PAID"',

		];
	}
	
	
    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'products.*.rent_payment.payment_method_type_id.required_if' => 'El campo m. pago es obligatorio cuando estado de pago es CANCELADO.',
            'products.*.rent_payment.payment_destination_id.required_if' => 'El campo destino es obligatorio cuando estado de pago es CANCELADO.',
        ];
    }

}
