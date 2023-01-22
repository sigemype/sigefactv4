<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;

class RestaurantNote extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'description',
    ];

   
}
