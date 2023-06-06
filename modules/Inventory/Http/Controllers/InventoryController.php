<?php

namespace Modules\Inventory\Http\Controllers;

use App\Models\Tenant\Item;
use App\Models\Tenant\Series;
use Barryvdh\DomPDF\PDF;
use Exception;

//use App\Models\Tenant\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Inventory\Models\Guide;
use Modules\Inventory\Models\InventoryTransfer;
use Modules\Item\Models\ItemLot;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Item\Models\ItemLotsGroup;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Inventory\Traits\InventoryTrait;
use Modules\Inventory\Models\InventoryKardex;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Models\InventoryTransferItem;
use Modules\Inventory\Http\Requests\InventoryRequest;
use Modules\Inventory\Http\Resources\InventoryResource;
use Modules\Inventory\Http\Resources\InventoryCollection;
use App\Imports\StockImport;
use Maatwebsite\Excel\Excel;
use Modules\Inventory\Http\Requests\RemoveRequest;


class InventoryController extends Controller
{
    use InventoryTrait;

    public function index()
    {
        return view('inventory::inventory.index');
    }

    public function columns()
    {
        return [
            'description' => 'Producto',
            'internal_id' => 'Código interno',
            'warehouse' => 'Almacén',
        ];
    }

    public function records(Request $request)
    {
        $column = $request->input('column');

        if ($column == 'warehouse') {
            $records = ItemWarehouse::with(['item', 'warehouse'])
                ->whereHas('item', function ($query) use ($request) {
                    $query->where('unit_type_id', '!=', 'ZZ');
                    $query->whereNotIsSet();
                })
                ->whereHas('warehouse', function ($query) use ($request) {
                    $query->where('description', 'like', '%' . $request->value . '%');
                })
                ->orderBy('item_id');
        } else {
            $records = $this->getCommonRecords($request);
        }

        return new InventoryCollection($records->paginate(config('tenant.items_per_page')));
    }


    /**
     *
     * Obtener registros
     *
     * @param Request $request
     * @return ItemWarehouse
     */
    public function getCommonRecords($request)
    {
        return ItemWarehouse::with(['item', 'warehouse'])
            ->whereHas('item', function ($query) use ($request) {
                $query->where('unit_type_id', '!=', 'ZZ');
                $query->whereNotIsSet();

                if ($this->applyAdvancedRecordsSearch() && $request->column === 'description') {
                    if ($request->value) $query->whereAdvancedRecordsSearch($request->column, $request->value);
                } else {
                    $query->where($request->column, 'like', '%' . $request->value . '%');
                }
            })
            ->orderBy('item_id');
    }


    public function tables()
    {
        return [
            'items' => $this->optionsItem(),
            'warehouses' => $this->optionsWarehouse()
        ];
    }

    public function record($id)
    {
        if (is_numeric($id)) {
            $record = new InventoryResource(ItemWarehouse::with(['item', 'warehouse'])->findOrFail($id));
        } else {
            request()->validate([
                'ids' => 'required|array|min:1'
            ]);
            $data = ItemWarehouse::with(['item', 'warehouse'])
                ->whereIn('id', request('ids'))
                ->get();

            $record = InventoryResource::collection($data);
        }

        return $record;
    }

    public function tables_transaction($type)
    {
        return [
            //            'items' => $this->optionsItemFull(),
            'warehouses' => $this->optionsWarehouse(),
            'inventory_transactions' => $this->optionsInventoryTransaction($type),
        ];
    }

    public function searchItems(Request $request)
    {
        $search = $request->input('search');

        return [
            'items' => $this->optionsItemFull($search, 20),
        ];
    }

    public function ExtraDataList()
    {
        return view('inventory::extra_info.index');
    }

