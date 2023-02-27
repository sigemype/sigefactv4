<?php

namespace Modules\Dispatch\Models;

use App\Models\Tenant\ModelTenant;

class OriginAddress extends ModelTenant
{
    protected $fillable = [
        'address',
        'location_id',
        'is_default',
        'is_active'
    ];

    protected $casts = [
        'location_id' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}
