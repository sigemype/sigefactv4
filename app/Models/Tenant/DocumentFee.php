<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\CurrencyType;

class DocumentFee extends ModelTenant
{
    public $timestamps = false;
    protected $table = 'document_fee';

    protected $fillable = [
        'document_id',
        'date',
        'cat_payment_method_type_id',
        'currency_type_id',
        'amount',
        'reference'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'float',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }
}
