<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Database\Eloquent\Builder;


class InventoryConfiguration extends ModelTenant
{

    protected $fillable = [ 
        'stock_control',
        'generate_internal_id'
    ];
  

    protected $casts = [
        'generate_internal_id' => 'boolean',
    ];
    

    /**
     * 
     * Obtener campo individual de la configuracion
     *
     * @param  Builder $query
     * @param  string $column
     * @return Builder
     */
    public function scopeGetRecordIndividualColumn($query, $column)
    {
        return $query->select($column)->firstOrFail()->{$column};
    }

}