    public function store(Request $request)
    {
        $result = DB::connection('tenant')->transaction(function () use ($request) {
            $item_id = $request->input('item_id');
            $warehouse_id = $request->input('warehouse_id');
            $quantity = $request->input('quantity');

            $item_warehouse = ItemWarehouse::firstOrNew(['item_id' => $item_id,
                'warehouse_id' => $warehouse_id]);
            if ($item_warehouse->id) {
                return [
                    'success' => false,
                    'message' => 'El producto ya se encuentra registrado en el almacén indicado.'
                ];
            }

            // $item_warehouse->stock = $quantity;
            // $item_warehouse->save();

            $inventory = new Inventory();
            $inventory->type = 1;
            $inventory->description = 'Stock inicial';
            $inventory->item_id = $item_id;
            $inventory->warehouse_id = $warehouse_id;
            $inventory->quantity = $quantity;
            $inventory->save();

            return [
                'success' => true,
                'message' => 'Producto registrado en almacén'
            ];
        });

        return $result;
    }

    public function store_transaction(InventoryRequest $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {

            // dd($request->all());
            $type = $request->input('type');
            $item_id = $request->input('item_id');
            $warehouse_id = $request->input('warehouse_id');
            $inventory_transaction_id = $request->input('inventory_transaction_id');
            $quantity = $request->input('quantity');
            $lot_code = $request->input('lot_code');
            $comments = $request->input('comments');
            $created_at = $request->input('created_at');


            $lots = ($request->has('lots')) ? $request->input('lots') : [];

            $item_warehouse = ItemWarehouse::firstOrNew(['item_id' => $item_id,
                'warehouse_id' => $warehouse_id]);

            $inventory_transaction = InventoryTransaction::findOrFail($inventory_transaction_id);

            if ($type == 'output' && ($quantity > $item_warehouse->stock)) {
                return [
                    'success' => false,
                    'message' => 'La cantidad no puede ser mayor a la que se tiene en el almacén.'
                ];
            }

            $inventory = new Inventory();
            $inventory->type = null;
            $inventory->description = $inventory_transaction->name;
            $inventory->item_id = $item_id;
            $inventory->warehouse_id = $warehouse_id;
            $inventory->quantity = $quantity;
            $inventory->inventory_transaction_id = $inventory_transaction_id;
            $inventory->lot_code = $lot_code;
            $inventory->comments = $comments;


            if ($created_at) {
                $inventory->date_of_issue = $created_at;
            }

            $inventory->save();

            $warehouse = Warehouse::query()->find($warehouse_id);

            $itm = Item::query()
                ->select('id', 'description')
                ->where('id', $item_id)
                ->first();
            $guide_items[] = [
                'id' => $item_id,
                'name' => $itm->description,
                'stock_add' => $quantity
            ];
            $res = (new GuideController())->storeWithData([
                'establishment_id' => $warehouse->establishment_id,
                'warehouse_id' => $warehouse_id,
                'date_of_issue' => now()->format('Y-m-d'),
                'time_of_issue' => now()->format('H:i:s'),
                'inventory_transaction_id' => $inventory_transaction_id,
                'observations' => $comments,
                'items' => $guide_items
            ]);

            if (!$res['success']) {
                throw new Exception($res['message']);
            }

            $inventory->update([
                'guide_id' => $res['data']['id']
            ]);

            $lots_enabled = isset($request->lots_enabled) ? $request->lots_enabled : false;

            if ($type == 'input') {
                foreach ($lots as $lot) {
                    /*$inventory->lots()->create([
                        'date' => $lot['date'],
                        'series' => $lot['series'],
                        'item_id' => $item_id,
                        'warehouse_id' => $warehouse_id,
                        'has_sale' => false
                    ]);*/

                    $inventory->lots()->create([
                        'date' => $lot['date'],
                        'series' => $lot['series'],
                        'item_id' => $item_id,
                        'warehouse_id' => $warehouse_id,
                        'has_sale' => false,
                        'state' => $lot['state'],
                    ]);
                }

                if ($lots_enabled) {
                    ItemLotsGroup::create([
                        'code' => $lot_code,
                        'quantity' => $quantity,
                        'date_of_due' => $request->date_of_due,
                        'item_id' => $item_id
                    ]);
                }
            } else {
                foreach ($lots as $lot) {
                    if ($lot['has_sale']) {
                        $item_lot = ItemLot::findOrFail($lot['id']);
                        // $item_lot->delete();
                        $item_lot->has_sale = true;
                        $item_lot->state = 'Inactivo';
                        $item_lot->save();
                    }
                }

                if (isset($request->IdLoteSelected)) {
                    if (is_array($request->IdLoteSelected)) {
                        foreach ($request->IdLoteSelected as $row) {
                            Log::info($row);
                            $lot = ItemLotsGroup::find($row['id']);
                            $lot->quantity = ($lot->quantity - $row['compromise_quantity']);
                            $lot->save();
                        }
                    } else {
                        $lot = ItemLotsGroup::find($request->IdLoteSelected);
                        $lot->quantity = ($lot->quantity - $quantity);
                        $lot->save();
                    }
                }

            }
            DB::connection('tenant')->commit();

            return [
                'success' => true,
                'message' => ($type == 'input') ? 'Ingreso registrado correctamente' : 'Salida registrada correctamente'
            ];
        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }
    }

