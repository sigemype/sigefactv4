<?php

namespace Modules\Pos\Models;
 
use App\Models\Tenant\Cash;
use App\Models\Tenant\ModelTenant;
use Modules\Finance\Models\GlobalPayment;
use Carbon\Carbon;
use App\Models\Tenant\PaymentMethodType;


class CashTransaction extends ModelTenant
{

    public $timestamps = false;
    
    protected $fillable = [
        'cash_id',
        'payment_method_type_id',  
        'date',  
        'description',  
        'payment',  
    ];
 
    protected $casts = [
        // 'date_of_payment' => 'date',
    ];
  
    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }
 
    public function global_payment()
    {
        return $this->morphOne(GlobalPayment::class, 'payment');
    }
 
    public function associated_record_payment()
    {
        return $this->belongsTo(Cash::class, 'cash_id');
    }

    public function getDateOfPaymentAttribute()
    {
        return Carbon::parse($this->date);
    }

    public function payment_method_type()
    {
        return $this->belongsTo(PaymentMethodType::class);
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
        // \Log::info("cash");
        return $query->with([
            'payment_method_type' => function($payment_method_type){
                $payment_method_type->select('id', 'description');
            },  
        ]);
    }
}