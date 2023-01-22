<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;
use Modules\Inventory\Models\InventoryKardex;
use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Modules\Item\Models\ItemLot;


/**
 * Class Inventory
 *
 * @property int $id
 * @property string|null $type
 * @property string $description
 * @property string|null $detail
 * @property int $item_id
 * @property int $warehouse_id
 * @property int|null $warehouse_destination_id
 * @property string|null $inventory_transaction_id
 * @property float $quantity
 * @property string|null $lot_code
 * @property int|null $inventories_transfer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property \Modules\Inventory\Models\InventoryTransfer|null $inventories_transfer
 * @property InventoryTransaction|null $inventory_transaction
 * @property Item $item
 * @property Warehouse $warehouse
 * @property-read \Illuminate\Database\Eloquent\Collection|InventoryKardex[] $inventory_kardex
 * @package Modules\Inventory\Models
 * @mixin ModelTenant
 * @property-read int|null $inventory_kardex_count
 * @property-read \Illuminate\Database\Eloquent\Collection|ItemLot[] $lots
 * @property-read int|null $lots_count
 * @property-read \Modules\Inventory\Models\InventoryTransaction $transaction
 * @property-read \Modules\Inventory\Models\Warehouse $warehouse_destination
 * @mixin \Eloquent
 * @method static Builder|Inventory newModelQuery()
 * @method static Builder|Inventory newQuery()
 * @method static Builder|Inventory query()
 */
class Inventory extends ModelTenant
{
    use UsesTenantConnection;

    protected $with = [
        'transaction',
        'warehouse',
        'warehouse_destination',
        'item'
    ];

