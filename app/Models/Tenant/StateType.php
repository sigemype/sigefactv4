<?php

namespace App\Models\Tenant;

class StateType extends ModelTenant
{
    public $incrementing = false;
    public $timestamps = false;

    
    public static function getDataApiApp()
    {
        $states = self::get();

        return $states->push([
            'id' => 'all',
            'description' => 'Todos',
        ]);
    }

} 