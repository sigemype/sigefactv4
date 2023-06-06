<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRentRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'customer_id'              => 'required|numeric',
			'customer'                 => 'required',
			'customer.name'            => 'required',
			'customer.address'         => 'required',
			'notes'                    => 'max:250',
			'towels'                   => 'required|numeric|min:1',
			'duration'                 => 'required|numeric|min:1',
			'quantity_persons'         => 'required|numeric|min:1',
			'payment_status'           => 'required|in:PAID,DEBT',
			'output_date'              => 'required|date_format:Y-m-d',
			'output_time'              => 'required|date_format:H:i',
			'product'                  => 'required',
			'hotel_rate_id'              => 'required|numeric',
			'affectation_igv_type_id' => 'required',

            'rent_payment.payment_method_type_id' => 'required_if:payment_status,"PAID"',
            'rent_payment.payment_destination_id' => 'required_if:payment_status,"PAID"',
            'rent_payment.payment' => 'required_if:payment_status,"PAID"',
		];
	}

	    
    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'rent_payment.payment_method_type_id.required_if' => 'El campo m. pago es obligatorio cuando estado de pago es PAGADO.',
            'rent_payment.payment_destination_id.required_if' => 'El campo destino es obligatorio cuando estado de pago es PAGADO.',
        ];
    }
	
}
