<?php

namespace Modules\Sale\Models;

use App\Models\Tenant\{
    ModelTenant
};


class Agent extends ModelTenant
{

    protected $fillable = [
        'name',
        'internal_id',
        'email',
        'telephone', 
    ];
 
    
    /**
     * Datos para listado/edicion
     *
     * @return void
     */
    public function getRowResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'internal_id' => $this->internal_id,
            'email' => $this->email,
            'telephone' => $this->telephone, 
        ];
    }


    public function getSearchDescriptionAttribute()
    {
        return $this->internal_id ? "{$this->internal_id} - {$this->name}" : $this->name;
    }

    
    /**
     * Datos para búsqueda
     *
     * @return arraay
     */
    public function getRowSearch()
    {
        return [
            'id' => $this->id,
            'search_description' => $this->search_description,
        ];
    }

}
