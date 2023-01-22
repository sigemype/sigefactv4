<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\User;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Inventory\Exports\KardexExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use App\Models\Tenant\Kardex;
use App\Models\Tenant\Item;
use Carbon\Carbon;
use Modules\Inventory\Models\Guide;
use Modules\Inventory\Models\InventoryKardex;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Http\Resources\ReportKardexCollection;
use Modules\Inventory\Http\Resources\ReportKardexLotsCollection;

use Modules\Inventory\Models\ItemWarehouse;
use Modules\Item\Models\ItemLotsGroup;
use Modules\Item\Models\ItemLot;

use Modules\Inventory\Http\Resources\ReportKardexLotsGroupCollection;
use Modules\Inventory\Http\Resources\ReportKardexItemLotCollection;
use Modules\Inventory\Models\Devolution;
use App\Models\Tenant\Dispatch;


class ReportKardexController extends Controller
{
    protected $models = [
        "App\Models\Tenant\Document",
        "App\Models\Tenant\Purchase",
        "App\Models\Tenant\PurchaseSettlement",
        "App\Models\Tenant\SaleNote",
        "Modules\Inventory\Models\Inventory",
        "Modules\Order\Models\OrderNote",
        Devolution::class,
        Dispatch::class
    ];

    public function index()
    {
        return view('inventory::reports.kardex.index');
    }


    public function filter()
    {
        $warehouses = [];
        $user = User::query()->find(auth()->id());
        if ($user->type === 'admin') {
            $warehouses[] = [
                'id' => 'all',
                'name' => 'Todos'
            ];
            $records = Warehouse::query()
                ->get();
        } else {
            $records = Warehouse::query()
                ->where('establishment_id', $user->establishment_id)
                ->get();
        }

        foreach ($records as $record) {
            $warehouses[] = [
                'id' => $record->id,
                'name' => $record->description,
            ];
        }

        return [
            'warehouses' => $warehouses
        ];
    }

    public function filterByWarehouse($warehouse_id)
    {
        $query = Item::query()->whereNotIsSet()
            ->with('warehouses')
            ->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ']]);

        if ($warehouse_id !== 'all') {
            $query->whereHas('warehouses', function ($query) use ($warehouse_id) {
                return $query->where('warehouse_id', $warehouse_id);
            });
        }

        $items = $query->latest()
            ->get()
            ->transform(function ($row) {
                $full_description = $this->getFullDescription($row);
                return [
                    'id' => $row->id,
                    'full_description' => $full_description,
                    'internal_id' => $row->internal_id,
                    'description' => $row->description,
                    'warehouses' => $row->warehouses
                ];
            });

        return [
            'items' => $items
        ];
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request->all());

        return new ReportKardexCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function records_lots()
    {
        $records = ItemWarehouse::with(['item'])->whereHas('item', function ($q) {
            $q->where([['item_type_id', '01'], ['unit_type_id', '!=', 'ZZ'], ['lot_code', '!=', null]]);
            $q->whereNotIsSet();
        });

        return new ReportKardexLotsCollection($records->paginate(config('tenant.items_per_page')));

    }


