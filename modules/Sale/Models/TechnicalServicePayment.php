<?php

namespace Modules\Sale\Models;

use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\Cash;
use App\Traits\PaymentModelHelperTrait;


class TechnicalServicePayment extends ModelTenant
{
    use PaymentModelHelperTrait;

    protected $with = ['payment_method_type'];
    public $timestamps = false;

    protected $fillable = [
        'technical_service_id',
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

    public function associated_record_payment()
    {
        return $this->belongsTo(TechnicalService::class, 'technical_service_id');
    }

    public function technical_service()
    {
        return $this->belongsTo(TechnicalService::class);
    }

    public function getTotal(){
        return $this->payment;
    }

    
    /**
     * 
     * Filtros para obtener pagos en efectivo y con destino caja
     *
     * @param  Builder $query
     * @return Collection
     */
    public function scopeWhereFilterCashPayment($query)
    {
        return $query->where('payment_method_type_id', PaymentMethodType::CASH_PAYMENT_ID)
                    ->whereHas('global_payment', function($query){
                        return $query->where('destination_type', Cash::class);
                    });
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
            'type' => 'technical_service',
            'type_transaction' => 'income',
            'type_transaction_description' => 'Venta',
            'date_of_issue' => $this->associated_record_payment->date_of_issue->format('Y-m-d'),
            'number_full' => $this->associated_record_payment->number_full,
            'acquirer_name' => $this->associated_record_payment->customer->name,
            'acquirer_number' => $this->associated_record_payment->customer->number,
            'currency_type_id' => $this->associated_record_payment->currency_type_id,
            'document_type_description' => $this->associated_record_payment->getDocumentTypeDescription(),
            'payment_method_type_id' => $this->payment_method_type_id,
            'payment' => $this->payment,
        ];
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
                $payment_method_type->select('id', 'description');
            }, 
        ]);
    }


    /**
     * 
     * Filtros para obtener pagos en efectivo
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeFilterCashPaymentWithoutDestination($query)
    {
        return $query->where('payment_method_type_id', PaymentMethodType::CASH_PAYMENT_ID);
    }

    
    /**
     * 
     * Filtros para obtener pagos con transferencia
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeFilterTransferPayment($query)
    {
        return $query->where('payment_method_type_id', PaymentMethodType::TRANSFER_PAYMENT_ID);
    }
    
}