    public function moveMultiples(Request $request)
    {
        $request->validate([
            'items' => 'required|array'
        ]);

        DB::connection('tenant')->beginTransaction();
        try {
            $items = $request->items;
            foreach ($items as $item) {
                $item_id = $item['item_id'];
                $warehouse_id = $item['warehouse_id'];
                $warehouse_new_id = $item['warehouse_new_id'];
                $quantity = $item['quantity'];
                $quantity_move = $item['quantity_move'];
                $detail = $item['detail'];
                if ($quantity_move <= 0) {
                    throw new Exception("La cantidad del producto {$item['item_description']} a trasladar debe ser mayor a 0", 500);
                }

                if ($warehouse_id === $warehouse_new_id) {
                    throw new Exception("El almacén destino del producto {$item['item_description']} no puede ser igual al de origen", 500);
                }
                if ($quantity < $quantity_move) {
                    throw new Exception("La cantidad a trasladar del producto {$item['item_description']} no puede ser mayor al que se tiene en el almacén.", 500);
                }

                $inventory = new Inventory();
                $inventory->type = 2;
                $inventory->description = 'Traslado';
                $inventory->item_id = $item_id;
                $inventory->warehouse_id = $warehouse_id;
                $inventory->warehouse_destination_id = $warehouse_new_id;
                $inventory->quantity = $quantity_move;
                $inventory->detail = $detail;

                $inventory->save();
            }
            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Productos trasladados con éxito'
            ], 200);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

//	public function move(Request $request)
//	{
//		$result = DB::connection('tenant')->transaction(function () use ($request) {
//			$id = $request->input('id');
//			$item_id = $request->input('item_id');
//			$warehouse_id = $request->input('warehouse_id');
//			$warehouse_new_id = $request->input('warehouse_new_id');
//			$quantity = $request->input('quantity');
//			$quantity_move = $request->input('quantity_move');
//			$lots = ($request->has('lots')) ? $request->input('lots') : [];
//			$detail = $request->input('detail');
//
//			if ($quantity_move <= 0) {
//				return  [
//					'success' => false,
//					'message' => 'La cantidad a trasladar debe ser mayor a 0'
//				];
//			}
//
//			if ($warehouse_id === $warehouse_new_id) {
//				return  [
//					'success' => false,
//					'message' => 'El almacén destino no puede ser igual al de origen'
//				];
//			}
//			if ($quantity < $quantity_move) {
//				return  [
//					'success' => false,
//					'message' => 'La cantidad a trasladar no puede ser mayor al que se tiene en el almacén.'
//				];
//			}
//
//			$inventory = new Inventory();
//			$inventory->type = 2;
//			$inventory->description = 'Traslado';
//			$inventory->item_id = $item_id;
//			$inventory->warehouse_id = $warehouse_id;
//			$inventory->warehouse_destination_id = $warehouse_new_id;
//			$inventory->quantity = $quantity_move;
//			$inventory->detail = $detail;
//
//			$inventory->save();
//
//			foreach ($lots as $lot) {
//				if ($lot['has_sale']) {
//					$item_lot = ItemLot::findOrFail($lot['id']);
//					$item_lot->warehouse_id = $inventory->warehouse_destination_id;
//					$item_lot->update();
//				}
//			}
//
//			return  [
//				'success' => true,
//				'message' => 'Producto trasladado con éxito'
//			];
//		});
//
//		return $result;
//	}

//	public function remove(Request $request)
//	{
//		$result = DB::connection('tenant')->transaction(function () use ($request) {
//			// dd($request->all());
//			$item_id = $request->input('item_id');
//			$warehouse_id = $request->input('warehouse_id');
//			$quantity = $request->input('quantity');
//			$quantity_remove = $request->input('quantity_remove');
//			$lots = ($request->has('lots')) ? $request->input('lots') : [];
//
//			//Transaction
//			$item_warehouse = ItemWarehouse::where('item_id', $item_id)
//										   ->where('warehouse_id', $warehouse_id)
//										   ->first();
//			if (!$item_warehouse) {
//				return [
//					'success' => false,
//					'message' => 'El producto no se encuentra en el almacén indicado'
//				];
//			}
//
//			if ($quantity < $quantity_remove) {
//				return  [
//					'success' => false,
//					'message' => 'La cantidad a retirar no puede ser mayor al que se tiene en el almacén.'
//				];
//			}
//
//			// $item_warehouse->stock = $quantity - $quantity_remove;
//			// $item_warehouse->save();
//
//			$inventory = new Inventory();
//			$inventory->type = 3;
//			$inventory->description = 'Retirar';
//			$inventory->item_id = $item_id;
//			$inventory->warehouse_id = $warehouse_id;
//			$inventory->quantity = $quantity_remove;
//			$inventory->save();
//
//			foreach ($lots as $lot) {
//				if ($lot['has_sale']) {
//					$item_lot = ItemLot::findOrFail($lot['id']);
//					$item_lot->delete();
//				}
//			}
//
//			return  [
//				'success' => true,
//				'message' => 'Producto trasladado con éxito'
//			];
//		});
//
//		return $result;
//	}


