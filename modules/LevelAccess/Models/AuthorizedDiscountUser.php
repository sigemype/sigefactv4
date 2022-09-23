<?php

namespace Modules\LevelAccess\Models;

use App\Models\Tenant\{
    ModelTenant,
    User,
};


class AuthorizedDiscountUser extends ModelTenant 
{

    protected $fillable = [
        'user_id',
        'seller_id',
        'date',
        'time',
        'token',
        'active',
    ];

    protected $casts = [
        'active' => 'bool',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function scopeFindTokenAvailable($query, $token)
    {
        return $query->where('token', $token)
                    ->where('active', true);
    }
        
}
