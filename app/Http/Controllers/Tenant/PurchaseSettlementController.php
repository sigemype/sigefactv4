<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\PurchaseSettlementCollection;
use App\Models\Tenant\PurchaseSettlement;
use App\Models\Tenant\PurchaseSettlementPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\CoreFacturalo\Facturalo;
use App\CoreFacturalo\Helpers\Number\NumberLetter;

use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Catalogs\ChargeDiscountType;
use App\Models\Tenant\GeneralPaymentCondition;

use App\Models\Tenant\User;

use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\GuideFile;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemUnitType;
use App\Models\Tenant\ItemWarehouse;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Person;

use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\CoreFacturalo\Requests\Inputs\Common\OperationDataInput;
use App\CoreFacturalo\Requests\Inputs\Common\LegendInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Requests\Inputs\Functions;
use Illuminate\Support\Str;
use Throwable;
use Modules\Finance\Traits\FinanceTrait;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Traits\OfflineTrait;

class PurchaseSettlementController extends Controller
{
    use FinanceTrait;
    use StorageDocument;
    use OfflineTrait;

    public function index()
    {
        return view('tenant.purchase-settlements.index');
    }

    public function create($order_id = null)
    {

        return view('tenant.purchase-settlements.form', compact('order_id'));
    }

    public function columns()
    {
        return [
            'number' => 'Número',
            'date_of_issue' => 'Fecha de emisión'
        ];
    }