    public function stock(Request $request)
    {
        $result = DB::connection('tenant')->transaction(function () use ($request) {
            $id = $request->input('id');
            $item_id = $request->input('item_id');
            $warehouse_id = $request->input('warehouse_id');
            $quantity = $request->input('quantity');
            $quantity_real = $request->input('quantity_real');
            $lots = ($request->has('lots')) ? $request->input('lots') : [];

            if ($quantity_real < 0) {
                return [
                    'success' => false,
                    'message' => 'La cantidad de stock real debe ser mayor o igual a 0'
                ];
            }
            $type = 1;
            $quantity_new = 0;
            $quantity_new = $quantity_real - $quantity;
            if ($quantity_real < $quantity) {
                $quantity_new = $quantity - $quantity_real;
                $type = null;
            }

            $inventory = new Inventory();
            $inventory->type = $type;
            $inventory->description = 'STock Real';
            $inventory->item_id = $item_id;
            $inventory->warehouse_id = $warehouse_id;
            $inventory->quantity = $quantity_new;
            if ($quantity_real < $quantity) {
                $inventory->inventory_transaction_id = 28;
            }

            $inventory->real_stock = $request->quantity_real;
            $inventory->system_stock = $request->quantity;

            $inventory->save();

            return [
                'success' => true,
                'message' => 'Cantidad de stock actualizado con éxito'
            ];
        });

        return $result;
    }