    protected $casts = [
        'item_id' => 'int',
        'warehouse_id' => 'int',
        'warehouse_destination_id' => 'int',
        'quantity' => 'float',
        'inventories_transfer_id' => 'int',
        'date_of_issue' => 'date',
        'real_stock' => 'float',
        'system_stock' => 'float',
    ];
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
        'inventories_transfer_id',
        'comments',
        'guide_id',
        'date_of_issue',
        'created_at',
        'real_stock',
        'system_stock',
    ];

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse_destination()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_destination_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(InventoryTransaction::class, 'inventory_transaction_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function lots()
    {
        return $this->morphMany(ItemLot::class, 'item_loteable');
    }

    /**
     * Obtener datos para reporte movimientos
     *
     * @return array
     */
    public function getRowResourceReport()
    {

        $input = '-';
        $output = '-';

        if ($this->transaction->type === 'input') {
            $input = $this->quantity;
        } else {
            $output = -$this->quantity;
        }

        $guide_id = null;
        $guide_number = null;
        $guide_date_of_issue = null;
        if (count($this->inventory_kardex) > 0) {
            $inventory_kardexable = $this->inventory_kardex[0]->inventory_kardexable;
            if ($inventory_kardexable) {
                $guide = Guide::query()->where('id', $inventory_kardexable->guide_id)->first();
                if ($guide) {
                    $guide_number = $guide->series . '-' . $guide->number;
                    $guide_date_of_issue = $guide->date_of_issue->format('Y-m-d');
                    $guide_id = $guide->id;
                }
            }
        }

        $warehouse_name = null;
        if ($this->warehouse) {
            $warehouse_name = $this->warehouse->description;
        }

        return [
            'description' => $this->description,
            'item_id' => $this->item_id,
            'item_description' => $this->item->getInternalIdDescription(),
            'inventory_transaction_id' => $this->inventory_transaction_id,
            'quantity' => $this->quantity,
            'input' => $input,
            'output' => $output,
            'guide_id' => $guide_id,
            'date_time' => $this->created_at->format('Y-m-d H:i:s'),
            'guide_number' => $guide_number,
            'guide_date_of_issue' => $guide_date_of_issue,
            'warehouse_name' => $warehouse_name
        ];

    }

    /**
     * Filtros para reporte movimientos
     * Usado en:
     * ReportMovementController
     *
     * @param  $query
     * @param  $warehouse_id
     * @param  $inventory_transaction_id
     * @param  $date_start
     * @param  $date_end
     * @param  $order_inventory_transaction_id
     */
    public function scopeWhereFilterReportMovement($query, $warehouse_id, $inventory_transaction_id, $date_start,
                                                   $date_end, $item_id, $order_inventory_transaction_id, $movement_type)
    {
        $_order_inventory_transaction_id = $order_inventory_transaction_id == 'true';

        $query->with(['inventory_kardex']);

//        dd($movement_type);
        if ($warehouse_id) {
            $query->where('warehouse_id', $warehouse_id);
        }

        if ($movement_type !== 'all') {
            $query->whereHas('transaction', function ($q) use ($movement_type) {
                $q->where('type', $movement_type);
            });
        } else {
            $query->whereHas('transaction');
        }

        $query->whereHas('inventory_kardex', function ($query) use ($date_start, $date_end, $item_id) {

            if ($date_start) $query->where('date_of_issue', '>=', $date_start);
            if ($date_end) $query->where('date_of_issue', '<=', $date_end);
            if ($item_id) $query->where('item_id', $item_id);

        });

        if ($inventory_transaction_id) $query->where('inventory_transaction_id', $inventory_transaction_id);

        if ($_order_inventory_transaction_id) $query->orderBy('inventory_transaction_id');

        return $query;
    }

    public function scopeWhereFilterReportStock($query, $warehouse_id, $date_start, $date_end, $order_by_item, $order_by_timestamps, $additional_filters)
    {

        $query->with(['inventory_kardex'])
            ->whereHas('transaction')
            // ->where('warehouse_id', $warehouse_id)
            // ->where('description', 'like', 'STock Real')
            ->filterByWarehouse($warehouse_id)
            ->where('inventories.description', 'like', 'STock Real')
            ->whereHas('inventory_kardex', function ($query) use ($date_start, $date_end) {

                if ($date_start) $query->where('date_of_issue', '>=', $date_start);
                if ($date_end) $query->where('date_of_issue', '<=', $date_end);

            })
            ->additionalFiltersStockReport($additional_filters);

        if ($order_by_timestamps) $query->applyOrderByCreatedAt();

        if ($order_by_item) $query->applyOrderByItemDescription();

        return $query;
    }


    /**
     *
     * Filtros adicionales por campos del producto
     *
     * @param Builder $query
     * @param array $additional_filters
     * @return Builder
     */
    public function scopeAdditionalFiltersStockReport($query, $additional_filters)
    {
        $search_column = $additional_filters['search_column'];
        $search_input = $additional_filters['search_input'];

        if ($search_input && $search_input != '') {
            $query->whereHas('item', function ($query_item) use ($search_column, $search_input) {
                return $query_item->filterRecordsStockReport($search_column, $search_input);
            });
        }

        return $query;
    }


    /**
     *
     * Filtrar por almacén
     *
     * @param Builder $query
     * @param int $warehouse_id
     * @return Builder
     */
    public function scopeFilterByWarehouse($query, $warehouse_id)
    {
        if ($warehouse_id) $query->where('inventories.warehouse_id', $warehouse_id);

        return $query;
    }


    /**
     *
     * Ordener por fecha de creacion del registro
     *
     * @param Builder $query
     * @param string $order
     * @return Builder
     */
    public function scopeApplyOrderByCreatedAt($query, $order = 'desc')
    {
        return $query->orderBy('inventories.created_at', $order);
    }


    /**
     *
     * Ordernar por descripcion del producto
     *
     * @param Builder $query
     * @param string $order
     * @return Builder
     */
    public function scopeApplyOrderByItemDescription($query, $order = 'asc')
    {
        return $query->join('items', 'items.id', '=', 'inventories.item_id')
            ->select('inventories.*')
            ->orderBy('items.description', $order);
    }


    /**
     *
     * Datos para reporte de ajuste de stock
     *
     * @return array
     */
    public function getRowResourceReportStock()
    {
        $ajust = '-';
        $input = '-';
        $output = '-';

        if ($this->transaction->type === 'input') {
            $ajust = $this->quantity;
            $input = $this->quantity;
        } else {
            $ajust = -$this->quantity;
            $output = -$this->quantity;
        }

        // $stock_system=$this->getStockFull($this->created_at->format('Y-m-d'));

        return [
            'item_description' => $this->item->getInternalIdDescription(),
            // 'stock_system' => $stock_system,
            // 'stock_real' => $stock_system+$ajust,
            'ajust' => $ajust,

            'real_stock' => $this->real_stock,
            'system_stock' => $this->system_stock,
            'description' => $this->description,
            'inventory_transaction_id' => $this->inventory_transaction_id,
            'input' => $input,
            'output' => $output,
            'date_time' => $this->created_at->format('Y-m-d H:i:s'),
        ];

    }


    public function getStockFull($date)
    {
        $stock_system = InventoryKardex::where('inventory_kardexable_type', 'Modules\Inventory\Models\Inventory')->where('item_id', $this->item_id)->where('warehouse_id', $this->warehouse_id)->where('date_of_issue', '<', $date)->sum('quantity');

        return $stock_system;

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventories_transfer()
    {
        return $this->belongsTo(InventoryTransfer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory_transaction()
    {
        return $this->belongsTo(InventoryTransaction::class, 'inventory_transaction_id', 'id');
    }

}
