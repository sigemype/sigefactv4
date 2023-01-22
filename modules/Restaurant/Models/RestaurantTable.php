<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;

class RestaurantTable extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'status',
        'products',
        'total',
        'personas',
        'cliente',
        'comentarios',
        'label',
        'shape',
        'environment',
        'waiter'
    ];


    public function getProductsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setProductsAttribute($value)
    {
        $this->attributes['products'] = (is_null($value))?null:json_encode($value);
    }
}