    public function stockMultiples(Request $request)
    {
        $request->validate([
            'items' => 'required|array'
        ]);

        DB::connection('tenant')->beginTransaction();
        try {
            $items = $request->items;
            foreach ($items as $item) {
                $item_id = $item['item_id'];
                $warehouse_id = $item['warehouse_id'];
                $quantity = $item['quantity'];
                $quantity_real = $item['quantity_real'];

                if ($quantity_real < 0) throw new Exception("La cantidad del producto {$item['item_description']} a modificar debe ser mayor o igual a 0", 500);

                $type = 1;
                $quantity_new = 0;
                $quantity_new = $quantity_real - $quantity;
                if ($quantity_real < $quantity) {
                    $quantity_new = $quantity - $quantity_real;
                    $type = null;
                }

                $inventory = new Inventory();
                $inventory->type = $type;
                $inventory->description = 'STock Real';
                $inventory->item_id = $item_id;
                $inventory->warehouse_id = $warehouse_id;
                $inventory->quantity = $quantity_new;
                if ($quantity_real < $quantity) {
                    $inventory->inventory_transaction_id = 28;
                }

                $inventory->real_stock = $item['quantity_real'];
                $inventory->system_stock = $item['quantity'];

                $inventory->save();

            }
            DB::connection('tenant')->commit();

            return response()->json([
                'success' => true,
                'message' => 'Cantidad de stock actualizado con éxito'
            ], 200);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|numeric|min:1'
        ]);
        if ($request->hasFile('file')) {
            try {
                $import = new StockImport();
                $import->import($request->file('file'), null, Excel::XLSX);
                $data = $import->getData();
                return [
                    'success' => true,
                    'message' => __('app.actions.upload.success'),
                    'data' => $data
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        return [
            'success' => false,
            'message' => __('app.actions.upload.error'),
        ];
    }

    public function move(Request $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $id = $request->input('id');
            $item_id = $request->input('item_id');
            $warehouse_id = $request->input('warehouse_id');
            $warehouse_new_id = $request->input('warehouse_new_id');
            $quantity = $request->input('quantity');
            $quantity_move = $request->input('quantity_move');
            $lots = ($request->has('lots')) ? $request->input('lots') : [];
            $detail = $request->input('detail');

            if ($quantity_move <= 0) {
                return [
                    'success' => false,
                    'message' => 'La cantidad a trasladar debe ser mayor a 0'
                ];
            }

            if ($warehouse_id === $warehouse_new_id) {
                return [
                    'success' => false,
                    'message' => 'El almacén destino no puede ser igual al de origen'
                ];
            }
            if ($quantity < $quantity_move) {
                return [
                    'success' => false,
                    'message' => 'La cantidad a trasladar no puede ser mayor al que se tiene en el almacén.'
                ];
            }

            $document_type_id = 'U4';
            $warehouse = Warehouse::query()
                ->select('id', 'establishment_id')
                ->where('id', $warehouse_id)
                ->first();

            $series = Series::query()
                ->select('number')
                ->where('establishment_id', $warehouse->establishment_id)
                ->where('document_type_id', 'U4')
                ->first();

            if (!$series) {
                throw new Exception('No se encontraron series en el establecimiento.');
            }

            $row = InventoryTransfer::query()
                ->create([
                    'description' => $detail,
                    'warehouse_id' => $warehouse_id,
                    'warehouse_destination_id' => $warehouse_new_id,
                    'quantity' => 1,
                    'document_type_id' => $document_type_id,
                    'series' => $series->number,
                    'number' => '#',
                ]);

            if($request->lots_group) {
                foreach($request->lots_group as $lot) {
                    InventoryTransferItem::query()->create([
                        'inventory_transfer_id' => $row->id,
                        'item_lots_group_id' => $lot['id'],
                    ]);
                }
            }

            if($request->lots) {
                foreach($request->lots as $lot) {
                    InventoryTransferItem::query()->create([
                        'inventory_transfer_id' => $row->id,
                        'item_lot_id' => $lot['id'],
                    ]);
                }
            }

            $inventory = new Inventory();
            $inventory->type = 2;
            $inventory->description = 'Traslado';
            $inventory->item_id = $item_id;
            $inventory->warehouse_id = $warehouse_id;
            $inventory->warehouse_destination_id = $warehouse_new_id;
            $inventory->quantity = $quantity_move;
            $inventory->inventories_transfer_id = $row->id;
            $inventory->detail = $detail;
            $inventory->save();

            foreach ($lots as $lot) {
                if ($lot['has_sale']) {
                    $item_lot = ItemLot::findOrFail($lot['id']);
                    $item_lot->warehouse_id = $inventory->warehouse_destination_id;
                    $item_lot->update();
                }
            }

            DB::connection('tenant')->commit();
            return [
                'success' => true,
                'message' => 'Producto trasladado con éxito'
            ];
        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function remove(RemoveRequest $request)
    {
        $result = DB::connection('tenant')->transaction(function () use ($request) {
            // dd($request->all());
            $item_id = $request->input('item_id');
            $warehouse_id = $request->input('warehouse_id');
            $quantity = $request->input('quantity');
            $quantity_remove = $request->input('quantity_remove');
            $lots = ($request->has('lots')) ? $request->input('lots') : [];

            //Transaction
            $item_warehouse = ItemWarehouse::where('item_id', $item_id)
                ->where('warehouse_id', $warehouse_id)
                ->first();
            if (!$item_warehouse) {
                return [
                    'success' => false,
                    'message' => 'El producto no se encuentra en el almacén indicado'
                ];
            }

            if ($quantity < $quantity_remove) {
                return [
                    'success' => false,
                    'message' => 'La cantidad a retirar no puede ser mayor al que se tiene en el almacén.'
                ];
            }

            // $item_warehouse->stock = $quantity - $quantity_remove;
            // $item_warehouse->save();

            $inventory = new Inventory();
            $inventory->type = 3;
            $inventory->inventory_transaction_id = '12';
            $inventory->description = 'Retiro';
            $inventory->item_id = $item_id;
            $inventory->warehouse_id = $warehouse_id;
            $inventory->quantity = $quantity_remove;
            $inventory->save();

            $warehouse = Warehouse::query()->find($warehouse_id);

            $itm = Item::query()
                ->select('id', 'description')
                ->where('id', $item_id)
                ->first();

            $guide_items[] = [
                'id' => $item_id,
                'name' => $itm->description,
                'stock_add' => $quantity_remove
            ];

            $res = (new GuideController())->storeWithData([
                'establishment_id' => $warehouse->establishment_id,
                'warehouse_id' => $warehouse_id,
                'date_of_issue' => now()->format('Y-m-d'),
                'time_of_issue' => now()->format('H:i:s'),
                'inventory_transaction_id' => '12',
                'observations' => 'Retiro',
                'items' => $guide_items
            ]);

            if (!$res['success']) {
                throw new Exception($res['message']);
            }

            $inventory->update([
                'guide_id' => $res['data']['id']
            ]);

            foreach ($lots as $lot) {
                if ($lot['has_sale']) {
                    $item_lot = ItemLot::findOrFail($lot['id']);
                    $item_lot->delete();
                }
            }

            $this->removeItemLotsGroup($request);

            return [
                'success' => true,
                'message' => 'Producto trasladado con éxito'
            ];
        });

        return $result;
    }


    /**
     * Remover lotes
     *
     * @param RemoveRequest $request
     * @return void
     */
    public function removeItemLotsGroup($request)
    {
        $selected_lots_group = $request->selected_lots_group ?? null;

        if ($selected_lots_group) {
            foreach ($selected_lots_group as $lots_group) {
                $lot = $this->getItemLotsGroupById($lots_group['id']);
                $lot->quantity = $lot->quantity - $lots_group['compromise_quantity'];
                $lot->save();
            }
        }
    }


    public function initialize()
    {
        $this->initializeInventory();
    }

    public function regularize_stock()
    {
        DB::connection('tenant')->transaction(function () {
            $item_warehouses = ItemWarehouse::get();

            foreach ($item_warehouses as $it_warehouse) {
                $inv_kardex = InventoryKardex::where([['item_id', $it_warehouse->item_id], ['warehouse_id', $it_warehouse->warehouse_id]])->sum('quantity');
                $it_warehouse->stock = $inv_kardex;
                $it_warehouse->save();
            }
        });

        return [
            'success' => true,
            'message' => 'Stock regularizado'
        ];
    }
}
