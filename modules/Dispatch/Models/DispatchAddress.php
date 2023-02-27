<?php

namespace Modules\Dispatch\Models;

use App\Models\Tenant\ModelTenant;

class DispatchAddress extends ModelTenant
{
    public $table = 'dispatch_addresses';
    public $timestamps = false;

    protected $fillable = [
        'person_id',
        'address',
        'location_id',
        'is_active'
    ];

    protected $casts = [
        'location_id' => 'array',
        'is_active' => 'boolean',
    ];
}
