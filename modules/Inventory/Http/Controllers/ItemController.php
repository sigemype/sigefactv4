<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Item;
use Illuminate\Http\Request;
use Modules\Inventory\Models\{
    Warehouse,
    ItemWarehouse,
    InventoryConfiguration
};
use Modules\Inventory\Imports\{
    ItemLotsGroupImport,
    ItemLotsImport
};
use Exception;
use Maatwebsite\Excel\Excel;


class ItemController extends Controller
{

        
    /**
     * 
     * Búsqueda avanzada de items para reporte kardex
     *
     * @param  Request $request
     * @return array
     */
    public function advancedItemsSearch(Request $request)
    {

        $items = Item::whereFilterReportKardex()->latest();

        if($request->has('search_value'))
        {
            $items->whereAdvancedRecordsSearch('description', $request->search_value)
                    ->orWhere('internal_id', $request->search_value);
        }
        else
        {
            $items->take(10);
        }

        // filtrar por almacen
        if($request->has('warehouse_id')) $items->filterByWarehouseId($request->warehouse_id);

        return [
            'items' => $items->get()->transform(function ($row) {
                return $row->getRowResourceAdvancedSearch();
            })
        ];

    }

        
    /**
     * 
     * Validar stock en la venta
     *
     * @param  Request $request
     * @return array
     */
    public function validateCurrentItemStock(Request $request)
    {
        $item_id = $request->item_id;
        $quantity = $request->quantity;
        $warehouse_id = $request->warehouse_id;
        $presentation_quantity = !empty($request->presentation) ? ($request->presentation['quantity_unit'] ?? 1) : 1;

        $item = Item::whereFilterWithOutRelations()->with(['sets'])->findOrFail($item_id);
        $stock_control = InventoryConfiguration::getRecordIndividualColumn('stock_control');

        if($item->checkIsNotService() && $stock_control)
        {
            if(!$warehouse_id) $warehouse_id = Warehouse::getWarehouseId();

            if($item->checkIsSet())
            {
                foreach ($item->sets as $set) 
                {
                    $individual_item = $set->individual_item()->whereFilterWithOutRelations()->first();

                    if($individual_item->checkIsNotService())
                    {
                        $calculate_quantity = $quantity * $set->quantity;
    
                        $error_message =  "El producto {$individual_item->description} registrado en el conjunto {$item->description} no tiene suficiente stock.";

                        $check_individual_item_stock = $this->checkIndividualItemStock($individual_item, $warehouse_id, $calculate_quantity, $error_message);
    
                        if(!$check_individual_item_stock['success']) return $check_individual_item_stock;
                    }
                }
            }
            else
            {
                $calculate_quantity = $quantity * $presentation_quantity;

                $check_individual_item_stock = $this->checkIndividualItemStock($item, $warehouse_id, $calculate_quantity);

                if(!$check_individual_item_stock['success']) return $check_individual_item_stock;
            }
        }

        return $this->generalResponse(true, '');
    }

    
    /**
     * 
     * Proceso para validar stock
     *
     * @param  Item $item
     * @param  int $warehouse_id
     * @param  float $quantity
     * @param  float $presentation_quantity
     * @param  string $message_not_stock
     * @return array
     * 
     */
    public function checkIndividualItemStock($item, $warehouse_id, $calculate_quantity, $message_not_stock = null)
    {
        $item_warehouse = $this->getDataItemWarehouse($item->id, $warehouse_id);
                
        if(!$item_warehouse) return $this->generalResponse(false, 'El producto no está disponible en su almacén.');

        $error_message = $message_not_stock ?? "El producto {$item->description} no tiene suficiente stock.";

        if (($item_warehouse->stock - $calculate_quantity) < 0) return $this->generalResponse(false, $error_message);

        return $this->generalResponse(true, '');
    }
    

    /**
     *
     * @param  int $item_id
     * @param  int $warehouse_id
     * @return ItemWarehouse
     */
    public function getDataItemWarehouse($item_id, $warehouse_id)
    {
        return ItemWarehouse::getItemStockData($item_id, $warehouse_id)->first();
    }

    
    /**
     * 
     * Importar lotes
     *
     * @param  Request $request
     * @return array
     */
    public function importItemLotsGroup(Request $request)
    {
        if ($request->hasFile('file')) 
        {
            try 
            {
                $import = new ItemLotsGroupImport();
                $import->import($request->file('file'), null, Excel::XLSX);
                $data = $import->getData();
                return [
                    'success' => true,
                    'message' => __('app.actions.upload.success'),
                    'data' => $data
                ];
            }
            catch (Exception $e) 
            {
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

    
    /**
     * 
     * Importar series
     *
     * @param  Request $request
     * @return array
     */
    public function importItemLots(Request $request)
    {
        if ($request->hasFile('file')) 
        {
            try 
            {
                $import = new ItemLotsImport();
                $import->import($request->file('file'), null, Excel::XLSX);
                $data = $import->getData();
                return [
                    'success' => true,
                    'message' => __('app.actions.upload.success'),
                    'data' => $data
                ];
            }
            catch (Exception $e) 
            {
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

}
