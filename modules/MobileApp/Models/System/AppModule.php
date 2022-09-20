<?php

namespace Modules\MobileApp\Models\System;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;


class AppModule extends Model
{

    use UsesSystemConnection;

    protected $fillable = [
        'value',
        'description',
        'order_menu',
    ];

    protected $casts = [
        'order_menu' => 'int',
    ];

}
