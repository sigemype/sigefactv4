<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\ModelTenant;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Item\Models\ItemLotsGroup;
use Modules\Item\Models\ItemLot;

class InventoryTransferItem extends ModelTenant
{
  use UsesTenantConnection;

  protected $fillable = [
    'inventory_transfer_id',
    'item_lots_group_id',
    'item_lot_id',
  ];

  protected $casts = [
    'inventory_transfer_id' => 'int',
    'item_lots_group_id' => 'int',
    'item_lot_id' => 'int',
  ];

  /**
   * This function returns the InventoryTransfer model that is associated with this Inventory model.
   *
   * @return The relationship between the two models.
   */
  public function inventories_transfer()
  {
    return $this->belongsTo(InventoryTransfer::class);
  }

  public function item_lot()
  {
    return $this->belongsTo(ItemLot::class);
  }

  public function item_lots_group()
  {
    return $this->belongsTo(ItemLotsGroup::class);
  }
}