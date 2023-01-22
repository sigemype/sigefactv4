<?php

namespace Modules\Inventory\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Exception;
use Modules\Inventory\Http\Controllers\InventoryController;
use Modules\Inventory\Http\Requests\InventoryRequest;
use App\Models\Tenant\{
    Item
};
use Illuminate\Support\Facades\Log;


class ItemLotsGroupImport implements ToCollection
{
    use Importable;

    protected $data;
    protected $response;

    public function collection(Collection $rows)
    {
        $total = count($rows);
        $warehouse_id = request('warehouse_id');
        $registered = 0;
        $inventory_transaction_id = '102'; //Entrada por importacion masiva (xlsx)
        $response = [];

        unset($rows[0]);

        // dd($rows);
        foreach ($rows as $row) 
        {
            $internal_id = $row[0];
            $lot_code = $row[1];
            $stock = $row[2];
            $date_of_due = Date::excelToDateTimeObject($row[3])->format('Y-m-d');
            $item = Item::getItemByInternalId($internal_id);

            // dd($item, $internal_id, $lot_code, $stock, $date_of_due);

            if($item)
            {
                if($item->lots_enabled)
                {
                    $params = [
                        'id' => null,
                        'item_id' => $item->id,
                        'warehouse_id' => $warehouse_id,
                        'inventory_transaction_id' => $inventory_transaction_id,
                        'quantity' => $stock,
                        'type' => 'input',
                        'lot_code' => $lot_code,
                        'lots_enabled' => $item->lots_enabled,
                        'series_enabled' => false,
                        'lots' => [],
                        'date_of_due' => $date_of_due,
                        'created_at' => null,
                        'comments' => null,
                    ];
        
                    $request = new InventoryRequest($params);
                    // dd($params, $request);
        
                    $res = app(InventoryController::class)->store_transaction($request);

                    $this->setResponse($res['success'], $res['message']);

        
                    $registered += 1;
                }
                else
                {
                    $this->setResponse(false, "El producto no maneja lotes.");
                }
            }
            else
            {
                $this->setResponse(false, "El producto con código interno {$internal_id} no existe.");
            }

        }

        Log::info("Log de lotes importados: ". json_encode($this->response));

        $this->data = compact('total', 'registered');

    }

    
    /**
     *
     * @param  bool $success
     * @param  string $message
     * @return void
     */
    public function setResponse($success, $message)
    {
        $this->response [] = [
            'success' => $success,
            'message' => $message
        ];
    }
    

    public function getData()
    {
        return $this->data;
    }
}
