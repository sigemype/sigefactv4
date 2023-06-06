<?php

namespace Modules\Hotel\Models;

use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\ModelTenant;
use Modules\Finance\Models\PaymentFile;
use App\Models\Tenant\{
    Cash,
};
use App\Traits\PaymentModelHelperTrait;


class HotelRentItemPayment extends ModelTenant
{
    use PaymentModelHelperTrait;

    public $timestamps = false;

    protected $fillable = [
        'hotel_rent_item_id',
        'date_of_payment',
        'payment_method_type_id',
        'reference',
        'change',
        'payment',
    ];

    protected $casts = [
        'date_of_payment' => 'date',
    ];

    public function payment_method_type()
    {
        return $this->belongsTo(PaymentMethodType::class);
    }

    public function global_payment()
    {
        return $this->morphOne(GlobalPayment::class, 'payment');
    }
     
    /**
     * Relacion con pagos de la habitacion y productos
     */
    public function associated_record_payment()
    {
        return $this->belongsTo(HotelRentItem::class, 'hotel_rent_item_id');
    }
    

    /**
     * 
     * Obtener relaciones necesarias o aplicar filtros para reporte pagos - finanzas
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeFilterRelationsPayments($query)
    {
        return $query->with([
                        'payment_method_type' => function($payment_method_type){
                            $payment_method_type->select(['id', 'description']);
                        }, 
                    ]);
    }


    /**
     * 
     * Obtener informacion del pago y registro origen relacionado
     *
     * @return array
     */
    public function getRowResourceCashPayment()
    {
        return [
            'type' => 'hotel_rent_item',
            'type_transaction' => 'income',
            'type_transaction_description' => $this->associated_record_payment->getDescriptionFromType(),
            'date_of_issue' => $this->associated_record_payment->hotel_rent->input_date,
            'number_full' => '-',
            'acquirer_name' => $this->associated_record_payment->hotel_rent->customer->name,
            'acquirer_number' => $this->associated_record_payment->hotel_rent->customer->number,
            'currency_type_id' => $this->associated_record_payment->hotel_rent->getDefaultCurrency(),
            'document_type_description' => 'R. HABITACIÓN (HOTEL)',
            'payment_method_type_id' => $this->payment_method_type_id,
            'payment' => $this->payment,
        ];
    }

}