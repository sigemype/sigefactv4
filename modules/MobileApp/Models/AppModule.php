<?php

namespace Modules\MobileApp\Models;

use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\User;


class AppModule extends ModelTenant
{

    protected $fillable = [
        'value',
        'description',
        'order_menu',
    ];


    protected $casts = [
        'order_menu' => 'int',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    /**
     * @return array
     */
    public function getRowResource()
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'description' => $this->description,
            'order_menu' => $this->order_menu,
        ];
    }


    /**
     * 
     * Datos para api
     * 
     * @return array
     */
    public function getPermissionsApp()
    {
        return [
            'value' => $this->value,
            'description' => $this->description,
        ];
    }


}
