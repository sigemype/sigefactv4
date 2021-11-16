<?php

namespace Modules\Expense\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;
use App\Traits\AttributePerItems;

class ExpenseItem extends ModelTenant
{
    use AttributePerItems;

    public $timestamps = false;

    protected $fillable = [
        'expense_id',
        'description',
        'total',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

}
