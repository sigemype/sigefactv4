<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\ModelTenant;
use Illuminate\Support\Collection;
class InventoryTransfer extends ModelTenant{

    protected $table = 'inventories_transfer';

    protected $fillable = [
        'description',
        'warehouse_id',
        'warehouse_destination_id',
        'quantity',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouse_destination(){
        return $this->belongsTo(Warehouse::class, 'warehouse_destination_id');
    }

    public function inventory(){
        return $this->hasMany(Inventory::class, 'inventories_transfer_id');
    }

}
