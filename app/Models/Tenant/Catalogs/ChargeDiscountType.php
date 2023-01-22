<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class ChargeDiscountType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_charge_discount_types";
    public $incrementing = false;

    public function scopeWhereType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWhereLevel($query, $level)
    {
        return $query->where('level', $level);
    }

        
    /**
     * 
     * Obtener descuentos globales que afectan y no afectan la base
     *
     * @return array
     */
    public static function getGlobalDiscounts()
    {
        return self::whereIn('id', ['02', '03'])->whereActive()->get();
    }
    
}