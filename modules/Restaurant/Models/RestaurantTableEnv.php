<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;

class RestaurantTableEnv extends ModelTenant
{
    const ENV_1 =  'Ambiente 1';
    const ENV_2 =  'Ambiente 2';
    const ENV_3 =  'Ambiente 3';
    const ENV_4 =  'Ambiente 4';

    public $timestamps = false;


    protected $fillable = [
        'name',
        'active',
        'tables_quantity',
    ];
}
