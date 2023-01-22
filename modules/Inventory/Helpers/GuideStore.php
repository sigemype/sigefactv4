<?php

namespace Modules\Inventory\Helpers;

use App\Models\Tenant\Series;
use Modules\Inventory\Entities\Guide\GuideEntity;
use Modules\Inventory\Entities\Guide\GuideItemEntity;
use Modules\Inventory\Models\Guide;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Models\Warehouse;
use Modules\Store\Helpers\DocumentData;
use Modules\Template\Formats\InternalFormat;
use Exception;


class GuideStore
{
    protected $record_data;

    public function save($data)
    {
        $inventory_transaction = InventoryTransaction::query()->find($data['inventory_transaction_id']);

        if($inventory_transaction->type === 'input') {
            $data['document_type_id'] = 'U2';
        }
        if($inventory_transaction->type === 'output') {
            $data['document_type_id'] = 'U3';
        }
        //'U2': 'Guía de Ingreso Almacén'
        //'U3': 'Guía de Salida Almacén'
        //'U3': 'Guía de Transferencia Almacén'

        $series = Series::query()
            ->where('establishment_id', $data['establishment_id'])
            ->where('document_type_id', $data['document_type_id'])
            ->first();

        if(is_null($series)) throw new Exception("No se encontró una serie para el tipo de documento {$data['document_type_id']}, registre la serie en Establecimientos/Series");

        $data['series'] = $series->number;
        $data['number'] = '#';

        $record = Guide::query()->create($data);

        foreach ($data['items'] as $row) {
//            if($data['type'] === 'output') {
//                $item = Item::query()->select('unit_cost')->where('id', $row['id'])->first();
//                $row['unit_cost'] = $item->unit_cost;
//            }

            $row['quantity'] = floatval($row['stock_add']);
            $row['unit_cost'] = 0; //floatval($row['unit_cost']);
            $row['total'] = 0; //$row['quantity'] * $row['unit_cost'];
            $row['item_id'] = $row['id'];
            $row['item_name'] = $row['name'];
            $record->items()->create($row);

//            if($data['type'] === 'input') {
//                Item::query()->where('id', $row['id'])->update([
//                    'unit_cost' => $row['unit_cost']
//                ]);
//            }
        }

        return $record;
    }

    public function createPdf($format = 'a4')
    {
        return (new InternalFormat())->create('guide', $this->record_data, $format);
    }

    public function setData($id)
    {
        $record = Guide::query()
            ->with(['user', 'warehouse', 'document_type', 'inventory_transaction',  'items', 'items.item'])
            ->find($id);

        $warehouse = Warehouse::query()
            ->with('establishment')
            ->find($record->warehouse_id);

        $data = new GuideEntity();
        $data = (new DocumentData())->setDocumentCommon($record, $data);
        $data->company = (new DocumentData())->setCompany();
        $data->establishment = (new DocumentData())->setEstablishment($warehouse->establishment);
        $data->inventory_transaction_name = $record->inventory_transaction->name;
        $data->observations = $record->observations;
        $data->user_name = $record->user->name;

        $items = [];
        foreach ($record->items as $item) {
            $guide_item = new GuideItemEntity();
            $guide_item->internal_id = $item->item->internal_id;
            $guide_item->name = $item->item_name;
            $guide_item->quantity = $item->quantity;
            $guide_item->unit_cost = $item->unit_cost;
            $guide_item->total = $item->total;
            $items[] = $guide_item;
        }
        $data->items = $items;

        $this->record_data = $data;
    }
}
