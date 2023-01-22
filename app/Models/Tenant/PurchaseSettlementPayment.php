<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\PurchaseSettlement;

class PurchaseSettlementPayment extends ModelTenant
{
    protected $with = ['payment_method_type'];
    public $timestamps = false;

    protected $fillable = [
        'purchase_settlement_id',
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
        return $this->belongsTo(PurchaseSettlement::class,'purchase_settlement_id');
    }

    public function purchase_settlement()
    {
        return $this->belongsTo(PurchaseSettlement::class);
    }

    public function getTotal(){
        return $this->payment;
    }
}
