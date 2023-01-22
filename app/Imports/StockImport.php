<?php

namespace App\Imports;

use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Item\Models\Category;
use Modules\Item\Models\Brand;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\ItemWarehouse;
use Exception;
use Modules\Item\Models\ItemLotsGroup;


class StockImport implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
            $total = count($rows);
            $warehouse_id_de = request('warehouse_id');
            $registered = 0;
            unset($rows[0]);
            foreach ($rows as $row) {
                $internal_id = ($row[0])?:null;
                $quantity_real = ($row[1]) ? : null;

                $item = Item::where('internal_id', $internal_id)
                                    ->first();

                $quantity=ItemWarehouse::where('item_id',$item->id)
                ->where('warehouse_id',$warehouse_id_de)
                ->select('stock')->get();
                $quantity=$quantity[0]->stock;
                //dd($quantity);
                $type=1;
				$quantity_new=0;
				$quantity_new=$quantity_real-$quantity;
				if ($quantity_real<$quantity) {
					$quantity_new=$quantity-$quantity_real;
					$type=null;
				}

				$inventory = new Inventory();
				$inventory->type = $type;
				$inventory->description = 'STock Real';
				$inventory->item_id = $item->id;
				$inventory->warehouse_id = $warehouse_id_de;
				$inventory->quantity = $quantity_new;
				if ($quantity_real<$quantity) {
					$inventory->inventory_transaction_id = 28;
				}
				$inventory->save();

                $registered += 1;
            }
            $this->data = compact('total', 'registered', 'warehouse_id_de');

    }

    public function getData()
    {
        return $this->data;
    }
}