    /**
     * @param $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|InventoryKardex
     */
    public function getRecords($request)
    {
        $warehouse_id = $request['warehouse_id'];
        $item_id = $request['item_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];

        $records = $this->data($item_id, $warehouse_id, $date_start, $date_end);

        return $records;

    }


    /**
     * @param $item_id
     * @param $date_start
     * @param $date_end
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|InventoryKardex
     */
    private function data($item_id, $warehouse_id, $date_start, $date_end)
    {
        //$warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

        $data = InventoryKardex::with(['inventory_kardexable']);
        if($warehouse_id !== 'all') {
            $data->where('warehouse_id', $warehouse_id);
        }
        if ($date_start) {
            $data->where('date_of_issue', '>=', $date_start);
        }
        if ($date_end) {
            $data->where('date_of_issue', '<=', $date_end);
        }
        if ($item_id) {
            $data->where('item_id', $item_id);
        }


        // if($date_start && $date_end){

        //     $data = InventoryKardex::with(['inventory_kardexable'])
        //                 ->where([['item_id', $item_id],['warehouse_id', $warehouse->id]])
        //                 ->whereBetween('date_of_issue', [$date_start, $date_end])
        //                 ->orderBy('id');

        // }else{

        //     $data = InventoryKardex::with(['inventory_kardexable'])
        //                 ->where([['item_id', $item_id],['warehouse_id', $warehouse->id]])
        //                 ->orderBy('id');
        // }

        $data
            ->orderBy('item_id')
            ->orderBy('id');
        return $data;

    }


    public function getFullDescription($row)
    {
        $desc = ($row->internal_id) ? $row->internal_id . ' - ' . $row->description : $row->description;
        $category = ($row->category) ? " - {$row->category->name}" : "";
        $brand = ($row->brand) ? " - {$row->brand->name}" : "";

        $desc = "{$desc} {$category} {$brand}";

        return $desc;
    }


    private function getData($request)
    {
        $company = Company::query()->first();
        $establishment = Establishment::query()->find(auth()->user()->establishment_id);
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');
        $item_id = $request->input('item_id');
        $item = Item::query()->findOrFail($request->input('item_id'));

        $warehouse = Warehouse::query()
            ->where('establishment_id', $establishment->id)
            ->first();

        $query = InventoryKardex::query()
            ->with(['inventory_kardexable'])
            ->where('warehouse_id', $warehouse->id);

        if ($date_start && $date_end) {
            $query->whereBetween('date_of_issue', [$date_start, $date_end])
                ->orderBy('item_id')->orderBy('id');
        }

        if ($item_id) {
            $query->where('item_id', $item_id);
        }

        $records = $query->orderBy('item_id')
            ->orderBy('id')
            ->get();

        return [
            'company' => $company,
            'establishment' => $establishment,
            'warehouse' => $warehouse,
            'item_id' => $item_id,
            'item' => $item,
            'models' => $this->models,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'records' => $records,
            'balance' => 0,
        ];
    }

    /**
     * PDF
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {
        $data = $this->getData($request);

        $pdf = PDF::loadView('inventory::reports.kardex.report_pdf', $data);
        $filename = 'Reporte_Kardex' . date('YmdHis');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Excel
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function excel(Request $request)
    {
        $data = $this->getData($request);
        $kardexExport = new KardexExport();
        $kardexExport
            ->balance($data['balance'])
            ->item_id($data['item_id'])
            ->records($data['records'])
            ->models($data['models'])
            ->company($data['company'])
            ->establishment($data['establishment'])
            ->item($data['item']);

        return $kardexExport->download('ReporteKar' . Carbon::now() . '.xlsx');
    }

    public function getRecords2($request)
    {

        $item_id = $request['item_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];

        $records = $this->data2($item_id, $date_start, $date_end);

        return $records;
    }

    private function data2($item_id, $date_start, $date_end)
    {

        // $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

        if ($date_start && $date_end) {

            $data = ItemLotsGroup::whereBetween('date_of_due', [$date_start, $date_end])
                ->orderBy('item_id')->orderBy('id');

        } else {

            $data = ItemLotsGroup::orderBy('item_id')->orderBy('id');
        }

        if ($item_id) {
            $data = $data->where('item_id', $item_id);
        }


        return $data;

    }

    public function records_lots_kardex(Request $request)
    {
        $records = $this->getRecords2($request->all());

        return new ReportKardexLotsGroupCollection($records->paginate(config('tenant.items_per_page')));


    }


    public function getRecords3($request)
    {

        $item_id = $request['item_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];

        $records = $this->data3($item_id, $date_start, $date_end);

        return $records;

    }


    private function data3($item_id, $date_start, $date_end)
    {

        // $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

        if ($date_start && $date_end) {

            $data = ItemLot::whereBetween('date', [$date_start, $date_end])
                ->orderBy('item_id')->orderBy('id');

        } else {

            $data = ItemLot::orderBy('item_id')->orderBy('id');
        }

        if ($item_id) {
            $data = $data->where('item_id', $item_id);
        }


        return $data;

    }

    public function records_series_kardex(Request $request)
    {

        $records = $this->getRecords3($request->all());

        return new ReportKardexItemLotCollection($records->paginate(config('tenant.items_per_page')));

        /*$records = [];

        if($item)
        {
            $records  =  ItemLot::where('item_id', $item)->get();

        }
        else{
            $records  = ItemLot::all();
        }

       // $records  =  ItemLot::all();
        return new ReportKardexItemLotCollection($records);*/

    }




    // public function search(Request $request) {
    //     //return $request->item_selected;
    //     $balance = 0;
    //     $d = $request->d;
    //     $a = $request->a;
    //     $item_selected = $request->item_selected;

    //     $items = Item::query()->whereNotIsSet()
    //         ->where([['item_type_id', '01'], ['unit_type_id', '!=','ZZ']])
    //         ->latest()
    //         ->get();

    //     $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

    //     if($d && $a){

    //         $reports = InventoryKardex::with(['inventory_kardexable'])
    //                     ->where([['item_id', $request->item_selected],['warehouse_id', $warehouse->id]])
    //                     ->whereBetween('date_of_issue', [$d, $a])
    //                     ->orderBy('id')
    //                     ->paginate(config('tenant.items_per_page'));

    //     }else{

    //         $reports = InventoryKardex::with(['inventory_kardexable'])
    //                     ->where([['item_id', $request->item_selected],['warehouse_id', $warehouse->id]])
    //                     ->orderBy('id')
    //                     ->paginate(config('tenant.items_per_page'));

    //     }

    //     //return json_encode($reports);

    //     $models = $this->models;

    //     return view('inventory::reports.kardex.index', compact('items', 'reports', 'balance','models', 'a', 'd','item_selected'));
    // }
    public function getPdfGuide($guide_id)
    {
        $company = Company::query()->first();

        $record = Guide::query()
            ->with('inventory_transaction', 'warehouse', 'document_type', 'items', 'items.item')
            ->find($guide_id);

        $items = [];
        foreach ($record->items as $i) {
            $items[] = [
                'item_internal_id' => $i->item->internal_id,
                'item_name' => $i->item_name,
                'unit_type_id' => $i->item->unit_type_id,
                'quantity' => $i->quantity,
                'lot' => ''
            ];
        }

        $data = [
            'company_number' => $company->number,
            'document_type_name' => $record->document_type->description,
            'document_number' => $record->series . '-' . $record->number,
            'document_date_of_issue' => $record->date_of_issue->format('d/m/Y'),
            'warehouse_name' => $record->warehouse->description,
            'transaction_name' => $record->inventory_transaction->name,
            'items' => $items
        ];

        $pdf = PDF::loadView('inventory::reports.kardex.guide', $data);
        $pdf->setPaper('A4', 'portrait');
        // $pdf->setPaper('A4', 'landscape');
        $filename = 'Guia_' . date('YmdHis');

        return $pdf->download($filename . '.pdf');
    }
}
