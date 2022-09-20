<?php

namespace Modules\Store\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Quotation;
use App\Models\Tenant\Series;
use Illuminate\Http\Request;
use Modules\Document\Http\Resources\ItemLotCollection;
use Modules\Inventory\Models\Warehouse as ModuleWarehouse;
use Modules\Item\Models\ItemLot;

class StoreController extends Controller
{
    public function tableToDocument($table ,$table_id)
    {
        $configuration = Configuration::query()->first();
        $is_contingency = 0;

        return view('tenant.documents.form', [
            'configuration' => $configuration,
            'is_contingency' => $is_contingency,
            'table_id' => $table_id,
            'table' => $table,
        ]);
    }

    public function getRecord($table, $table_id)
    {
        $record = Quotation::query()->with('person')->find($table_id);
        $person = $record->person;

        $rec = $record->toArray();
        $document_type_id = $person->identity_document_type_id === '6'?'01':'03';

        $series = Series::query()
            ->select('number')
            ->where('establishment_id', $rec['establishment_id'])
            ->where('document_type_id', $document_type_id)
            ->first();

        foreach ($rec['items'] as &$item) {
            $item['total_plastic_bag_taxes'] = 0;
            $item['attributes'] = ($item['attributes'])?(array)$item['attributes']:[];
            $item['charges'] = ($item['charges'])?(array)$item['charges']:[];
            $item['discounts'] = ($item['discounts'])?(array)$item['discounts']:[];
        }

        $rec['document_type_id'] = $document_type_id;
        $rec['operation_type_id'] = '0101';
        $rec['number'] = '#';
        $rec['date_of_issue'] = now()->format('Y-m-d');
        $rec['fee'] = [];
        $rec['charges'] = [];
        $rec['discounts'] = [];
        $rec['payments'] = [];
        $rec['guides'] = [];
        $rec['payment_condition_id'] = '01';
        $rec['series'] = $series->number;
        $rec['ubl_version'] = '2.1';
        $rec['unique_filename'] = '';
        $rec['user_rel_suscription_plan_id'] = 0;
        $rec['was_deducted_prepayment'] = 0;
        $rec['quotation_id'] = $table_id;
        $rec['quotation_id'] = $table_id;
        $rec['additional_information'] = $rec['description'];

        return [
            'success' => true,
            'data' => $rec
        ];
    }

    public function getItems()
    {

    }

    public function getItemSeries(Request $request)
    {
        $warehouse = ModuleWarehouse::query()
            ->select('id')
            ->where('establishment_id', auth()->user()->establishment_id)
            ->first();

        $input = $request->input('input');
        $item_id = $request->input('item_id');
        $document_item_id = $request->input('document_item_id');
        $sale_note_item_id = $request->input('sale_note_item_id');

        return ItemLot::query()
            ->select('id', 'series', 'date', 'has_sale')
            ->where('series', 'like', "%$input%")
            ->where('item_id', $item_id)
            ->where('has_sale', false)
            ->where('warehouse_id', $warehouse->id)
            ->latest()
            ->get()
            ->transform(function ($row) {
                return [
                    'id'           => $row->id,
                    'series'       => $row->series,
                    'date'         => $row->date,
//                    'item_id'      => $row->item_id,
//                    'warehouse_id' => $row->warehouse_id,
                    'has_sale'     => $row->has_sale,
//                    'lot_code'     => ($row->item_loteable_type) ? $lot_code : null,
                ];
            });

//        $sale_note_item_id = $request->has('sale_note_item_id') ? $request->sale_note_item_id : null;
//
//        if ($request->document_item_id)
//        {
//            //proccess credit note
//            $document_item = DocumentItem::query()
//                ->findOrFail($request->document_item_id);
//            /** @var array $lots */
//            $lots = $document_item->item->lots;
//            $records
//                ->whereIn('id', collect($lots)->pluck('id')->toArray())
//                ->where('has_sale', true)
//                ->latest();
//
//        }
//        else if($sale_note_item_id)
//        {
//            $records = $this->getRecordsForSaleNoteItem($records, $sale_note_item_id, $request);
//        }
//        else
//        {
//
//            $records
//                ->where('item_id', $request->item_id)
//                ->where('has_sale', false)
//                ->where('warehouse_id', $warehouse->id)
//                ->latest();
//        }

//        return new ItemLotCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function getIgv(Request $request)
    {
        $establishment_id = $request->input('establishment_id');
        $date = $request->input('date');
        $date_start = config('tenant.igv_31556_start');
        $date_end = config('tenant.igv_31556_end');
        $date_percentage = config('tenant.igv_31556_percentage');
        $establishment = Establishment::query()
            ->select('id', 'has_igv_31556')
            ->find($establishment_id);
        if ($establishment->has_igv_31556) {
            if ($date >= $date_start && $date <= $date_end) {
                return $date_percentage;
            }
        }
        return 0.18;
    }
}