    public function records(Request $request)
    {
        $records = PurchaseSettlement::where($request->column, 'like', "%{$request->value}%")
                            ->where('user_id', auth()->id())
                            ->latest();

        return new PurchaseSettlementCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function store(Request $request)
    {
        $data = self::convert($request);
        // dd($request->all());
        try {
        $fact = DB::connection('tenant')->transaction(function () use($data) {
            $facturalo = new Facturalo();
            $facturalo->save($data);
            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->updateHash();
            $facturalo->createPdf();
            $facturalo->senderXmlSignedBill();

            return $facturalo;
        });

        $document = $fact->getDocument();

        foreach ($data['payments'] as $payment) {

            $record_payment = new PurchaseSettlementPayment;
            $record_payment->purchase_settlement_id=$document->id;
            $record_payment->date_of_payment=$payment['date_of_payment'];
            $record_payment->payment_method_type_id=$payment['payment_method_type_id'];
            $record_payment->reference=$payment['reference'];
            $record_payment->payment=$payment['payment'];
            $record_payment->save();
            $this->createGlobalPayment($record_payment, $payment);
        }

        $response = $fact->getResponse();

        return [
            'success' => true,
            'data' => [
                'id' => $document->id,
                'number_full' => "{$document->series}-{$document->number}",
            ],
        ];
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public static function convert($inputs)
    {
        $operation_data = $inputs['operation_data'];
        $establishment=Establishment::find($inputs['establishment_id']);
        /* dd($establishment); */
        $operation_data['address']= $establishment->address;
        $operation_data['address_type_id'] = '02';
        $operation_data['country_id'] = $establishment->country_id;
        $operation_data['district_id'] = $establishment->district_id;
        $legends[] = [
            'code' => 1000,
            'value' => NumberLetter::convertToLetter($inputs['total'])
        ];
        $actions=[
            'send_email'=>true,
            'send_xml_signed'=>true,
            'format_pdf'=>'a4',
        ];
        $suplier_info = PersonInput::set($inputs['supplier_id']);
        $suplier_info['address_type_id'] = '01';
        //dd($suplier_info);
        $last_number = PurchaseSettlement::getLastNumberBySerie($inputs['series']);
        // se actualiza el numero actual en $imputs
        $number_new= $last_number + 1;
        $type='purchase_settlement';
        $filename= Functions::filename(Company::active(), $inputs['document_type_id'], $inputs['series'], $number_new);
        $company = Company::active();
        $values = [
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'supplier' => $suplier_info,
            'soap_type_id' => $company->soap_type_id,
            'state_type_id' => '01',
            'establishment'=>EstablishmentInput::set($inputs['establishment_id']),
            'ubl_version' => '2.1',
            'operation_type_id' => '0501',
            'operation_data' => OperationDataInput::set($operation_data),
            'legends' => $legends,
            'filename' => $filename,
            'actions' => $actions,
            'type' => $type,
            'number' => $number_new
        ];

        $inputs->merge($values);

        return $inputs->all();
    }

    public function tables()
    {
        $user = new User();
        if(\Auth::user()){
            $user = \Auth::user();
        }
        $document_id =  $user->document_id;
        $series_id =  $user->series_id;
        $establishment_id =  $user->establishment_id;
        $userId =  $user->id;
        $userType = $user->type;
        $series = $user->getSeries();

        $suppliers = $this->table('suppliers');
        $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
        $currency_types = CurrencyType::whereActive()->get();
        $document_types_invoice = DocumentType::DocumentsActiveToSettlement()->get();
        $discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();
        $charge_types = ChargeDiscountType::whereType('charge')->whereLevel('item')->get();
        $company = Company::active();
        $payment_method_types = PaymentMethodType::getPaymentMethodTypes();
        // $payment_method_types = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();
        $customers = $this->getPersons('customers');
        $configuration = Configuration::first();
        $payment_conditions = GeneralPaymentCondition::where('id','01')->get();

        return compact('suppliers', 'establishment', 'currency_types', 'discount_types', 'configuration', 'payment_conditions',
            'charge_types', 'document_types_invoice', 'company', 'payment_method_types', 'payment_destinations', 'customers', 'series_id', 'series','document_id');
    }

    public function table($table)
    {
        switch ($table) {
            case 'suppliers':

                $suppliers = Person::whereType('suppliers')->whereIn('identity_document_type_id',[1,4,7])->orderBy('name')->get()->transform(function ($row) {
                    return [
                        'id' => $row->id,
                        'description' => $row->number . ' - ' . $row->name,
                        'name' => $row->name,
                        'number' => $row->number,
                        'perception_agent' => (bool)$row->perception_agent,
                        'identity_document_type_id' => $row->identity_document_type_id,
                        'identity_document_type_code' => $row->identity_document_type->code
                    ];
                });
                return $suppliers;

                break;

            case 'items':
                return SearchItemController::getItemToPurchase();
                return SearchItemController::getItemToPurchase()->transform(function ($row) {
                    /*
                                        $items = Item::whereNotIsSet()->whereIsActive()->orderBy('description')->take(20)->get(); //whereWarehouse()
                                    return collect($items)->transform(function($row) {
                                        */
                    /** @var Item $row */
                    $full_description = ($row->internal_id) ? $row->internal_id . ' - ' . $row->description : $row->description;
                    return [
                        'id' => $row->id,
                        'item_code' => $row->item_code,
                        'full_description' => $full_description,
                        'description' => $row->description,
                        'currency_type_id' => $row->currency_type_id,
                        'currency_type_symbol' => $row->currency_type->symbol,
                        'sale_unit_price' => $row->sale_unit_price,
                        'purchase_unit_price' => $row->purchase_unit_price,
                        'unit_type_id' => $row->unit_type_id,
                        'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                        'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                        'purchase_has_igv' => (bool)$row->purchase_has_igv,
                        'has_perception' => (bool)$row->has_perception,
                        'lots_enabled' => (bool)$row->lots_enabled,
                        'percentage_perception' => $row->percentage_perception,
                        'item_unit_types' => collect($row->item_unit_types)->transform(function ($row) {
                            return [
                                'id' => $row->id,
                                'description' => "{$row->description}",
                                'item_id' => $row->item_id,
                                'unit_type_id' => $row->unit_type_id,
                                'quantity_unit' => $row->quantity_unit,
                                'price1' => $row->price1,
                                'price2' => $row->price2,
                                'price3' => $row->price3,
                                'price_default' => $row->price_default,
                            ];
                        }),
                        'series_enabled' => (bool)$row->series_enabled,

                        // 'warehouses' => collect($row->warehouses)->transform(function($row) {
                        //     return [
                        //         'warehouse_id' => $row->warehouse->id,
                        //         'warehouse_description' => $row->warehouse->description,
                        //         'stock' => $row->stock,
                        //     ];
                        // })
                    ];
                });
//                return $items;

                break;
            default:

                return [];

                break;
        }
    }

    public function getPersons($type)
    {

        $persons = Person::whereType($type)->orderBy('name')->take(20)->get()->transform(function ($row) {
            return [
                'id' => $row->id,
                'description' => $row->number . ' - ' . $row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
            ];
        });

        return $persons;

    }

    public function record($id)
    {

        $record = PurchaseSettlement::findOrFail($id);

        return [
            'id' => $record->id,
            'number' => $record->number,
            'external_id' => $record->external_id,
        ];
    }

}