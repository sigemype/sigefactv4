<?php

namespace Modules\LevelAccess\Models;

use App\Models\Tenant\{
    ModelTenant,
};

class SystemActivityLogType extends ModelTenant 
{

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'description',
    ];

}
