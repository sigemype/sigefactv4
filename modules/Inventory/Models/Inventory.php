<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;
use Modules\Item\Models\ItemLot;

class Inventory extends ModelTenant
{
    protected $with = ['transaction', 'warehouse', 'warehouse_destination', 'item'];

    protected $fillable = [
        'type',
        'description',
        'item_id',
        'warehouse_id',
        'warehouse_destination_id',
        'quantity',
        'inventory_transaction_id',
        'lot_code',
        'detail',
        'inventories_transfer_id'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouse_destination()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_destination_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Se usa en la relacion con el inventario kardex en modules/Inventory/Traits/InventoryTrait.php.
     * Tambien se debe tener en cuenta modules/Inventory/Providers/InventoryKardexServiceProvider.php y
     * app/Providers/KardexServiceProvider.php para la correcta gestion de kardex
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function inventory_kardex()
    {
        return $this->morphMany(InventoryKardex::class, 'inventory_kardexable');
    }

    public function transaction()
    {
        return $this->belongsTo(InventoryTransaction::class);
    }

    public function lots()
    {
        return $this->morphMany(ItemLot::class, 'item_loteable');
    }
}
