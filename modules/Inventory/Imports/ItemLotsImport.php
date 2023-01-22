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


class ItemLotsImport implements ToCollection
{
    use Importable;

    protected $data;
    protected $response;

    
    /**
     * 
     * Obtener filas que tengan codigo interno
     *
     * @param  array $rows
     * @return array
     */
    private function getFilteredRows($rows)
    {
        return $rows->filter(function($row){

            $internal_id = $row[0] ?? null;

            return !is_null($internal_id) && $internal_id != '';

        });
    }

    
    /**
     * 
     * Agrupar series por codigo interno
     *
     * @param  array $filter_rows
     * @return array
     */
    private function getGroupedRows($filter_rows)
    {
        return $filter_rows->groupBy(function($row){
            return $row[0];
        });
    }

    
    /**
     *
     * @param  array $row
     * @return array
     */
    private function getLots($row)
    {
        return $row->map(function($lot){
                    return [
                        'id' => null,
                        'item_id' => null,
                        'series' => $lot[1],
                        'state' => $lot[2],
                        'date' => Date::excelToDateTimeObject($lot[3])->format('Y-m-d')
                    ];
                })
                ->toArray();
    }


    public function collection(Collection $rows)
    {
        $total = count($rows);
        $warehouse_id = request('warehouse_id');
        $registered = 0;
        $inventory_transaction_id = '102'; //Entrada por importacion masiva (xlsx)
        unset($rows[0]);

        $filter_rows = $this->getFilteredRows($rows);
        $grouped_rows = $this->getGroupedRows($filter_rows);

        
        foreach ($grouped_rows as $key => $row) 
        {
            $internal_id = $key;
            $item = Item::getItemByInternalId($internal_id);

            if($item)
            {
                if($item->series_enabled)
                {
                    $lots = $this->getLots($row);
        
                    $params = [
                        'id' => null,
                        'item_id' => $item->id,
                        'warehouse_id' => $warehouse_id,
                        'inventory_transaction_id' => $inventory_transaction_id,
                        'quantity' => count($lots),
                        'type' => 'input',
                        'lot_code' => null,
                        'lots_enabled' => false,
                        'series_enabled' => $item->series_enabled,
                        'lots' => $lots,
                        'date_of_due' => null,
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
                    $this->setResponse(false, "El producto no maneja series.");
                }
            }
            else
            {
                $this->setResponse(false, "El producto con código interno {$internal_id} no existe.");
            }

        }

        Log::info("Log de series importadas: ". json_encode($this->response));

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
