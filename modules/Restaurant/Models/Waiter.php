<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;

class Waiter extends ModelTenant
{
    protected $fillable = [
        'name',
        'last_name',
    ];
}
