<?php

namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Tenant\EmailController;
use Exception;
use Carbon\Carbon;
use App\Models\Tenant\Item;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use App\Models\Tenant\Company;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Document;
use App\Mail\Tenant\DocumentEmail;
use Modules\Order\Mail\DispatchEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Tenant\PersonRequest;
use Modules\Item\Http\Requests\ItemRequest;
use Modules\Dashboard\Helpers\DashboardData;
use Modules\Finance\Helpers\UploadFileHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Item\Http\Requests\ItemUpdateRequest;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Warehouse;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Finance\Traits\FinanceTrait;
use App\Http\Resources\Tenant\UserResource;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Cash;
use App\Models\Tenant\CashDocument;
use App\Http\Requests\Tenant\CashRequest;
use App\Http\Resources\Tenant\CashCollection;
use App\Http\Resources\Tenant\CashResource;
use Mpdf\Mpdf;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\SaleNoteItem;
use App\Models\Tenant\PurchaseItem;
use Modules\Pos\Mail\CashEmail;
use App\Models\Tenant\StateType;
use App\Http\Resources\Tenant\DocumentCollection;
use App\CoreFacturalo\Services\IntegratedQuery\{
    AuthApi,
    ValidateCpe,
};
use Modules\Item\Http\Requests\CategoryRequest;
use Modules\Item\Models\Category;
use Modules\Dashboard\Traits\TotalsTrait;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNotePayment;
use Modules\Order\Models\OrderNote;
use Modules\Order\Http\Resources\OrderNoteCollection;
use Modules\Order\Http\Resources\OrderNoteResource;
use App\Models\Tenant\Catalogs\UnitType;
use Modules\Item\Models\ItemLotsGroup;
use App\Models\Tenant\Summary;
use App\Models\Tenant\SummaryDocument;
use App\Models\Tenant\Catalogs\{
    Department,
    Province,
    District
};
use GuzzleHttp\Client;
use App\Models\Tenant\Dispatch;
use App\Http\Resources\Tenant\DispatchCollection;
use Modules\Account\Exports\ReportFormatPurchaseExport;
use Modules\Account\Exports\ReportFormatSaleGarageGllExport;
use Modules\Account\Exports\ReportFormatSaleExport;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use Modules\Dispatch\Models\Dispatcher;
use Modules\Dispatch\Models\Transport;
use Modules\Dispatch\Models\Driver;
use App\Models\Tenant\Catalogs\TransferReasonType;
use App\Models\Tenant\Catalogs\TransportModeType;
use Modules\ApiPeruDev\Http\Controllers\ServiceDispatchController;
use App\CoreFacturalo\Helpers\QrCode\QrCodeGenerate;
use Modules\Dispatch\Http\Controllers\DispatcherController;
use Modules\Dispatch\Models\OriginAddress;

class AppController extends Controller
{

    use  FinanceTrait;
    use TotalsTrait;

    protected $document_state = [
        '-' => '-',
        '0' => 'NO EXISTE',
        '1' => 'ACEPTADO',
        '2' => 'ANULADO',
        '3' => 'AUTORIZADO',
        '4' => 'NO AUTORIZADO'
    ];
 
    protected $company_state = [
        '-' => '-',
        '00' => 'ACTIVO',
        '01' => 'BAJA PROVISIONAL',
        '02' => 'BAJA PROV. POR OFICIO',
        '03' => 'SUSPENSION TEMPORAL',
        '10' => 'BAJA DEFINITIVA',
        '11' => 'BAJA DE OFICIO',
        '12' => 'BAJA MULT.INSCR. Y OTROS ',
        '20' => 'NUM. INTERNO IDENTIF.',
        '21' => 'OTROS OBLIGADOS',
        '22' => 'INHABILITADO-VENT.UNICA',
        '30' => 'ANULACION - ERROR SUNAT   '
    ];

    protected $company_condition = [
        '-' => '-',
        '00' => 'HABIDO',
        '01' => 'NO HALLADO SE MUDO DE DOMICILIO',
        '02' => 'NO HALLADO FALLECIO',
        '03' => 'NO HALLADO NO EXISTE DOMICILIO',
        '04' => 'NO HALLADO CERRADO',
        '05' => 'NO HALLADO NRO.PUERTA NO EXISTE',
        '06' => 'NO HALLADO DESTINATARIO DESCONOCIDO',
        '07' => 'NO HALLADO RECHAZADO',
        '08' => 'NO HALLADO OTROS MOTIVOS',
        '09' => 'PENDIENTE',
        '10' => 'NO APLICABLE',
        '11' => 'POR VERIFICAR',
        '12' => 'NO HABIDO',
        '20' => 'NO HALLADO',
        '21' => 'NO EXISTE LA DIRECCION DECLARADA',
        '22' => 'DOMICILIO CERRADO',
        '23' => 'NEGATIVA RECEPCION X PERSONA CAPAZ',
        '24' => 'AUSENCIA DE PERSONA CAPAZ',
        '25' => 'NO APLICABLE X TRAMITE DE REVERSION',
        '40' => 'DEVUELTO'
    ];

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'No Autorizado'
            ];
        }

        $company = Company::active();
        $user = $request->user();
        $establishment = Establishment::where('id', '=', $user->establishment_id)->first();
        $configurations = Configuration::where('id', '=', 1)->first();
// return $configurations; 
        $permisos = new UserResource(User::findOrFail($user->id));

        $url_logo = "".config('tenant.app_url_base')."".$establishment->logo != null ? $establishment->logo : $company->logo."";

        if($url_logo){
            $image = file_get_contents($url_logo);
            if ($image !== false){
                $logobase64 = 'data:image/jpg;base64,'.base64_encode($image);
            }
        }else{
            $logobase64 = null;
        }

        return [
            'success' => true,
            'user_id' => $user->id,
            'name' => $user->name,
            'company_name' => $company->name,
            'company_trade' => $company->trade_name,
            'address' => $establishment->address,
            'ubigeo' => "".$establishment->district->description.", ".$establishment->province->description." - ".$establishment->department->description."",
            'telephone' => $establishment->telephone,
            'web_address' => $establishment->web_address,
            'email' => $user->email,
            'correo' => $establishment->email,
            'aditional_information' => $establishment->aditional_information != null ?  $establishment->aditional_information : null,
            'logobase64' => $logobase64,
            'customerdefault' => $establishment->customer_id,
            'seriedefault' => $user->series_id,
            'token' => $user->api_token,
            'restaurant_role_id' => $user->restaurant_role_id,
            'ruc' => $company->number,
            'logo' => $establishment->logo != null ? $establishment->logo : $company->logo,
            'levels' => $permisos->levels,
            'modules' => $permisos->modules,
            'edit_price' => $configurations->allow_edit_unit_price_to_seller,
            'terms_condition_sale' => $configurations->terms_condition_sale,
            'edit_name_product' => $configurations->edit_name_product,
            'affectation_igv' => $configurations->affectation_igv_type_id,
            'usertype' => $user->type
        ];

    }

    public function sellers()
    {
        $sellers = User::where('establishment_id',auth()->user()->establishment_id)->whereIn('type', ['seller'])->orWhere('id', auth()->user()->id)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'establishment_id' => $row->establishment_id
            ];
        });

        return [
            'success' => true,
            'data' => array('sellers' => $sellers)
        ];

    }

    public function customers()
    {
        $customers = Person::whereType('customers')->whereIsEnabled()->orderBy('name')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'identity_document_type_code' => $row->identity_document_type->code,
                'address' => $row->address,
                'telephone' => $row->telephone,
                'country_id' => $row->country_id,
                'district_id' => $row->district_id,
                'email' => $row->email,
                'addresses' => $row->addresses,
                'selected' => false
            ];
        });

        return [
            'success' => true,
            'data' => array('customers' => $customers)
        ];

    }

    public function customersAdmin()
    {
        $customers = Person::whereType('customers')->orderBy('name')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'identity_document_type_code' => $row->identity_document_type->code,
                'address' => $row->address,
                'telephone' => $row->telephone,
                'country_id' => $row->country_id,
                'district_id' => $row->district_id,
                'email' => $row->email,
                'enabled' => $row->enabled,
                'addresses' => $row->addresses,
                'selected' => false
            ];
        });

        return [
            'success' => true,
            'data' => array('customers' => $customers)
        ];

    }


    public function CustomersSearch(Request $request)
    {

        $identity_document_type_id = $this->getIdentityDocumentTypeId($request->document_type_id);

        $customers = Person::where('name', 'like', "%{$request->input}%" )
                            ->orWhere('number','like', "%{$request->input}%")
                            ->orWhere('internal_code','like', "%{$request->input}%")
                            ->orWhere('barcode','like', "%{$request->input}%")
                            ->whereType('customers')
                            ->whereIn('identity_document_type_id', $identity_document_type_id)
                            ->orderBy('name')
                            ->whereIsEnabled()
                            ->get()
                            ->take(10)
                            ->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->number.' - '.$row->name,
                                    'name' => $row->name,
                                    'number' => $row->number,
                                    'identity_document_type_id' => $row->identity_document_type_id,
                                    'identity_document_type_code' => $row->identity_document_type->code,
                                    'address' => $row->address,
                                    'telephone' => $row->telephone,
                                    'email' => $row->email,
                                    'enabled' => $row->enabled,
                                    'country_id' => $row->country_id,
                                    'district_id' => $row->district_id,
                                    'addresses' => $row->addresses,
                                    'selected' => false
                                ];
                            });

        return [
            'success' => true,
            'data' => array('customers' => $customers)
        ];
    }

    public function CustomerEnable($type, $id)
    {

        $person = Person::findOrFail($id);
        $person->enabled = $type;
        $person->save();

        $type_message = ($type) ? 'habilitado':'inhabilitado';

        return [
            'success' => true,
            'message' => "Cliente {$type_message} con éxito"
        ];

    }

    public function destroy_customer($id)
    {
        try {

            $person = Person::findOrFail($id);
            $person_type = ($person->type == 'customers') ? 'Cliente':'Proveedor';
            $person->delete();

            return [
                'success' => true,
                'message' => $person_type.' eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => "El {$person_type} esta siendo usado por otros registros, no puede eliminar"] : ['success' => false,'message' => "Error inesperado, no se pudo eliminar el {$person_type}"];

        }

    }
       
    public function searchCustomers(Request $request)
    {

        $identity_document_type_id = $this->getIdentityDocumentTypeId($request->document_type_id);

        $customers = Person::where('name', 'like', "%{$request->input}%" )
                            ->orWhere('number','like', "%{$request->input}%")
                            ->orWhere('internal_code','like', "%{$request->input}%")
                            ->orWhere('barcode','like', "%{$request->input}%")
                            ->whereType('customers')
                            ->whereIn('identity_document_type_id', $identity_document_type_id)
                            ->orderBy('name')
                            ->whereIsEnabled()
                            ->get()
                            ->take(20)
                            ->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->number.' - '.$row->name,
                                    'name' => $row->name,
                                    'number' => $row->number,
                                    'identity_document_type_id' => $row->identity_document_type_id,
                                    'identity_document_type_code' => $row->identity_document_type->code,
                                    'address' => $row->address,
                                    'telephone' => $row->telephone,
                                    'email' => $row->email,
                                    'enabled' => $row->enabled,
                                    'country_id' => $row->country_id,
                                    'district_id' => $row->district_id,
                                    'addresses' => $row->addresses,
                                    'selected' => false
                                ];
                            });

        return [
            'success' => true,
            'data' => array('customers' => $customers)
        ];
    }

    public function searchDispatcher(Request $request)
    {
        if ($request->byid!='true') {
            $dispatchers = Dispatcher::where('name', 'like', "%{$request->input}%" )
                                ->orWhere('number','like', "%{$request->input}%")
                                ->orWhere('number_mtc','like', "%{$request->input}%")
                                ->orderBy('id')
                                ->get()
                                ->take(10)
                                ->transform(function($row) {
                                    return [
                                        'id' => $row->id,
                                        'identity_document_type_id' => $row->identity_document_type_id,
                                        'number' => $row->number,
                                        'name' => $row->name,
                                        'address' => $row->address,
                                        'number_mtc' => $row->number_mtc,
                                    ];
                                });
            return [
                'success' => true,
                'data' => array('dispatchers' => $dispatchers)
            ];
        }else{
            $dispatchers = Dispatcher::findOrFail($request->input);
            return [
                'success' => true,
                'data' => $dispatchers
            ];
        }
    }

    public function storeDispatcher(Request $request)
    {

        $id = $request->input('id');
        $is_default = $request->input('is_default');
        if($is_default) {
            Dispatcher::query()->update([
                'is_default' => false
            ]);
        }

        $record = Dispatcher::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Transportista editado con éxito':'Transportista registrado con éxito',
            'id' => $record->id
        ];
        
    }

    public function destroyDispatcher($id)
    {
        try {

            $record = Dispatcher::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Transportista eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El transportista esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el transportista'];

        }


    }

    public function searchDriver(Request $request)
    {
        if ($request->byid!='true') {
            $drivers = Driver::where('name', 'like', "%{$request->input}%" )
                                ->orWhere('number','like', "%{$request->input}%")
                                ->orWhere('license','like', "%{$request->input}%")
                                ->orWhere('telephone','like', "%{$request->input}%")
                                ->orderBy('id')
                                ->get()
                                ->take(10)
                                ->transform(function($row) {
                                    return [
                                        'id' => $row->id,
                                        'identity_document_type_id' => $row->identity_document_type_id,
                                        'number' => $row->number,
                                        'name' => $row->name,
                                        'license' => $row->license,
                                        'telephone' => $row->telephone,
                                    ];
                                });
            return [
                'success' => true,
                'data' => array('drivers' => $drivers)
            ];
        }else{
            $drivers = Driver::findOrFail($request->input);
            return [
                'success' => true,
                'data' => $drivers
            ];
        }
    }

    public function storeDriver(Request $request)
    {

        $id = $request->input('id');
        $is_default = $request->input('is_default');
        if($is_default) {
            Driver::query()->update([
                'is_default' => false
            ]);
        }

        $record = Driver::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Conductor editado con éxito':'Conductor registrado con éxito',
            'id' => $record->id
        ];
        
    }

    public function destroyDriver($id)
    {
        try {

            $record = Driver::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Conductor eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El conductor esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el conductor'];

        }


    }


    public function searchTransport(Request $request)
    {
        if ($request->byid!='true') {
            $transports = Transport::where('plate_number', 'like', "%{$request->input}%" )
                                ->orWhere('model','like', "%{$request->input}%")
                                ->orWhere('brand','like', "%{$request->input}%")
                                ->orderBy('id')
                                ->get()
                                ->take(10)
                                ->transform(function($row) {
                                    return [
                                        'id' => $row->id,
                                        'plate_number' => $row->plate_number,
                                        'model' => $row->model,
                                        'brand' => $row->brand
                                    ];
                                });
            return [
                'success' => true,
                'data' => array('transports' => $transports)
            ];
        }else{
            $transports = Transport::findOrFail($request->input);
            return [
                'success' => true,
                'data' => $transports
            ];
        }
    }

    public function storeTransport(Request $request)
    {

        $id = $request->input('id');
        $is_default = $request->input('is_default');
        if($is_default) {
            Transport::query()->update([
                'is_default' => false
            ]);
        }

        $record = Transport::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Vehículo editado con éxito':'Vehículo registrado con éxito',
            'id' => $record->id
        ];
        
    }

    public function destroyTransport($id)
    {
        try {

            $record = Transport::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Vehículo eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El vehículo esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el vehículo'];

        }


    }

    public function searchOriginAddress(Request $request)
    {
        if ($request->byid!='true') {

            $originaddresses = OriginAddress::where('address', 'like', "%{$request->input}%" )
                                // ->orWhere('model','like', "%{$request->input}%")
                                // ->orWhere('brand','like', "%{$request->input}%")
                                ->orderBy('id')
                                ->get()
                                ->take(10)
                                ->transform(function($row) {
                                    return [
                                        'id' => $row->id,
                                        'address' => $row->address,
                                        'location_id' => $row->plate_number,
                                        // 'model' => $row->model,
                                    ];
                                });
            return [
                'success' => true,
                'data' => array('originaddresses' => $originaddresses)
            ];
        }else{
            $originaddresses = OriginAddress::findOrFail($request->input);
            return [
                'success' => true,
                'data' => $originaddresses
            ];
        }
    }

    public function storeOriginAddress(Request $request)
    {

        $id = $request->input('id');
        $is_default = $request->input('is_default');
        if($is_default) {
            OriginAddress::query()->update([
                'is_default' => false
            ]);
        }

        $record = OriginAddress::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Dirección editada con éxito':'Dirección registrada con éxito',
            'id' => $record->id
        ];
        
    }

    public function destroyOriginAddress($id)
    {
        try {

            $record = OriginAddress::findOrFail($id);
            $record->delete();

            return [
                'success' => true,
                'message' => 'Dirección eliminada con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'La Dirección esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar la Dirección'];

        }


    }

    public function documents_count()
    {
        $document_not_send = Document::whereNotSent()->count();

        $document_regularize = Document::whereRegularizeShipping()->count();
        
        return [
            'success' => true,
            'data' => array('document_not_send' => $document_not_send, 
                'document_regularize' => $document_regularize)
        ];

    }
    

    public function typeStatus()
    {
        $state_types = StateType::get();
        
        return [
            'success' => true,
            'data' => $state_types
        ];

    }

    public function tables()
    {
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $unitTypes = UnitType::whereActive()->get();
        $categories = Category::orderBy('name')->get();
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();
        $items = Item::with(['brand', 'category'])
                    ->whereWarehouse()
                    ->whereHasInternalId()
                    // ->whereNotIsSet()
                    ->whereIsActive()
                    ->orderBy('description')
                    ->take(20)
                    ->get()
                    ->transform(function($row) use($warehouse){
                        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                return [
                    'id' => $row->id,
                    'item_id' => $row->id,
                    'name' => $row->name,
                    'full_description' => $full_description,
                    'description' => $row->description,
                    'currency_type_id' => $row->currency_type_id,
                    'internal_id' => $row->internal_id,
                    'item_code' => $row->item_code,
                    'currency_type_symbol' => $row->currency_type->symbol,
                    'sale_unit_price' => str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                    'price' =>  str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                    'purchase_unit_price' => $row->purchase_unit_price,
                    'unit_type_id' => $row->unit_type_id,
                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                    'calculate_quantity' => (bool) $row->calculate_quantity,
                    'has_igv' => (bool) $row->has_igv,
                    'is_set' => (bool) $row->is_set,
                    'aux_quantity' => 1,
                    'brand' => optional($row->brand)->name,
                    'category' => optional($row->category)->name,
                    'active' => $row->active,
                    'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                    'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                    'warehouses' => collect($row->warehouses)->transform(function($row) {
                        return [
                            'warehouse_description' => $row->warehouse->description,
                            'stock' => $row->stock,
                            'warehouse_id' => $row->warehouse_id,
                        ];
                    }),
                    'item_unit_types' => $row->item_unit_types->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'description' => $row->description,
                            'unit_type_id' => $row->unit_type_id,
                            'quantity_unit' => $row->quantity_unit,
                            'price1' => $row->price1,
                            'price2' => $row->price2,
                            'price3' => $row->price3,
                            'price_default' => $row->price_default,
                        ];
                    }),
                ];
            });


        return [
            'success' => true,
            'data' => array('items' => $items, 'affectation_types' => $affectation_igv_types, 'categories' => $categories, 'unittypes' => $unitTypes)
        ];

    }

//incio categorias
    public function categories(Request $request)
    {
        // return $request->input;
        $categories = Category::where("name", "like", "%{$request->input}%")->orderBy('id')->get();
        return [
            'success' => true,
            'data' => $categories
        ];
    }

    public function category_detail($id)
    {
        $categories = Category::where("id", "=", $id)->first();
        return [
            'success' => true,
            'data' => $categories
        ];
    }

    public function category(CategoryRequest $request)
    {
        // return $request->id;
        $row = Category::firstOrNew(['id' => $request->id]);
        $row->fill($request->all());
        $row->save();

        return [
            'success' => true,
            'msg' => 'Categoria registrado con éxito',
            'data' => (object)[
                'id' => $row->id,
                'name' => $row->name,
            ]
        ];
    }

    public function category_destroy($id)
    {
        try {
            $item = Category::findOrFail($id);
            // $this->deleteRecordInitialKardex($item);
            $item->delete();
            return [
                'success' => true,
                'message' => 'Categoria eliminado con éxito'
            ];
        } catch (Exception $e) {
            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'La categoria esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el producto'];
        }
    }

//fin categorias

    public function getSeries(){

        return Series::where('establishment_id', auth()->user()->establishment_id)
                    ->whereIn('document_type_id', ['01', '03'])
                    ->get()
                    ->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'document_type_id' => $row->document_type_id,
                            'number' => $row->number
                        ];
                    });

    }

    public function getTypeDoc(){

        return IdentityDocumentType::where('active', true)
                    ->get()
                    ->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'description' => $row->description
                        ];
                    });

    }

    public function dispatches_series(){

        return Series::where('establishment_id', auth()->user()->establishment_id)
                    ->whereIn('document_type_id', ['09'])
                    ->get()
                    ->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'document_type_id' => $row->document_type_id,
                            'number' => $row->number
                        ];
                    });

    }

    public function dispatches_data(){

        $transferReasonTypes = TransferReasonType::query()->whereActive()->get();
        $transportModeTypes = TransportModeType::query()->whereActive()->get();
        $dispatchers = Dispatcher::all();
        $transports = Transport::all();
        $drivers = Driver::all();

        $series = Series::where('establishment_id', auth()->user()->establishment_id)
                    ->whereIn('document_type_id', ['09'])
                    ->get()
                    ->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'document_type_id' => $row->document_type_id,
                            'number' => $row->number
                        ];
                    });
        return compact('drivers','dispatchers','series','transportModeTypes','transferReasonTypes','transports');

    }

    public function getPaymentmethod(){ 

        $payment_method_type = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();
        return compact( 'payment_method_type','payment_destinations');
    }


    public function metodos_filtro(){ 

        $payment_method_type = PaymentMethodType::where("is_credit", "!=", 1)
                                ->get();
        return compact( 'payment_method_type');
    }

    public function document_email(Request $request)
    {        
        $company = Company::active();

        $Idbuscar = strpos($request->id, "-");

            if ($Idbuscar === false) {
                $document = Document::find($request->id);
            }else{
                $document = Document::where("external_id", "=", $request->id)->first();
                $request->id = $document->id;
            }


        $mailable =new DocumentEmail($company, $document);
        $sendIt = EmailController::SendMail($request->email, $mailable, $request->id, 1);

        return [
            'success' => true,
            'message'=> 'Documento enviado.'
        ];
    }

    public function saleNote_email(Request $request)
    {        
        $company = Company::active();

        $Idbuscar = strpos($request->id, "-");

            if ($Idbuscar === false) {
                $document = SaleNote::find($request->id);
            }else{
                $document = SaleNote::where("external_id", "=", $request->id)->first();
                $request->id = $document->id;
            }


        $mailable =new DocumentEmail($company, $document);
        $sendIt = EmailController::SendMail($request->email, $mailable, $request->id, 1);

        return [
            'success' => true,
            'message'=> 'Documento enviado.'
        ];
    }

    public function search_document(Request $request)
    {

            $document = Document::where('external_id', $request->id)->take(1)->get()->transform(function($row) {
                $letras_ = collect($row->legends);
            return [
                'id' => $row->id,
                'external_id' => $row->external_id,
                'user_id' => $row->user_id,
                'document_type_id' => $row->document_type_id,
                'document_type' => $row->document_type->description,
                'series' => $row->series,
                'number' => $row->number,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'time_of_issue' => $row->time_of_issue,
                'customer_name' => $row->customer->name,
                'customer_email' => $row->customer->email,
                'customer_number' => $row->customer->number,
                'customer_address' => $row->customer->address,
                'customer_address_dis' => $row->customer->district->description,
                'customer_address_prov' => $row->customer->province->description,
                'customer_address_dev' => $row->customer->department->description,
                'customer_address_dis_id' => $row->customer->district->id,
                'customer_address_prov_id' => $row->customer->province->id,
                'customer_address_dev_id' => $row->customer->department->id,
                'customer_telephone' => $row->customer->telephone,
                'customer_trade_name' => $row->customer->trade_name,
                'customer_document' => $row->customer->identity_document_type->description,
                'currency_type_id' => $row->currency_type_id,
                'currency_type' => $row->currency_type->description,
                'currency_symbol' => $row->currency_type->symbol,
                'payment_condition' =>  $row->payment_condition_id == '02' ? "CREDITO" : 'CONTADO',
                'payment_condition_id' =>  $row->payment_condition_id,
                'payment_method_type' =>  $row->payment_condition_id == '01' ? sizeof($row->payments) ? $row->payments[0]->payment_method_type->description : null : null,
                'payment_method_type_id' =>  $row->payment_condition_id == '01' ? sizeof($row->payments) ? $row->payments[0]->payment_method_type->id : null : null,
                'terms_condition' => $row->terms_condition,
                'seller_id' => $row->seller_id,
                'seller_name' => $row->user->name,
                'total_taxed' => $row->total_taxed,
                'total_igv' => $row->total_igv,
                'total_taxes' => $row->total_taxes,
                'total_value' => $row->total_value,
                'total_exonerated' => $row->total_exonerated,
                'subtotal' => $row->subtotal,
                'total' => $row->total,
                'total_letra' => $letras_[0]->value,
                'hash' => $row->hash,
                'items' => $row->items->transform(function($row) {
                        return [
                            'unit_price' => $row->item->unit_price,
                            'description' => $row->item->description,
                            'unit_type_id' => $row->item->unit_type_id,
                            'amount_plastic_bag_taxes' => $row->item->amount_plastic_bag_taxes,
                            'quantity' => $row->quantity,
                            'unit_value' => $row->unit_value,
                            'total_base_igv' => $row->total_base_igv,
                            'name_product_pdf' => $row->name_product_pdf,
                            'total_value' => $row->total_value,
                            'total' => $row->total,
                        ];
                    }),
                'payments' => $row->payments->transform(function($row) {
                        return [
                            'date_of_payment' => $row->date_of_payment->format('Y-m-d'),
                            'payment_method_type' => $row->payment_method_type->description,
                            'payment' => $row->payment,
                        ];
                    }),
                'fee' => $row->fee->transform(function($row) {
                        return [
                            'date' => $row->date->format('Y-m-d'),
                            'currency_type_id' => $row->currency_type_id,
                            'currency_type_name' => $row->currency_type_id == 'PEN' ? 'SOLES' : 'DOLARES',
                            'payment_method_type_id' => $row->payment_method_type_id,
                            'amount' => $row->amount,
                        ];
                    }),
                'qr' => $row->qr,
            ];
        });

                // $document = Document::where("external_id", "=", $request->id)->first();
                // $request->id = $document->id;
            // }

        return [
            'success' => true,
            'message'=> 'Documento encontrado.',
            'data'=> $document[0]
        ];
    }

    public function search_notesale(Request $request)
    {
        
            $notesale = SaleNote::where('external_id', $request->id)->take(1)->get()->transform(function($row) {

            return [
                'id' => $row->id,
                'external_id' => $row->external_id,
                'user_id' => $row->user_id,
                'document_type_id' => 'NV',
                'document_type' => 'Nota de Venta',
                'series' => $row->series,
                'number' => $row->number,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'time_of_issue' => $row->time_of_issue,
                'customer_name' => $row->customer->name,
                'customer_email' => $row->customer->email,
                'customer_number' => $row->customer->number,
                'customer_address' => $row->customer->address,
                'customer_address_dis' => $row->customer->district->description,
                'customer_address_prov' => $row->customer->province->description,
                'customer_address_dev' => $row->customer->department->description,
                'customer_address_dis_id' => $row->customer->district->id,
                'customer_address_prov_id' => $row->customer->province->id,
                'customer_address_dev_id' => $row->customer->department->id,
                'customer_telephone' => $row->customer->telephone,
                'customer_trade_name' => $row->customer->trade_name,
                'customer_document' => $row->customer->identity_document_type->description,
                'currency_type_id' => $row->currency_type_id,
                'currency_type' => $row->currency_type->description,
                'currency_symbol' => $row->currency_type->symbol,
                'payment_condition' =>  $row->payment_condition_id == '02' ? "CREDITO" : 'CONTADO',
                'terms_condition' => $row->terms_condition,
                'seller_id' => $row->seller_id,
                'seller_name' => $row->user->name,
                'total_taxed' => $row->total_taxed,
                'total_igv' => $row->total_igv,
                'total_taxes' => $row->total_taxes,
                'total_value' => $row->total_value,
                'total_exonerated' => $row->total_exonerated,
                'subtotal' => $row->subtotal,
                'total' => $row->total,
                'items' => $row->items->transform(function($row) {
                        return [
                            'unit_price' => $row->item->unit_price,
                            'description' => $row->item->description,
                            'unit_type_id' => $row->item->unit_type_id,
                            // 'amount_plastic_bag_taxes' => $row->item->amount_plastic_bag_taxes,
                            'quantity' => $row->quantity,
                            'unit_value' => $row->unit_value,
                            'total_base_igv' => $row->total_base_igv,
                            'name_product_pdf' => $row->name_product_pdf,
                            'total_value' => $row->total_value,
                            'total' => $row->total,
                        ];
                    }),
                'payments' => $row->payments->transform(function($row) {
                        return [
                            'date_of_payment' => $row->date_of_payment->format('Y-m-d'),
                            'payment_method_type' => $row->payment_method_type->description,
                            'payment' => $row->payment,
                        ];
                    }),
                // 'fee' => $row->fee->transform(function($row) {
                //         return [
                //             'date' => $row->date->format('Y-m-d'),
                //             'currency_type_id' => $row->currency_type_id,
                //             'currency_type_name' => $row->currency_type_id == 'PEN' ? 'SOLES' : 'DOLARES',
                //             'payment_method_type_id' => $row->payment_method_type_id,
                //             'amount' => $row->amount,
                //         ];
                //     }),
            ];
        });

           

        return [
            'success' => true,
            'message'=> 'Documento encontrado.',
            'data'=> $notesale[0]
        ];
    }

    public function document_light($startDate = null, $endDate = null)
    {

        if ($startDate == null) {
            $records = Document::orderBy('date_of_issue', 'desc')->whereTypeUser()
                ->take(20)->get()->transform(function ($row) {

                    $summary = DB::connection('tenant')
                        ->table('summaries AS s')
                        ->join('summary_documents AS sd', 's.id', '=', 'sd.summary_id')
                        ->where('sd.document_id', $row->id)
                        ->where('sd.description', '!=', null)
                        ->select('s.external_id', 's.ticket')
                        ->first();

                    return [
                        'id' => $row->id,
                        'external_id' => $row->external_id,
                        'user_id' => $row->user_id,
                        'document_type_id' => $row->document_type_id,
                        'document_type' => $row->document_type->description,
                        'series' => $row->series,
                        'number' => $row->number,
                        'number2' => '' . $row->series . '-' . $row->number,
                        'state_type_description' => $row->state_type->description,
                        'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                        'time_of_issue' => $row->time_of_issue,
                        'customer_name' => $row->customer->name,
                        'customer_email' => $row->customer->email,
                        'customer_number' => $row->customer->number,
                        'customer_address' => $row->customer->address,
                        'customer_address_dis' => $row->customer->district->description,
                        'customer_address_prov' => $row->customer->province->description,
                        'customer_address_dev' => $row->customer->department->description,
                        'customer_address_dis_id' => $row->customer->district->id,
                        'customer_address_prov_id' => $row->customer->province->id,
                        'customer_address_dev_id' => $row->customer->department->id,
                        'customer_telephone' => $row->customer->telephone,
                        'customer_trade_name' => $row->customer->trade_name,
                        'customer_document' => $row->customer->identity_document_type->description,
                        'currency_type_id' => $row->currency_type_id,
                        'currency_type' => $row->currency_type->description,
                        'currency_symbol' => $row->currency_type->symbol,
                        'payment_condition' => $row->payment_condition_id == '02' ? "CREDITO" : 'CONTADO',
                        'payment_condition_id' =>  $row->payment_condition_id,
                        'payment_method_type' =>  $row->payment_condition_id == '01' ? sizeof($row->payments) ? isset($row->payments[0]) ? $row->payments[0]->payment_method_type->description : null : null : null,
                        'payment_method_type_id' =>  $row->payment_condition_id == '01' ? sizeof($row->payments) ? isset($row->payments[0]) ? $row->payments[0]->payment_method_type->description : null : null : null,
                        'terms_condition' => $row->terms_condition,
                        'seller_name' => $row->user->name,
                        'total' => number_format($row->total, 2, '.', ','),
                        'voided' => $row->voided,
                        'voidedbol' => collect($summary),
                    ];
                });

        } else {
            $records = Document::whereBetween('date_of_issue', [$startDate, $endDate])->whereTypeUser()
                ->orderBy('date_of_issue', 'desc')
                ->take(20)->get()->transform(function ($row) {

                    $summary = DB::connection('tenant')
                        ->table('summaries AS s')
                        ->join('summary_documents AS sd', 's.id', '=', 'sd.summary_id')
                        ->where('sd.document_id', $row->id)
                        ->where('sd.description', '!=', null)
                        ->select('s.external_id', 's.ticket')
                        ->first();

                    return [
                        'id' => $row->id,
                        'external_id' => $row->external_id,
                        'user_id' => $row->user_id,
                        'document_type_id' => $row->document_type_id,
                        'document_type' => $row->document_type->description,
                        'series' => $row->series,
                        'number' => $row->number,
                        'number2' => '' . $row->series . '-' . $row->number,
                        'state_type_description' => $row->state_type->description,
                        'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                        'time_of_issue' => $row->time_of_issue,
                        'customer_name' => $row->customer->name,
                        'customer_email' => $row->customer->email,
                        'customer_number' => $row->customer->number,
                        'customer_address' => $row->customer->address,
                        'customer_address_dis' => $row->customer->district->description,
                        'customer_address_prov' => $row->customer->province->description,
                        'customer_address_dev' => $row->customer->department->description,
                        'customer_address_dis_id' => $row->customer->district->id,
                        'customer_address_prov_id' => $row->customer->province->id,
                        'customer_address_dev_id' => $row->customer->department->id,
                        'customer_telephone' => $row->customer->telephone,
                        'customer_trade_name' => $row->customer->trade_name,
                        'customer_document' => $row->customer->identity_document_type->description,
                        'currency_type_id' => $row->currency_type_id,
                        'currency_type' => $row->currency_type->description,
                        'currency_symbol' => $row->currency_type->symbol,
                        'payment_condition' => $row->payment_condition_id == '02' ? "CREDITO" : 'CONTADO',
                        'payment_condition_id' =>  $row->payment_condition_id,
                        'payment_method_type' =>  $row->payment_condition_id == '01' ? sizeof($row->payments) ? $row->payments[0]->payment_method_type->description : null : null,
                        'payment_method_type_id' =>  $row->payment_condition_id == '01' ? sizeof($row->payments) ? $row->payments[0]->payment_method_type->id : null : null,
                        'terms_condition' => $row->terms_condition,
                        'seller_name' => $row->user->name,
                        'total' => number_format($row->total, 2, '.', ','),
                        'voided' => $row->voided,
                        'voidedbol' => $summary,
                    ];
                });

        }
        return $records;
    }


    public function items()
    {
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $unitTypes = UnitType::whereActive()->get();
        $categories = Category::orderBy('name')->get();
        $categories = Category::orderBy('id')->take(10)->get();
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        $items = Item::with(['brand', 'category'])
                    ->whereWarehouse()
                    ->whereHasInternalId()
                    // ->whereNotIsSet()
                    // ->whereIsActive()
                    ->orderBy('description')
                    ->take(20)
                    ->get()
                    ->transform(function($row) use($warehouse){
                        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                return [
                    'id' => $row->id,
                    'item_id' => $row->id,
                    'name' => $row->name,
                    'full_description' => $full_description,
                    'description' => $row->description,
                    'currency_type_id' => $row->currency_type_id,
                    'internal_id' => $row->internal_id,
                    'item_code' => $row->item_code,
                    'currency_type_symbol' => $row->currency_type->symbol,
                    'sale_unit_price' => str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                    'price' =>  str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                    'purchase_unit_price' => $row->purchase_unit_price,
                    'unit_type_id' => $row->unit_type_id,
                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                    'calculate_quantity' => (bool) $row->calculate_quantity,
                    'has_igv' => (bool) $row->has_igv,
                    'is_set' => (bool) $row->is_set,
                    'aux_quantity' => 1,
                    'brand' => optional($row->brand)->name,
                    'category' => optional($row->category)->name,
                    'active' => $row->active,
                    'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                    'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                    'warehouses' => collect($row->warehouses)->transform(function($row) {
                        return [
                            'warehouse_description' => $row->warehouse->description,
                            'stock' => $row->stock,
                            'warehouse_id' => $row->warehouse_id,
                        ];
                    }),
                    'item_unit_types' => $row->item_unit_types->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'description' => $row->description,
                            'unit_type_id' => $row->unit_type_id,
                            'quantity_unit' => $row->quantity_unit,
                            'price1' => $row->price1,
                            'price2' => $row->price2,
                            'price3' => $row->price3,
                            'price_default' => $row->price_default,
                        ];
                    }),
                ];
            });


        return [
            'success' => true,
            'data' => array('items' => $items, 'affectation_types' => $affectation_igv_types, 'categories' => $categories, 'unittypes' => $unitTypes)
        ];

    }

    public function ItemsSearch(Request $request)
    {
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        $items = Item::where('description', 'like', "%{$request->input}%" )
                    ->orWhere('internal_id', 'like', "%{$request->input}%")
                    ->orWhere('barcode', 'like', "%{$request->input}%")
                    ->whereHasInternalId()
                    ->whereWarehouse()
                    // ->whereNotIsSet()
                    ->whereIsActive()
                    ->orderBy('description')
                    ->get()
                    ->take(20)
                    ->transform(function($row) use($warehouse){

                        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                        return [
                            'id' => $row->id,
                            'item_id' => $row->id,
                            'name' => $row->name,
                            'full_description' => $full_description,
                            'description' => $row->description,
                            'currency_type_id' => $row->currency_type_id,
                            'internal_id' => $row->internal_id,
                            'item_code' => $row->item_code ?? '',
                            'currency_type_symbol' => $row->currency_type->symbol,
                            'sale_unit_price' => str_replace("", "" , number_format( $row->sale_unit_price, 2)),
                            'purchase_unit_price' => $row->purchase_unit_price,
                            'unit_type_id' => $row->unit_type_id,
                            'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                            'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                            'calculate_quantity' => (bool) $row->calculate_quantity,
                            'has_igv' => (bool) $row->has_igv,
                            'is_set' => (bool) $row->is_set,
                            'aux_quantity' => 1,
                            'active' => $row->active,
                            'barcode' => $row->barcode ?? '',
                            'brand' => optional($row->brand)->name,
                            'category' => optional($row->category)->name,
                            'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                            'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                            'warehouses' => collect($row->warehouses)->transform(function($row) {
                                return [
                                    'warehouse_description' => $row->warehouse->description,
                                    'stock' => $row->stock,
                                    'warehouse_id' => $row->warehouse_id,
                                ];
                            }),
                            'item_unit_types' => $row->item_unit_types->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->description,
                                    'unit_type_id' => $row->unit_type_id,
                                    'quantity_unit' => $row->quantity_unit,
                                    'price1' => $row->price1,
                                    'price2' => $row->price2,
                                    'price3' => $row->price3,
                                    'precio' => $row->price3,
                                    'price_default' => $row->price_default,
                                ];
                            }),
                        ];
                    });

        return [
            'success' => true,
            'data' => array('items' => $items)
        ];
    }


    public function item(ItemRequest $request)
    {
        
        $row = Item::firstOrNew(['id' => $request->id]);
        $row->item_type_id = '01';
        // $row->has_igv = '01';
        $row->amount_plastic_bag_taxes = Configuration::firstOrFail()->amount_plastic_bag_taxes;
        $row->fill($request->all());
        $temp_path = $request->input('temp_path');

        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        if($temp_path) {

            $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR;

            $file_name_old = $request->input('image');
            $file_name_old_array = explode('.', $file_name_old);
            $file_content = file_get_contents($temp_path);
            $datenow = date('YmdHis');
            $file_name = Str::slug($row->description).'-'.$datenow.'.'.$file_name_old_array[1];
            Storage::put($directory.$file_name, $file_content);
            $row->image = $file_name;

            //--- IMAGE SIZE MEDIUM
            $image = \Image::make($temp_path);
            $file_name = Str::slug($row->description).'-'.$datenow.'_medium'.'.'.$file_name_old_array[1];
            $image->resize(512, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::put($directory.$file_name,  (string) $image->encode('jpg', 30));
            $row->image_medium = $file_name;

              //--- IMAGE SIZE SMALL
            $image = \Image::make($temp_path);
            $file_name = Str::slug($row->description).'-'.$datenow.'_small'.'.'.$file_name_old_array[1];
            $image->resize(256, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::put($directory.$file_name,  (string) $image->encode('jpg', 20));
            $row->image_small = $file_name;



        }else if(!$request->input('image') && !$request->input('temp_path') && !$request->input('image_url')){
            $row->image = 'imagen-no-disponible.jpg';
        }

        $row->save();

        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

        return [
            'success' => true,
            'msg' => 'Producto registrado con éxito',
            'data' => (object)[
                'id' => $row->id,
                'item_id' => $row->id,
                'name' => $row->name,
                'full_description' => $full_description,
                'description' => $row->description,
                'currency_type_id' => $row->currency_type_id,
                'internal_id' => $row->internal_id,
                'item_code' => $row->item_code ?? '',
                'currency_type_symbol' => $row->currency_type->symbol,
                'sale_unit_price' => str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                'purchase_unit_price' => $row->purchase_unit_price,
                'unit_type_id' => $row->unit_type_id,
                'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                'calculate_quantity' => (bool) $row->calculate_quantity,
                'has_igv' => (bool) $row->has_igv,
                'is_set' => (bool) $row->is_set,
                'aux_quantity' => 1,
                'active' => $row->active,
                'barcode' => $row->barcode ?? '',
                'brand' => optional($row->brand)->name,
                'category' => optional($row->category)->name,
                'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                'warehouses' => collect($row->warehouses)->transform(function($row) {
                    return [
                        'warehouse_description' => $row->warehouse->description,
                        'stock' => $row->stock,
                        'warehouse_id' => $row->warehouse_id,
                    ];
                }),
                'item_unit_types' => $row->item_unit_types->transform(function($row) {
                    return [
                        'id' => $row->id,
                        'description' => $row->description,
                        'unit_type_id' => $row->unit_type_id,
                        'quantity_unit' => $row->quantity_unit,
                        'price1' => $row->price1,
                        'price2' => $row->price2,
                        'price3' => $row->price3,
                        'price_default' => $row->price_default,
                    ];
                }),
            ],
        ];

    }

    public function destroy_item($id)
    {
        try {

            $item = Item::findOrFail($id);
            $this->deleteRecordInitialKardex($item);
            $item->delete();

            return [
                'success' => true,
                'message' => 'Producto eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El producto esta siendo usado por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el producto'];

        }


    }

    private function deleteRecordInitialKardex($item){

        if($item->kardex->count() == 1){
            ($item->kardex[0]->type == null) ? $item->kardex[0]->delete() : false;
        }

    }

    public function disable($id)
    {
        try {

            $item = Item::findOrFail($id);
            $item->active = 0;
            $item->save();

            return [
                'success' => true,
                'message' => 'Producto inhabilitado con éxito'
            ];

        } catch (Exception $e) {

            return  ['success' => false, 'message' => 'Error inesperado, no se pudo inhabilitar el producto'];

        }
    }


    public function enable($id)
    {
        try {

            $item = Item::findOrFail($id);
            $item->active = 1;
            $item->save();

            return [
                'success' => true,
                'message' => 'Producto habilitado con éxito'
            ];

        } catch (Exception $e) {

            return  ['success' => false, 'message' => 'Error inesperado, no se pudo habilitar el producto'];

        }
    }

    public function person(PersonRequest $request)
    {
        $row = Person::firstOrNew(['id' => $request->id]);
        if ($request->department_id === '-') {
            $request->merge([
                'department_id' => null,
                'province_id'   => null,
                'district_id'   => null
            ]);
        }
        $row->fill($request->all());
        $row->save();

        return [
            'success' => true,
            'msg' => ($request->type == 'customers') ? 'Cliente registrado con éxito' : 'Proveedor registrado con éxito',
            'data' => (object)[
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'identity_document_type_code' => $row->identity_document_type->code,
                'address' => $row->address,
                'email' => $row->email,
                'telephone' => $row->telephone,
                'country_id' => $row->country_id,
                'district_id' => $row->district_id,
                'addresses' => $row->addresses,
                'selected' => false
            ]
        ];
    }

    public function ubigeos()
    {
        $departments = Department::whereActive()->get();
        $provinces = Province::whereActive()->get();
        $districts = District::whereActive()->get();

        return [
            'success' => true,
            'data' => array('departments' => $departments, 'provinces' => $provinces, 'districts' => $districts)
        ];
    }

    public function unitTypes()
    {

        $unitTypes = UnitType::query()
            ->where('active', true)
            ->whereIn('id', ['KGM', 'TM'])->get()->transform(function ($r) {
                return [
                    'id' => $r->id,
                    'description' => func_str_to_upper_utf8($r->description)
                ];
            });
        // $unitTypes = UnitType::whereActive()->get();

        return [
            'success' => true,
            'data' => array('unitTypes' => $unitTypes)
        ];
    }

    public function getTablesGr()
    {

        $unitTypes = UnitType::query()
            ->where('active', true)
            ->whereIn('id', ['KGM', 'TNE'])->get()->transform(function ($r) {
                return [
                    'id' => $r->id,
                    'description' => func_str_to_upper_utf8($r->description)
                ];
            });

        $methods_payment = collect(PaymentMethodType::all())->transform(function ($row) {
            return (object)[
                'id'   => $row->id,
                'name' => $row->description,
                'sum'  => 0,
            ];
        });

        return [
            'success' => true,
            'data' => array('unitTypes' => $unitTypes, 'methodsPayment' => $methods_payment)
        ];
    }

    public function searchItems(Request $request)
    {
        // return $request->search_cat; 
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

    if ($request->search_cat=='true') {
        // return "dd";
        // Item::where('category_id', '=', "{$request->search_idcat}" )
        $items = Item::where('category_id', '=', "".$request->search_idcat."" )
                ->whereHasInternalId()
                ->whereWarehouse()
                // ->whereIsActive()
                ->orderBy('description')
                ->get()
                ->take(20)
                ->transform(function($row) use($warehouse){

                    $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                    return [
                        'id' => $row->id,
                        'item_id' => $row->id,
                        'name' => $row->name,
                        'full_description' => $full_description,
                        'description' => $row->description,
                        'currency_type_id' => $row->currency_type_id,
                        'internal_id' => $row->internal_id,
                        'item_code' => $row->item_code ?? '',
                        'currency_type_symbol' => $row->currency_type->symbol,
                        'sale_unit_price' => str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                        'purchase_unit_price' => $row->purchase_unit_price,
                        'unit_type_id' => $row->unit_type_id,
                        'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                        'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                        'calculate_quantity' => (bool) $row->calculate_quantity,
                        'has_igv' => (bool) $row->has_igv,
                        'is_set' => (bool) $row->is_set,
                        'aux_quantity' => 1,
                        'active' => $row->active,
                        'barcode' => $row->barcode ?? '',
                        'brand' => optional($row->brand)->name,
                        'category' => optional($row->category)->name,
                        'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                        'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                        'warehouses' => collect($row->warehouses)->transform(function($row) {
                            return [
                                'warehouse_description' => $row->warehouse->description,
                                'stock' => $row->stock,
                                'warehouse_id' => $row->warehouse_id,
                            ];
                        }),
                        'item_unit_types' => $row->item_unit_types->transform(function($row) {
                            return [
                                'id' => $row->id,
                                'description' => $row->description,
                                'unit_type_id' => $row->unit_type_id,
                                'quantity_unit' => $row->quantity_unit,
                                'price1' => $row->price1,
                                'price2' => $row->price2,
                                'price3' => $row->price3,
                                'price_default' => $row->price_default,
                            ];
                        }),
                    ];
                });
    }else{
        // return "33";
        $items = Item::where('description', 'like', "%{$request->input}%" )
                ->orWhere('internal_id', 'like', "%{$request->input}%")
                    ->orWhere('barcode', 'like', "%{$request->input}%")
                ->whereHasInternalId()
                ->whereWarehouse()
                ->whereIsActive()
                ->orderBy('description')
                ->get()
                ->take(20)
                ->transform(function($row) use($warehouse){

                    $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                    return [
                        'id' => $row->id,
                        'item_id' => $row->id,
                        'name' => $row->name,
                        'full_description' => $full_description,
                        'description' => $row->description,
                        'currency_type_id' => $row->currency_type_id,
                        'internal_id' => $row->internal_id,
                        'item_code' => $row->item_code ?? '',
                        'currency_type_symbol' => $row->currency_type->symbol,
                        'sale_unit_price' => str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                        'purchase_unit_price' => $row->purchase_unit_price,
                        'unit_type_id' => $row->unit_type_id,
                        'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                        'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                        'calculate_quantity' => (bool) $row->calculate_quantity,
                        'has_igv' => (bool) $row->has_igv,
                        'is_set' => (bool) $row->is_set,
                        'aux_quantity' => 1,
                        'active' => $row->active,
                        'barcode' => $row->barcode ?? '',
                        'brand' => optional($row->brand)->name,
                        'category' => optional($row->category)->name,
                        'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                        'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                        'warehouses' => collect($row->warehouses)->transform(function($row) {
                            return [
                                'warehouse_description' => $row->warehouse->description,
                                'stock' => $row->stock,
                                'warehouse_id' => $row->warehouse_id,
                            ];
                        }),
                        'item_unit_types' => $row->item_unit_types->transform(function($row) {
                            return [
                                'id' => $row->id,
                                'description' => $row->description,
                                'unit_type_id' => $row->unit_type_id,
                                'quantity_unit' => $row->quantity_unit,
                                'price1' => $row->price1,
                                'price2' => $row->price2,
                                'price3' => $row->price3,
                                'price_default' => $row->price_default,
                            ];
                        }),
                    ];
                });
    }


        return [
            'success' => true,
            'data' => array('items' => $items)
        ];
    }

    public function item_details($id)
    {
        $establishment_id = auth()->user()->establishment_id;
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();

        $items = Item::where('id', '=', $id )
                    // ->orWhere('internal_id', 'like', "%{$request->input}%")
                    ->whereHasInternalId()
                    ->whereWarehouse()
                    // ->whereNotIsSet()
                    ->whereIsActive()
                    ->orderBy('description')
                    ->take(1)
                    ->get()
                    ->transform(function($row) use($warehouse){

                        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

                return [
                    'id' => $row->id,
                    'item_id' => $row->id,
                    'name' => $row->name,
                    'full_description' => $full_description,
                    'description' => $row->description,
                    'currency_type_id' => $row->currency_type_id,
                    'internal_id' => $row->internal_id,
                    'item_code' => $row->item_code,
                    'currency_type_symbol' => $row->currency_type->symbol,
                    'sale_unit_price' => str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                    'purchase_unit_price' => $row->purchase_unit_price,
                    'unit_type_id' => $row->unit_type_id,
                    'stock_min' => $row->stock_min,
                    // 'item_unit_types' => $row->item_unit_types,
                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                    'calculate_quantity' => (bool) $row->calculate_quantity,
                    'has_igv' => (bool) $row->has_igv,
                    'is_set' => (bool) $row->is_set,
                    'aux_quantity' => 1,
                    'brand' => optional($row->brand)->name,
                    'category' => optional($row->category)->name,
                    'active' => $row->active,
                    'stock' => $row->unit_type_id!='ZZ' ? ItemWarehouse::where([['item_id', $row->id],['warehouse_id', $warehouse->id]])->first()->stock : '0',
                    'image' => $row->image != "imagen-no-disponible.jpg" ? url("/storage/uploads/items/" . $row->image) : url("/logo/" . $row->image),
                    'warehouses' => collect($row->warehouses)->transform(function($row) {
                        return [
                            'warehouse_description' => $row->warehouse->description,
                            'stock' => $row->stock,
                            'warehouse_id' => $row->warehouse_id,
                        ];
                    }),
                    'item_unit_types' => $row->item_unit_types->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'description' => $row->description,
                            'unit_type_id' => $row->unit_type_id,
                            'quantity_unit' => $row->quantity_unit,
                            'price1' => $row->price1,
                            'price2' => $row->price2,
                            'price3' => $row->price3,
                            'price_default' => $row->price_default,
                        ];
                    }),
                ];
            });

        return [
            'success' => true,
            'data' => $items
        ];
    }
 
    public function getIdentityDocumentTypeId($document_type_id){

        return ($document_type_id == '01') ? [6] : [1,4,6,7,0];

    }

    public function updateItem(ItemUpdateRequest $request, $itemId)
    {
        $row = Item::findOrFail($itemId);

        $row->fill($request->only('internal_id', 'barcode', 'model', 'has_igv', 'description', 'sale_unit_price', 'stock_min', 'item_code'));
        $row->save();

        $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;

        return [
            'success' => true,
            'msg' => 'Producto editado con éxito',
            'data' => (object)[
                'id' => $row->id,
                'item_id' => $row->id,
                'name' => $row->name,
                'full_description' => $full_description,
                'description' => $row->description,
                'currency_type_id' => $row->currency_type_id,
                'internal_id' => $row->internal_id,
                'item_code' => $row->item_code,
                'currency_type_symbol' => $row->currency_type->symbol,
                'sale_unit_price' => str_replace(",", "" , number_format( $row->sale_unit_price, 2)),
                'purchase_unit_price' => $row->purchase_unit_price,
                'category_id' => $row->category_id,
                'unit_type_id' => $row->unit_type_id,
                'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                'calculate_quantity' => (bool) $row->calculate_quantity,
                'has_igv' => (bool) $row->has_igv,
                'is_set' => (bool) $row->is_set,
                'aux_quantity' => 1,
            ],
        ];
    }

    //subir imagen app
    public function upload(Request $request)
    {

        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,gif,svg');

        if(!$validate_upload['success']){
            return $validate_upload;
        }

        if ($request->hasFile('file')) {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type'),
            ];

            return $this->upload_image($new_request);
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }


    function upload_image($request)
    {
        $file = $request['file'];
        $type = $request['type'];

        $temp = tempnam(sys_get_temp_dir(), $type);
        file_put_contents($temp, file_get_contents($file));

        $mime = mime_content_type($temp);
        $data = file_get_contents($temp);

        return [
            'success' => true,
            'data' => [
                'filename' => $file->getClientOriginalName(),
                'temp_path' => $temp,
                'temp_image' => 'data:' . $mime . ';base64,' . base64_encode($data)
            ]
        ];
    }


    public function customers_details($id)
    {
        $customers = Person::whereType('customers')->where('id', $id)->orderBy('name')->take(1)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
                'identity_document_type_code' => $row->identity_document_type->code,
                'address' => $row->address,
                'telephone' => $row->telephone,
                'country_id' => $row->country_id,
                'department_id' => $row->department_id,
                'province_id' => $row->province_id,
                'district_id' => $row->district_id,
                'email' => $row->email,
                'enabled' => $row->enabled,
                'addresses' => $row->addresses,
                'selected' => false
            ];
        });
        return [
            'success' => true,
            'data' => $customers
        ];

    }

///filtrar cpe por estados
    public function filterCPE($state, $type_doc)
    {


        $records = $this->getFilterRecords($state, $type_doc);

        return new DocumentCollection($records->paginate(config('tenant.items_per_page')));

    }

    public function getFilterRecords($state, $type_doc){

        $records = Document::query();

        if ($state!=0) {
            $records->whereTypeUser()
            ->where("state_type_id", $state)
            ->where("document_type_id", $type_doc)
            ->latest();
        }else{
             $records->whereTypeUser()->latest();
       }


        return $records;
    }

    //nuevo validador
    public function validateCpe_2(Request $request)
    {

        $company_number = $request->numero_ruc_emisor;
        $document_type_id = $request->codigo_tipo_documento;
        $series = $request->serie_documento;
        $number = $request->numero_documento;
        $date_of_issue = $request->fecha_de_emision;
        $total = $request->total;

        $auth_api = (new AuthApi())->getToken();
        if(!$auth_api['success']) return $auth_api;
        $this->access_token = $auth_api['data']['access_token'];
   
            $validate_cpe = new ValidateCpe(
                                $this->access_token,
                                $company_number,
                                $document_type_id,
                                $series,
                                $number,
                                $date_of_issue,
                                $total
                            );

            $response = $validate_cpe->search();


        if ($response['success']) {

            return [
                'success' => true,
                'response' => $response,
                'data' => [
                    'comprobante_estado_codigo' => $response["data"]["estadoCp"],
                    'comprobante_estado_descripcion' => $this->document_state[$response["data"]["estadoCp"]],
                    'empresa_estado_codigo' => $response["data"]["estadoRuc"],
                    'empresa_estado_description' => $this->company_state[$response["data"]["estadoRuc"]],
                    'empresa_condicion_codigo' => $response["data"]["condDomiRuc"],
                    'empresa_condicion_descripcion' => $this->company_condition[$response["data"]["condDomiRuc"]],
                ]
            ];

        } else {
            return [
                'success' => false,
                'data' => $response["data"]
            ];
        }

    }

//sector para caja chica 

    /**
     * @param int    $total
     * @param string $currency_type_id
     * @param int    $exchange_rate_sale
     *
     * @return float|int|mixed
     */
    public static function CalculeTotalOfCurency(
        $total = 0,
        $currency_type_id = 'PEN',
        $exchange_rate_sale = 1
    ) {
        if ($currency_type_id !== 'PEN') {
            $total = $total * $exchange_rate_sale;
        }
        return $total;
    }
    /**
     * Obtiene el string del metodo de pago
     *
     * @param $payment_id
     *
     * @return string 
     */
    public static function getStringPaymentMethod($payment_id) {
        $payment_method = PaymentMethodType::find($payment_id);
        return (!empty($payment_method)) ? $payment_method->description : '';
    }
    public function opening_cash_check()
    {
        $cash = Cash::where([['user_id', auth()->user()->id],['state', true]])->first();
         return [
            'success' => true,
            'cash' => $cash
        ];
        // return compact('cash');
    }

    public function opening_cash()
    {

        $cash = Cash::where([['user_id', auth()->user()->id],['state', true]])->first();

         return [
            'success' => true,
            'message' => ($id)?'Caja actualizada con éxito':'Caja aperturada con éxito'
        ];

        // return compact('cash');
    }

    //listar cajas
    public function records()
    {
        $records = Cash::where('user_id', auth()->user()->id)
                        // ->whereTypeUser()
                        ->orderBy('id', 'DESC')
                        ->get()->take(7);
                        // dd($records);
         return [
            'success' => true,
            'cashes' => $records
        ];
        // return new CashCollection($records->paginate(config('tenant.items_per_page')));
    }

    //crear caja

    public function opencash($value) {
        // $id = $request->input('id');
$cashopen = Cash::where([['user_id', auth()->user()->id],['state', true]])->first();
// dd($value);

if ($cashopen==null) {

     DB::connection('tenant')->transaction(function () use ($value) {

            $cash = Cash::firstOrNew(['id' => 0]);
            // $cash->fill($request->all());
            $cash->user_id=auth()->user()->id;
            $cash->beginning_balance=$value;
            $cash->state=1;

            // if(!$id){
                $cash->date_opening = date('Y-m-d');
                $cash->time_opening = date('H:i:s');
            // }

            $cash->save();

            $this->createCashTransaction($cash, $value);

        });

        return [
            'success' => true,
            'message' => 'Caja aperturada con éxito'
        ];

}else{
    return [
            'success' => false,
            'message' => 'El usuario ya tiene una caja abierta'
        ];
}
   

    }


    public function createCashTransaction($cash, $value){

        $this->destroyCashTransaction($cash);

        $data = [
            'date' => date('Y-m-d'),
            'description' => 'Saldo inicial',
            'payment_method_type_id' => '01',
            'payment' => $value,
            'payment_destination_id' => 'cash',
            'user_id' => auth()->user()->id,
        ];

        $cash_transaction = $cash->cash_transaction()->create($data);

        $this->createGlobalPaymentTransaction($cash_transaction, $data);

    }

   
    public function destroyCashTransaction($cash){

        $ini_cash_transaction = $cash->cash_transaction;

        if($ini_cash_transaction){
            CashTransaction::find($ini_cash_transaction->id)->delete();
        }

    }
     
    //cerrar caja
    public function close($id) {

        $cash = Cash::findOrFail($id);

        // dd($cash->cash_documents);

        $cash->date_closed = date('Y-m-d');
        $cash->time_closed = date('H:i:s');

        $final_balance = 0;
        $income = 0;

        foreach ($cash->cash_documents as $cash_document) {


            if($cash_document->sale_note){

                if(in_array($cash_document->sale_note->state_type_id, ['01','03','05','07','13'])){
                    $final_balance += ($cash_document->sale_note->currency_type_id == 'PEN') ? $cash_document->sale_note->total : ($cash_document->sale_note->total * $cash_document->sale_note->exchange_rate_sale);
                }

            }
            else if($cash_document->document){

                if(in_array($cash_document->document->state_type_id, ['01','03','05','07','13'])){
                    $final_balance += ($cash_document->document->currency_type_id == 'PEN') ? $cash_document->document->total : ($cash_document->document->total * $cash_document->document->exchange_rate_sale);
                }

            }
            else if($cash_document->expense_payment){

                if($cash_document->expense_payment->expense->state_type_id == '05'){
                    $final_balance -= ($cash_document->expense_payment->expense->currency_type_id == 'PEN') ? $cash_document->expense_payment->payment:($cash_document->expense_payment->payment  * $cash_document->expense_payment->expense->exchange_rate_sale);
                }

            }
            else if($cash_document->purchase){
                if(in_array($cash_document->purchase->state_type_id, ['01','03','05','07','13'])){
                    $final_balance -= ($cash_document->purchase->currency_type_id == 'PEN') ? $cash_document->purchase->total : ($cash_document->purchase->total * $cash_document->purchase->exchange_rate_sale);
                }
            }

        }

        $cash->final_balance = round($final_balance + $cash->beginning_balance, 2);
        $cash->income = round($final_balance, 2);
        $cash->state = false;
        $cash->save();

        return [
            'success' => true,
            'message' => 'Caja cerrada con éxito',
        ];

    }

    //reporte de caja en ticket

    /**
     * Obtiene un array de status para sumarlos en los reportes
     *
     * @return string[]
     */
    public static function getStateTypeId(){
        return [
            '01', //Registrado
            '03', // Enviado
            '05', // Aceptado
            '07', // Observado
            // '09', // Rechazado
            // '11', // Anulado
            '13' // Por anular
        ];
    }

    /**
     * @param int $cash_id
     *
     * @return array
     */
    public function setDataToReport($cash_id = 0) {

        set_time_limit(0);
        $data = [];
        /** @var Cash $cash */
        $cash = Cash::findOrFail($cash_id);
        $establishment = $cash->user->establishment;
        $status_type_id = self::getStateTypeId();
        $final_balance = 0;
        $cash_income = 0;
        $credit = 0;
        $cash_egress = 0;
        $cash_final_balance = 0;
        $cash_documents = $cash->cash_documents;
        $all_documents = [];

        // Metodos de pago de no credito
        $methods_payment_credit = PaymentMethodType::NonCredit()->get()->transform(function ($row) {
            return $row->id;
        })->toArray();

        $methods_payment = collect(PaymentMethodType::all())->transform(function ($row) {
            return (object)[
                'id'   => $row->id,
                'name' => $row->description,
                'sum'  => 0,
            ];
        });
        $company = Company::first();

        $data['cash'] = $cash;
        $data['cash_user_name'] = $cash->user->name;
        $data['cash_date_opening'] = $cash->date_opening;
        $data['cash_state'] = $cash->state;
        $data['cash_date_closed'] = $cash->date_closed;
        $data['cash_time_closed'] = $cash->time_closed;
        $data['cash_time_opening'] = $cash->time_opening;
        $data['cash_documents'] = $cash_documents;
        $data['cash_documents_total'] = (int)$cash_documents->count();

        $data['company_name'] = $company->name;
        $data['company_number'] = $company->number;
        $data['company'] = $company;

        $data['status_type_id'] = $status_type_id;

        $data['establishment'] = $establishment;
        $data['establishment_address'] = $establishment->address;
        $data['establishment_department_description'] = $establishment->department->description;
        $data['establishment_district_description'] = $establishment->district->description;
        $data['nota_venta'] = 0;
        $nota_credito = 0;
        $nota_debito = 0;
        /************************/

        foreach ($cash_documents as $cash_document) {
            $type_transaction = null;
            $document_type_description = null;
            $number = null;
            $date_of_issue = null;
            $customer_name = null;
            $customer_number = null;
            $currency_type_id = null;
            $temp = [];
            $notes = [];
            $usado = '';

            /** Documentos de Tipo Nota de venta */
            if ($cash_document->sale_note) {
                $sale_note = $cash_document->sale_note;
                if (in_array($sale_note->state_type_id, $status_type_id)) {
                    $record_total = 0;
                    $total = self::CalculeTotalOfCurency(
                        $sale_note->total,
                        $sale_note->currency_type_id,
                        $sale_note->exchange_rate_sale
                    );
                    $cash_income += $total;
                    $final_balance += $total;
                    if (count($sale_note->payments) > 0) {
                        $pays = $sale_note->payments;
                        foreach ($methods_payment as $record) {
                            $record_total = $pays->where('payment_method_type_id', $record->id)->sum('payment');
                            $record->sum = ($record->sum + $record_total);
                        }
                    }
                }
                $temp = [
                    'type_transaction'          => 'Venta',
                    'document_type_description' => 'NOTA DE VENTA',
                    'number'                    => $sale_note->number_full,
                    'date_of_issue'             => $sale_note->date_of_issue->format('Y-m-d'),
                    'date_sort'                 => $sale_note->date_of_issue,
                    'customer_name'             => $sale_note->customer->name,
                    'customer_number'           => $sale_note->customer->number,
                    'total'                     => ((!in_array($sale_note->state_type_id, $status_type_id)) ? 0
                        : $sale_note->total),
                    'currency_type_id'          => $sale_note->currency_type_id,
                    'usado'                     => $usado." ".__LINE__,
                    'tipo'                      => 'sale_note',
                ];
            } /** Documentos de Tipo Document */
            elseif ($cash_document->document) {
                $record_total = 0;
                $document = $cash_document->document;
                $payment_condition_id = $document->payment_condition_id;
                $pays = $document->payments;
                $pagado = 0;
                if (in_array($document->state_type_id, $status_type_id)) {
                    if ($payment_condition_id == '01') {
                        $total = self::CalculeTotalOfCurency(
                            $document->total,
                            $document->currency_type_id,
                            $document->exchange_rate_sale
                        );
                        $usado .= '<br>Tomado para income<br>';
                        $cash_income += $total;
                        $final_balance += $total;
                        if (count($pays) > 0) {
                            $usado .= '<br>Se usan los pagos<br>';
                            foreach ($methods_payment as $record) {
                                $record_total = $pays
                                    ->where('payment_method_type_id', $record->id)
                                    ->whereIn('document.state_type_id', $status_type_id)
                                    ->sum('payment');
                                $record->sum = ($record->sum + $record_total);
                                if (!empty($record_total)) {
                                    $usado .= self::getStringPaymentMethod($record->id).'<br>Se usan los pagos Tipo '.$record->id.'<br>';
                                }
                            }
                        }
                    } else {
                        $usado .= '<br> state_type_id: '.$document->state_type_id.'<br>';
                        foreach ($methods_payment as $record) {
                            $record_total = $pays
                                ->where('payment_method_type_id', $record->id)
                                ->whereIn('document.state_type_id', $status_type_id)
                                ->transform(function ($row) {
                                    if (!empty($row->change) && !empty($row->payment)) {
                                        return (object)[
                                            'payment' => $row->change * $row->payment,
                                        ];
                                    }
                                    return (object)[
                                        'payment' => $row->payment,
                                    ];
                                })
                                ->sum('payment');
                            $usado .= "Id de documento {$document->id} - ".self::getStringPaymentMethod($record->id)." /* $record_total */<br>";
                            if ($record->id == '09') {
                                $usado .= '<br>Se usan los pagos Credito Tipo '.$record->id.' ****<br>';
                                // $record->sum += $document->total;
                                $credit += $document->total;
                            } elseif ($record_total != 0) {
                                if ((in_array($record->id, $methods_payment_credit))) {
                                    $record->sum += $record_total;
                                    $pagado += $record_total;
                                    $cash_income += $record_total;
                                    $credit -= $record_total;
                                    $final_balance += $record_total;
                                } else {
                                    $record->sum += $record_total;
                                    $credit += $record_total;
                                }
                            }
                        }
                        foreach ($methods_payment as $record) {
                            if ($record->id == '09') {
                                $record->sum += $document->total - $pagado;
                            }
                        }
                    }
                }
                if ($record_total != $document->total) {
                    $usado .= '<br> Los montos son diferentes '.$document->total." vs ".$pagado."<br>";
                }
                $temp = [
                    'type_transaction'          => 'Venta',
                    'document_type_description' => $document->document_type->description,
                    'number'                    => $document->number_full,
                    'date_of_issue'             => $document->date_of_issue->format('Y-m-d'),
                    'date_sort'                 => $document->date_of_issue,
                    'customer_name'             => $document->customer->name,
                    'customer_number'           => $document->customer->number,
                    'total'                     => (!in_array($document->state_type_id, $status_type_id)) ? 0
                        : $document->total,
                    'currency_type_id'          => $document->currency_type_id,
                    'usado'                     => $usado." ".__LINE__,

                    'tipo' => 'document',
                ];
                /* Notas de credito o debito*/
                $notes = $document->getNotes();
            } /** Documentos de Tipo Servicio tecnico */
            elseif ($cash_document->technical_service) {
                $usado = '<br>Se usan para cash<br>';
                $technical_service = $cash_document->technical_service;
                $cash_income += $technical_service->cost;
                $final_balance += $technical_service->cost;
                if (count($technical_service->payments) > 0) {
                    $usado = '<br>Se usan los pagos<br>';
                    $pays = $technical_service->payments;
                    foreach ($methods_payment as $record) {
                        $record->sum = ($record->sum + $pays->where('payment_method_type_id', $record->id)->sum('payment'));
                        if (!empty($record_total)) {
                            $usado .= self::getStringPaymentMethod($record->id).'<br>Se usan los pagos Tipo '.$record->id.'<br>';
                        }
                    }
                }
                $temp = [
                    'type_transaction'          => 'Venta',
                    'document_type_description' => 'Servicio técnico',
                    'number'                    => 'TS-'.$technical_service->id,//$value->document->number_full,
                    'date_of_issue'             => $technical_service->date_of_issue->format('Y-m-d'),
                    'date_sort'                 => $technical_service->date_of_issue,
                    'customer_name'             => $technical_service->customer->name,
                    'customer_number'           => $technical_service->customer->number,
                    'total'                     => $technical_service->cost,
                    'currency_type_id'          => 'PEN',
                    'usado'                     => $usado." ".__LINE__,
                    'tipo'                      => 'technical_service',
                ];
            } /** Documentos de Tipo Gastos */
            elseif ($cash_document->expense_payment) {
                $expense_payment = $cash_document->expense_payment;
                //    $usado = '<br>No se usan pagos<br>';

                if ($expense_payment->expense->state_type_id == '05') {
                    $total = self::CalculeTotalOfCurency(
                        $expense_payment->payment,
                        $expense_payment->expense->currency_type_id,
                        $expense_payment->expense->exchange_rate_sale
                    );
                    //        $usado = '<br>Se usan para cash<br>';

                    $cash_egress += $total;
                    $final_balance -= $total;

                }
                $temp = [
                    'type_transaction'          => 'Gasto',
                    'document_type_description' => $expense_payment->expense->expense_type->description,
                    'number'                    => $expense_payment->expense->number,
                    'date_of_issue'             => $expense_payment->expense->date_of_issue->format('Y-m-d'),
                    'date_sort'                 => $expense_payment->expense->date_of_issue,
                    'customer_name'             => $expense_payment->expense->supplier->name,
                    'customer_number'           => $expense_payment->expense->supplier->number,
                    'total'                     => -$expense_payment->payment,
                    'currency_type_id'          => $expense_payment->expense->currency_type_id,
                    'usado'                     => $usado." ".__LINE__,

                    'tipo' => 'expense_payment',
                ];
            }

            /** Documentos de Tipo compras */
            else if ($cash_document->purchase) {

                /**
                 * @var \App\Models\Tenant\CashDocument $cash_document
                 * @var \App\Models\Tenant\Purchase $purchase
                 * @var \Illuminate\Database\Eloquent\Collection $payments
                 */
                $purchase = $cash_document->purchase;

                if (in_array($purchase->state_type_id, $status_type_id)) {

                    $payments = $purchase->purchase_payments;
                    $record_total = 0;
                    // $total = self::CalculeTotalOfCurency($purchase->total, $purchase->currency_type_id, $purchase->exchange_rate_sale);
                    // $cash_egress += $total;
                    // $final_balance -= $total;
                    if (count($payments) > 0) {
                        $pays = $payments;
                        foreach ($methods_payment as $record) {
                            $record_total = $pays->where('payment_method_type_id', $record->id)->sum('payment');
                            $record->sum = ($record->sum - $record_total);
                            $cash_egress += $record_total;
                            $final_balance -= $record_total;
                        }

                    }

                }

                $temp = [
                    'type_transaction'          => 'Compra',
                    'document_type_description' => $purchase->document_type->description,
                    'number'                    => $purchase->number_full,
                    'date_of_issue'             => $purchase->date_of_issue->format('Y-m-d'),
                    'date_sort'                 => $purchase->date_of_issue,
                    'customer_name'             => $purchase->supplier->name,
                    'customer_number'           => $purchase->supplier->number,
                    'total'                     => ((!in_array($purchase->state_type_id, $status_type_id)) ? 0 : $purchase->total),
                    'currency_type_id'          => $purchase->currency_type_id,
                    'usado'                     => $usado." ".__LINE__,
                    'tipo'                      => 'purchase',
                ];
            }




            if (!empty($temp)) {
                $temp['usado'] = isset($temp['usado']) ? $temp['usado'] : '--';
                $temp['total_string'] = self::FormatNumber($temp['total']);
                $all_documents[] = $temp;
            }

            /** Notas de credito o debito */
            if ($notes !== null) {
                foreach ($notes as $note) {
                    $usado = 'Tomado para ';
                    /** @var \App\Models\Tenant\Note $note */
                    $sum = $note->isDebit();
                    $type = ($note->isDebit()) ? 'Nota de debito' : 'Nota de crédito';
                    $document = $note->getDocument();
                    if (in_array($document->state_type_id, $status_type_id)) {
                        $record_total = $document->getTotal();
                        /** Si es credito resta */
                        if ($sum) {
                            $usado .= 'Nota de debito';
                            $nota_debito += $record_total;
                            $final_balance += $record_total;
                            $usado .= "Id de documento {$document->id} - Nota de Debito /* $record_total * /<br>";
                        } else {
                            $usado .= 'Nota de credito';
                            $nota_credito += $record_total;
                            $final_balance -= $record_total;
                            $usado .= "Id de documento {$document->id} - Nota de Credito /* $record_total * /<br>";
                        }
                        $temp = [
                            'type_transaction'          => $type,
                            'document_type_description' => $document->document_type->description,
                            'number'                    => $document->number_full,
                            'date_of_issue'             => $document->date_of_issue->format('Y-m-d'),
                            'date_sort'                 => $document->date_of_issue,
                            'customer_name'             => $document->customer->name,
                            'customer_number'           => $document->customer->number,
                            'total'                     => (!in_array($document->state_type_id, $status_type_id)) ? 0
                                : $document->total,
                            'currency_type_id'          => $document->currency_type_id,
                            'usado'                     => $usado.' '.__LINE__,
                            'tipo'                      => 'document',
                        ];

                        $temp['usado'] = isset($temp['usado']) ? $temp['usado'] : '--';
                        $temp['total_string'] = self::FormatNumber($temp['total']);
                        $all_documents[] = $temp;
                    }

                }
            }

        }
//        $all_documents = collect($all_documents)->sortBy('date_sort')->all();
        /************************/
        /************************/
        $data['all_documents'] = $all_documents;
        $temp = [];

        foreach ($methods_payment as $index => $item) {
            $temp[] = [
                'iteracion' => $index + 1,
                'name'      => $item->name,
                'sum'       => self::FormatNumber($item->sum),
            ];
        }

        $data['nota_credito'] = $nota_credito;
        $data['nota_debito'] = $nota_debito;
        $data['methods_payment'] = $temp;
        $data['credit'] = self::FormatNumber($credit);
        $data['cash_beginning_balance'] = self::FormatNumber($cash->beginning_balance);
        $cash_final_balance = $final_balance + $cash->beginning_balance;
        $data['cash_egress'] = self::FormatNumber($cash_egress);
        $data['cash_final_balance'] = self::FormatNumber($cash_final_balance);

        $data['cash_income'] = self::FormatNumber($cash_income);

        //$cash_income = ($final_balance > 0) ? ($cash_final_balance - $cash->beginning_balance) : 0;
        return $data;
    }

    /**
     * Genera un pdf basado en el formato deseado
     *
     * @param        $cash
     * @param string $format
     *
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \Throwable
     */
    private function getPdf($cash, $format = 'ticket') {
        $data = $this->setDataToReport($cash);
        $quantity_rows = 30;//$cash->cash_documents()->count();


        $view = view('pos::cash.report_pdf_'.$format, compact('data'));
        $html = $view->render();
        /*
        $html = view('pos::cash.report_pdf_' . $format,
            compact('cash', 'company', 'methods_payment','status_type_id'))->render();
        */
        $width = 78;
        if ($format === 'ticket') {
            $pdf = new Mpdf([
                                'mode'          => 'utf-8',
                                'format'        => [
                                    $width,
                                    190 +
                                    ($quantity_rows * 8),
                                ],
                                'margin_top'    => 5,
                                'margin_right'  => 5,
                                'margin_bottom' => 5,
                                'margin_left'   => 5,
                            ]);
        } else {
            $pdf = new Mpdf([
                                'mode' => 'utf-8',
                            ]);
        }

        $pdf->WriteHTML($html);

        return $pdf->output('', 'S');
    }

          
    public function report_products($id)
    {

        $data = $this->getDataReport($id);
        $pdf = PDF::loadView('tenant.cash.report_product_pdf', $data);
        $filename = "Reporte_POS_PRODUCTOS - {$data['cash']->user->name} - {$data['cash']->date_opening} {$data['cash']->time_opening}";

        return $pdf->stream($filename.'.pdf');

    }

    public function report_products_ticket($id)
    {
        $data = $this->getDataReport($id);
        // dd($data["documents"]->count());
        $total_ = ($data["documents"]->count()*20)+250;
        $pdf = PDF::loadView('tenant.cash.report_product_pdf_ticket', $data)
            ->setPaper(array(0,0,180,$total_), 'portrait');
        $filename = "Reporte_POS_PRODUCTOS - {$data['cash']->user->name} - {$data['cash']->date_opening} {$data['cash']->time_opening}";

        return $pdf->stream($filename.'.pdf');

    }


    public function getDataReport($id){

        $documents_with_out_sale_note = SaleNote::where('document_id', '>=', 1)->get()->pluck('document_id');
        $cash = Cash::findOrFail($id);

        $company = Company::first();
        $cash_documents =  CashDocument::whereNotIn('document_id', $documents_with_out_sale_note)
            ->where('cash_id', $cash->id)
            ->get()
            ->pluck('document_id');

        $source = DocumentItem::with('document')->whereIn('document_id', $cash_documents)->get();

        $documents = collect($source)->transform(function(DocumentItem $row){
            $item = $row->item;
            $data = $row->toArray();
            $data['item'] =$item;
            $data['unit_value']=$data['unit_value']??0;
            $data['sub_total'] =$data['unit_value'] * $data['quantity'];
            $data['number_full'] = $row->document->number_full;
            $data['description'] = $row->item->description;
            return $data;
        });

        $documents = $documents->merge($this->getSaleNotesReportProducts($cash));

        $documents = $documents->merge($this->getPurchasesReportProducts($cash));

        return compact("cash", "company", "documents");

        // $cash = Cash::findOrFail($id);
        // $company = Company::first();
        // $cash_documents =  CashDocument::select('document_id')->where('cash_id', $cash->id)->get();

        // $source = DocumentItem::with('document')->whereIn('document_id', $cash_documents)->get();

        // $documents = collect($source)->transform(function($row){
        //     return [
        //         'id' => $row->id,
        //         'number_full' => $row->document->number_full,
        //         'description' => $row->item->description,
        //         'quantity' => $row->quantity,
        //     ];
        // });

        // $documents = $documents->merge($this->getSaleNotesReportProducts($cash));
        
        // $documents = $documents->merge($this->getPurchasesReportProducts($cash));

        // return compact("cash", "company", "documents");

    }



    public function getSaleNotesReportProducts($cash){

        $cd_sale_notes =  CashDocument::select('sale_note_id')->where('cash_id', $cash->id)->get();

        $sale_note_items = SaleNoteItem::with('sale_note')->whereIn('sale_note_id', $cd_sale_notes)->get();

        return collect($sale_note_items)->transform(function(SaleNoteItem $row){

            $item_name = is_null($row->name)?'':$row->name;
            if($item_name === '') {
                $item_name = $row->item->description;
            }

            $item = $row->item;
            $data = $row->toArray();
            $data['item'] =$item;
            $data['unit_value']=$data['unit_value']??0;
            $data['sub_total'] =$data['unit_value'] * $data['quantity'];
            $data['number_full'] = $row->sale_note->number_full;
            $data['description'] = $item_name;
            return $data;
        });
    }

    public function getPurchasesReportProducts($cash){

        $cd_purchases =  CashDocument::select('purchase_id')->where('cash_id', $cash->id)->get();
        $purchase_items = PurchaseItem::with('purchase')->whereIn('purchase_id', $cd_purchases)->get();

        return collect($purchase_items)->transform(function(PurchaseItem $row){

            $item = $row->item;
            $data = $row->toArray();
            $data['item'] =$item;
            $data['unit_value']=$data['unit_value']??0;
            $data['sub_total'] =$data['unit_value'] * $data['quantity'];
            $data['number_full'] = $row->purchase->number_full;
            $data['description'] = $row->item->description;
            return $data;
        });

    }


    public function pdf($cash_id) {

        $company = Company::active();
        $cash = Cash::findOrFail($cash_id);

        set_time_limit(0); 
        $pdf = PDF::loadView('report::income_summary.report_pdf', compact("cash", "company"));

        $filename = "Reporte_Resúmen_Ingreso - {$cash->user->name} - {$cash->date_opening} {$cash->time_opening}";
        
        return $pdf->download($filename.'.pdf');
    }


    /**
     * Reporte en Ticket formato cash_pdf_ticket
     *
     * @param $cash
     *
     * @return mixed
     * @throws \Mpdf\MpdfException
     * @throws \Throwable
     */
    public function reportTicket($cash) {
        $temp = tempnam(sys_get_temp_dir(), 'cash_pdf_ticket');
        file_put_contents($temp, $this->getPdf($cash, 'ticket'));

        return response()->file($temp);
    }

    /**
     * Reporte en A4 formato cash_pdf_a4
     *
     * @param $cash
     *
     * @return mixed
     * @throws \Mpdf\MpdfException
     * @throws \Throwable
     */
    public function reportA4($cash) {
        $temp = tempnam(sys_get_temp_dir(), 'cash_pdf_a4');
        file_put_contents($temp, $this->getPdf($cash, 'a4'));

        return response()->file($temp);
    }


////modulo reportes


    public function report($year, $month, $day, $method, $type_user, $user_id)
    {
        // return $day; 
        $request = [
            'customer_id' => null,
            'date_end' => "".$year."-".$month."-".$day."",
            'date_start' => "".$year."-".$month."-".$day."",
            'enabled_expense' => null,
            'enabled_move_item' => false,
            'enabled_transaction_customer' => false,
            'establishment_id' => 1,
            'item_id' => null,
            'month_end' => "".$year."-".$month."",
            'month_start' => "".$year."-".$month."",
            'type_user' => "".$type_user."",
            'period' => "".$method."",
            'user_id' => "".$user_id."",
        ];

        return [
            'data' => $this->data_mobile($request)
        ];
    }

    public function data_mobile($request)
    {
        $establishment_id = $request['establishment_id'];
        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $type_user = $request['type_user'];
        $user_id = $request['user_id'];

        $d_start = null;
        $d_end = null;

        /** @todo: Eliminar periodo, fechas y cambiar por

        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        \App\CoreFacturalo\Helpers\Functions\FunctionsHelper\FunctionsHelper::setDateInPeriod($request, $date_start, $date_end);
         */
        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_start;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }

        return [
            'general' => $this->totals($establishment_id, $d_start, $d_end, $period, $month_start, $month_end, $type_user, $user_id),
        ];
    }
    
    /**
     * @param $establishment_id
     * @param $date_start
     * @param $date_end
     * @param $period
     * @param $month_start
     * @param $month_end
     * @return array
     */
    private function totals($establishment_id, $date_start, $date_end, $period, $month_start, $month_end, $type_user, $user_id)
    {

        if($date_start && $date_end){

if ($type_user=="admin") {
    if ($user_id=="0") {
        $sale_notes = SaleNote::query()->where('establishment_id', $establishment_id)
                                       ->where('changed', false)
                                       ->whereBetween('date_of_issue', [$date_start, $date_end])
                                       ->whereStateTypeAccepted()
                                       ->get();
        $orders = OrderNote::query()->where('establishment_id', $establishment_id)
                                       ->whereBetween('date_of_issue', [$date_start, $date_end])
                                       ->get();

        $documents = Document::query()->where('establishment_id', $establishment_id)
                                        ->whereBetween('date_of_issue', [$date_start, $date_end])
                                        ->get();
    }else{
        $sale_notes = SaleNote::query()->where('establishment_id', $establishment_id)
                                       ->where('user_id', $user_id)
                                       ->where('changed', false)
                                       ->whereBetween('date_of_issue', [$date_start, $date_end])
                                       ->whereStateTypeAccepted()
                                       ->get();
        $orders = OrderNote::query()->where('establishment_id', $establishment_id)
                                       ->where('user_id', $user_id)
                                       ->whereBetween('date_of_issue', [$date_start, $date_end])
                                       ->get();

        $documents = Document::query()->where('establishment_id', $establishment_id)
                                       ->where('user_id', $user_id)
                                        ->whereBetween('date_of_issue', [$date_start, $date_end])
                                        ->get();
    }                              
}else{
    $sale_notes = SaleNote::query()->where('establishment_id', $establishment_id)
                                   ->where('user_id', auth()->user()->id)
                                   ->where('changed', false)
                                   ->whereBetween('date_of_issue', [$date_start, $date_end])
                                   ->whereStateTypeAccepted()
                                   ->get();
    $orders = OrderNote::query()->where('establishment_id', $establishment_id)
                                   ->where('user_id', auth()->user()->id)
                                   ->whereBetween('date_of_issue', [$date_start, $date_end])
                                   ->get();

    $documents = Document::query()->where('establishment_id', $establishment_id)
                                   ->where('user_id', auth()->user()->id)
                                   ->whereBetween('date_of_issue', [$date_start, $date_end])
                                   ->get();
}



        }else{


if ($type_user=="admin") {
    $sale_notes = SaleNote::query()->where('establishment_id', $establishment_id)
                                   ->where('changed', false)
                                   ->whereStateTypeAccepted()
                                   ->get();
    $orders = OrderNote::query()->where('establishment_id', $establishment_id)
                                   ->get();
    $documents = Document::query()->where('establishment_id', $establishment_id)->get();
}else{
    $sale_notes = SaleNote::query()->where('establishment_id', $establishment_id)
                                   ->where('changed', false)
                                   ->whereStateTypeAccepted()
                                   ->get();
    $orders = OrderNote::query()->where('establishment_id', $establishment_id)                                   ->where('user_id', auth()->user()->id)
                                   ->get();
    $documents = Document::query()->where('establishment_id', $establishment_id)
                                   ->where('user_id', auth()->user()->id)
                                   ->get();
}
        }





        //DOCUMENT
        //PEN
        $document_total_pen = 0;
        $document_total_note_credit_pen = 0;

        $document_total_pen = collect($documents->whereIn('state_type_id', ['01','03','05','07','13'])->whereIn('document_type_id', ['01','03','08']))->where('currency_type_id', 'PEN')->sum('total');

        //USD
        $document_total_usd = 0;
        $document_total_note_credit_usd = 0;

        $documents_usd = $documents->whereIn('state_type_id', ['01','03','05','07','13'])
                                    ->whereIn('document_type_id', ['01','03','08'])
                                    ->where('currency_type_id', 'USD');

        foreach ($documents_usd as $dusd) {
            $document_total_usd += $dusd->total * $dusd->exchange_rate_sale;
        }

        //TWO CURRENCY

        foreach ($documents as $document)
        {

            if(in_array($document->state_type_id, ['01','03','05','07','13'])){

                if($document->currency_type_id == 'PEN'){
                    $document_total_note_credit_pen += ($document->document_type_id == '07') ? $document->total:0; //nota de credito
                }else{
                    $document_total_note_credit_usd += ($document->document_type_id == '07') ? $document->total * $document->exchange_rate_sale:0; //nota de credito
                }
            }

        }

        $document_total = $document_total_pen + $document_total_usd;
        $document_total_note_credit = $document_total_note_credit_pen + $document_total_note_credit_usd;

        $documents_total = $document_total - $document_total_note_credit;

        // dd($document_total_pen , $document_total_usd, $document_total_note_credit_pen);

        //DOCUMENT

        //SALE NOTE

        //PEN
        $sale_note_total_pen = 0;

        $sale_note_total_pen = collect($sale_notes->where('currency_type_id', 'PEN'))->sum('total');

        //USD
        $sale_note_total_usd = 0;

        //TWO CURRENCY
        foreach ($sale_notes as $sale_note)
        {
            if($sale_note->currency_type_id == 'USD'){
                $sale_note_total_usd += $sale_note->total * $sale_note->exchange_rate_sale;
            }
        }

        //TOTALS
        $sale_notes_total = $sale_note_total_pen + $sale_note_total_usd;



        //ORDERS

        //PEN
        $orders_total_pen = 0;

        $orders_total_pen = collect($orders->where('currency_type_id', 'PEN'))->sum('total');

        //USD
        $orders_total_usd = 0;

        //TWO CURRENCY
        foreach ($orders as $order)
        {
            if($order->currency_type_id == 'USD'){
                $orders_total_usd += $order->total * $order->exchange_rate_sale;
            }
        }

        //TOTALS
        $orders_total = $orders_total_pen + $orders_total_usd;

        //ORDER 

        $total = $sale_notes_total + $documents_total;


        if($period == 'month')
        {
            $data_array = $this->getDocumentsByDays($sale_notes, $documents, $orders, $date_start, $date_end);
        }
        else if($period == 'between_months' && $month_start === $month_end)
        {
            $data_array = $this->getDocumentsByDays($sale_notes, $documents, $orders, $date_start, $date_end);
        }
        else if($period == 'between_months')
        {
            $data_array = $this->getDocumentsByMonths($sale_notes, $documents, $month_start, $month_end);
        }
        else
        {
            if($date_start === $date_end) {
                $data_array = $this->getDocumentsByHours($sale_notes, $documents);
            } else {
                $data_array = $this->getDocumentsByDays($sale_notes, $documents, $orders, $date_start, $date_end);
            }
        }

        return [
            'totals' => [
                'total_documents' => number_format($documents_total,2, ".", ""),
                'total_sale_notes' => number_format($sale_notes_total,2, ".", ""),
                'total_orders' => number_format($orders_total,2, ".", ""),
                'total' => number_format($total,2, ".", ""),
            ],
            'graph' => [
                'labels' => array_keys($data_array['total_array']),
                'datasets' => [
                    [
                        'label' => 'Total notas de venta',
                        'data' => array_values($data_array['sale_notes_array']),
                        'backgroundColor' => 'rgb(255, 99, 132)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'borderWidth' => 1,
                        'fill' => false,
                        'lineTension' => 0,
                    ],
                    [
                        'label' => 'Total pedidos',
                        'data' => array_values($data_array['sale_notes_array']),
                        'backgroundColor' => 'rgb(156, 39, 176)',
                        'borderColor' => 'rgb(156, 39, 176)',
                        'borderWidth' => 1,
                        'fill' => false,
                        'lineTension' => 0,
                    ],
                    [
                        'label' => 'Total comprobantes',
                        'data' => array_values($data_array['documents_array']),
                        'backgroundColor' => 'rgb(54, 162, 235)',
                        'borderColor' => 'rgb(54, 162, 235)',
                        'borderWidth' => 1,
                        'fill' => false,
                        'lineTension' => 0,
                    ],
                    [
                        'label' => 'Total',
                        'data' => array_values($data_array['total_array']),
                        'backgroundColor' => 'rgb(201, 203, 207)',
                        'borderColor' => 'rgb(201, 203, 207)',
                        'borderWidth' => 1,
                        'fill' => false,
                        'lineTension' => 0,
                    ]

                ],
            ]
        ];
    }
 
    private function getDocumentsByDays($sale_notes, $documents, $orders, $date_start, $date_end)
    {
        $sale_notes_array = [];
        $documents_array = [];
        $total_array = [];
        $document_total = 0;
        $orders_total = 0;
        $document_total_note_credit = 0;

        $d_start = Carbon::parse($date_start);
        $d_end = Carbon::parse($date_end);

        while ($d_start <= $d_end)
        {

            //ORDERS
            $orders_total_pen = 0;
            $orders_total_pen = collect($orders->where('currency_type_id', 'PEN'))->where('date_of_issue', $d_start)->sum('total');

            $orders_total_usd = 0;
            $orders_total_usd = collect($orders->where('currency_type_id', 'USD'))->where('date_of_issue', $d_start)->map(function ($item, $key) {
                return $item->total * $item->exchange_rate_sale;
            })->sum();

            $orders_total = round($orders_total_pen + $orders_total_usd, 2);
            $total_orders[$d_start->format('d').'d'] = $orders_total;


            //SALE NOTE
            $sale_note_total_pen = collect($sale_notes->where('currency_type_id', 'PEN'))->where('date_of_issue', $d_start)->sum('total');

            $sale_note_total_usd = collect($sale_notes->where('currency_type_id', 'USD'))->where('date_of_issue', $d_start)->map(function ($item, $key) {
                return $item->total * $item->exchange_rate_sale;
            })->sum();

            $sale_note_total = round($sale_note_total_pen + $sale_note_total_usd, 2);
            $sale_notes_array[$d_start->format('d').'d'] = $sale_note_total;

            //DOCUMENT
            $document_total_pen = collect($documents)->whereIn('state_type_id', ['01','03','05','07','13'])
                                                 ->whereIn('document_type_id', ['01','03','08'])
                                                 ->where('currency_type_id', 'PEN')
                                                 ->where('date_of_issue', $d_start)->sum('total');

            $document_total_usd = collect($documents)->whereIn('state_type_id', ['01','03','05','07','13'])
                                                 ->whereIn('document_type_id', ['01','03','08'])
                                                 ->where('currency_type_id', 'USD')
                                                 ->where('date_of_issue', $d_start)
                                                 ->map(function ($item, $key) {
                                                    return $item->total * $item->exchange_rate_sale;
                                                 })->sum();

            $document_total_note_credit_pen = collect($documents)->where('document_type_id', '07')
                                                            ->whereIn('state_type_id', ['01','03','05','07','13'])
                                                            ->where('currency_type_id', 'PEN')
                                                            ->where('date_of_issue', $d_start)
                                                            ->sum('total');

            $document_total_note_credit_usd = collect($documents)->where('document_type_id', '07')
                                                            ->whereIn('state_type_id', ['01','03','05','07','13'])
                                                            ->where('currency_type_id', 'USD')
                                                            ->where('date_of_issue', $d_start)
                                                            ->map(function ($item, $key) {
                                                                return $item->total * $item->exchange_rate_sale;
                                                            })->sum();


            $d_total = $document_total_pen + $document_total_usd;
            $d_total_note_credit = $document_total_note_credit_pen + $document_total_note_credit_usd;

            $document_total = round($d_total - $d_total_note_credit,2);

            $documents_array[$d_start->format('d').'d'] = $document_total;

            $total_array[$d_start->format('d').'d'] = round($sale_note_total + $document_total ,2);

            $d_start = $d_start->addDay();
        }

        return compact('sale_notes_array', 'documents_array', 'total_array', 'total_orders');
    }



    private function getDocumentsByHours($sale_notes, $documents)
    {
        $sale_notes_array = [];
        $documents_array = [];
        $total_array = [];
        $document_total = 0;
        $document_total_note_credit = 0;

        $h_start = 0;
        $h_end = 23;

        for ($h = $h_start; $h <= $h_end; $h++)
        {
            $h_format = str_pad($h, 2, '0', STR_PAD_LEFT);

            //SALE NOTE
            $sale_note_total_pen = 0;
            $sale_note_total_col_usd = [];
            $sale_note_total_usd = 0;

            $sale_note_total_pen = $sale_notes->filter(function ($row) use($h_format) {
                return substr($row->time_of_issue, 0, 2) === $h_format;
            })->where('currency_type_id', 'PEN')->sum('total');

            $sale_note_total_col_usd = $sale_notes->filter(function ($row) use($h_format) {
                return substr($row->time_of_issue, 0, 2) === $h_format;
            })->where('currency_type_id', 'USD');

            foreach ($sale_note_total_col_usd as $sn) {
                $sale_note_total_usd += $sn->total * $sn->exchange_rate_sale;
            }

            $sale_note_total = $sale_note_total_pen + $sale_note_total_usd;
            $sale_notes_array[$h_format.'h'] = round($sale_note_total, 2);

            //SALE NOTE


            //DOCUMENT
            $document_total_pen = 0;
            $document_total_col_usd = [];
            $document_total_usd = 0;
            $document_total_nc_col_usd = [];
            $document_total_note_credit_usd = 0;

            $document_total_pen = $documents->filter(function ($row) use($h_format) {
                return substr($row->time_of_issue, 0, 2) === $h_format;
            })->whereIn('state_type_id', ['01','03','05','07','13'])->where('currency_type_id', 'PEN')->whereIn('document_type_id', ['01','03','08'])->sum('total');

            $document_total_col_usd = $documents->filter(function ($row) use($h_format) {
                return substr($row->time_of_issue, 0, 2) === $h_format;
            })->whereIn('state_type_id', ['01','03','05','07','13'])->where('currency_type_id', 'USD')->whereIn('document_type_id', ['01','03','08']);

            foreach ($document_total_col_usd as $doc) {
                $document_total_usd += $doc->total * $doc->exchange_rate_sale;
            }

            //NC
            $document_total_note_credit_pen = $documents->filter(function ($row) use($h_format) {
                return substr($row->time_of_issue, 0, 2) === $h_format;
            })->whereIn('state_type_id', ['01','03','05','07','13'])->where('document_type_id', '07')->where('currency_type_id', 'PEN')->sum('total');

            $document_total_nc_col_usd = $documents->filter(function ($row) use($h_format) {
                return substr($row->time_of_issue, 0, 2) === $h_format;
            })->whereIn('state_type_id', ['01','03','05','07','13'])->where('document_type_id', '07')->where('currency_type_id', 'USD');

            foreach ($document_total_nc_col_usd as $docnc) {
                $document_total_note_credit_usd += $docnc->total * $docnc->exchange_rate_sale;
            }

            $d_total = $document_total_pen + $document_total_usd;
            $d_total_nc = $document_total_note_credit_pen + $document_total_note_credit_usd;

            $document_total = $d_total - $d_total_nc;
            //DOCUMENT

            $documents_array[$h_format.'h'] = round($document_total, 2);

            $total_array[$h_format.'h'] = round($sale_note_total + $document_total,2);
        }

        return compact('sale_notes_array', 'documents_array', 'total_array');
    }



    public function items_woo()
    {

        $items = Item::whereHasInternalId()
                    // ->whereNotIsSet()
                    ->whereIsActive()
                    ->orderBy('description')
                    ->take(20)
                    ->get()
                    ->transform(function($row){

                return [
                    'sku' => $row->internal_id,
                    'qty' => $row->stock,
                    'name' => $row->description,
                    'price' => number_format( $row->sale_unit_price, 3),
                ];
            });


        return $items;


    }


//anulacion de nota de venta
    public function anulateNote($id)
    {

        DB::connection('tenant')->transaction(function () use ($id) {

            $obj =  SaleNote::find($id);
            $obj->state_type_id = 11;
            $obj->save();

            // $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
            $warehouse = Warehouse::where('establishment_id',$obj->establishment_id)->first();

            foreach ($obj->items as $sale_note_item) {

                // voided sets
                $this->voidedSaleNoteItem($sale_note_item, $warehouse);
                // voided sets

                //habilito las series
                // ItemLot::where('item_id', $item->item_id )->where('warehouse_id', $warehouse->id)->update(['has_sale' => false]);
                $this->voidedLots($sale_note_item);

            }

        });

        return [
            'success' => true,
            'message' => 'N. Venta anulada con éxito'
        ];


    }


    public function voidedSaleNoteItem($sale_note_item, $warehouse)
    {

        $warehouse_id = ($sale_note_item->warehouse_id) ? $sale_note_item->warehouse_id : $warehouse->id;

        if(!$sale_note_item->item->is_set){

            $presentationQuantity = (!empty($sale_note_item->item->presentation)) ? $sale_note_item->item->presentation->quantity_unit : 1;

            $sale_note_item->sale_note->inventory_kardex()->create([
                'date_of_issue' => date('Y-m-d'),
                'item_id' => $sale_note_item->item_id,
                'warehouse_id' => $warehouse_id,
                'quantity' => $sale_note_item->quantity * $presentationQuantity,
            ]);

            $wr = ItemWarehouse::where([['item_id', $sale_note_item->item_id],['warehouse_id', $warehouse_id]])->first();

            if($wr)
            {
                $wr->stock =  $wr->stock + ($sale_note_item->quantity * $presentationQuantity);
                $wr->save();
            }

        }else{

            $item = Item::findOrFail($sale_note_item->item_id);

            foreach ($item->sets as $it) {

                $ind_item  = $it->individual_item;
                $item_set_quantity  = ($it->quantity) ? $it->quantity : 1;
                $presentationQuantity = 1;
                $warehouse = $this->findWarehouse($sale_note_item->sale_note->establishment_id);
                $this->createInventoryKardexSaleNote($sale_note_item->sale_note, $ind_item->id , (1 * ($sale_note_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id, $sale_note_item->id);
                if(!$sale_note_item->sale_note->order_note_id) $this->updateStock($ind_item->id , (1 * ($sale_note_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id);

            }

        }

    }


    private function voidedLots($item){

        $i_lots_group = isset($item->item->lots_group) ? $item->item->lots_group:[];
        $lot_group_selecteds_filter = collect($i_lots_group)->where('compromise_quantity', '>', 0);
        $lot_group_selecteds =  $lot_group_selecteds_filter->all();

        if(count($lot_group_selecteds) > 0){

            foreach ($lot_group_selecteds as $lt) {
                $lot = ItemLotsGroup::find($lt->id);
                $lot->quantity = $lot->quantity + $lt->compromise_quantity;
                $lot->save();
            }

        }

        if(isset($item->item->lots)){
            foreach ($item->item->lots as $it) {
                if($it->has_sale == true){
                    $ilt = ItemLot::find($it->id);
                    $ilt->has_sale = false;
                    $ilt->save();
                }
            }
        }
    }
 
   
    private function sync_products_send($item){

        $i_lots_group = isset($item->item->lots_group) ? $item->item->lots_group:[];
        $lot_group_selecteds_filter = collect($i_lots_group)->where('compromise_quantity', '>', 0);
        $lot_group_selecteds =  $lot_group_selecteds_filter->all();

        if(count($lot_group_selecteds) > 0){

            foreach ($lot_group_selecteds as $lt) {
                $lot = ItemLotsGroup::find($lt->id);
                $lot->quantity = $lot->quantity + $lt->compromise_quantity;
                $lot->save();
            }

        }

        if(isset($item->item->lots)){
            foreach ($item->item->lots as $it) {
                if($it->has_sale == true){
                    $ilt = ItemLot::find($it->id);
                    $ilt->has_sale = false;
                    $ilt->save();
                }
            }
        }
    }
    
    private function sync_products_receive($item){

        $i_lots_group = isset($item->item->lots_group) ? $item->item->lots_group:[];
        $lot_group_selecteds_filter = collect($i_lots_group)->where('compromise_quantity', '>', 0);
        $lot_group_selecteds =  $lot_group_selecteds_filter->all();

        if(count($lot_group_selecteds) > 0){

            foreach ($lot_group_selecteds as $lt) {
                $lot = ItemLotsGroup::find($lt->id);
                $lot->quantity = $lot->quantity + $lt->compromise_quantity;
                $lot->save();
            }

        }

        if(isset($item->item->lots)){
            foreach ($item->item->lots as $it) {
                if($it->has_sale == true){
                    $ilt = ItemLot::find($it->id);
                    $ilt->has_sale = false;
                    $ilt->save();
                }
            }
        }
    }
 
   
    private function DescriptionById($id, $type)
    {
        switch ($type) {
            case 'dis':
                $district = District::where('id', $id)->first();
                if ($district) {
                    return $district->description;
                }
                break;
            case 'prov':
                $province = Province::where('id', $id)->first();
                if ($province) {
                    return $province->description;
                }
                break;
            case 'dep':
                $department = Department::where('id', $id)->first();
                if ($department) {
                    return $department->description;
                }
                break;
        }
        return 'LIMA';
    }

    public function dispatches_email(Request $request)
    {
        $record = Dispatch::find($request->input('id'));
        $customer_email = $request->input('customer_email');
        $email = $customer_email;
        $mailable =new DispatchEmail($record);
        $id =  $request->input('id');
        $model = __FILE__.";;".__LINE__;
        $sendIt = EmailController::SendMail($email, $mailable, $id, 4);
        return [
            'success' => true
        ];
    }


    public function dispatches_list()
    {
        $records = Dispatch::orderBy('id', 'desc')->take(20)->get();
        $records = new DispatchCollection($records);

        return $records;
    }

    public function dispatches_id(Request $request)
    {

        $records = Dispatch::where('external_id', '=', $request->id)->get()->transform(function($row) {
                    
        if($row->qr_text) {
            $qrCode = new QrCodeGenerate();
            $row->qr = $qrCode->displayPNGBase64($row->qr_text);
        }
            return [
                'id' => $row->id,
                'external_id' => $row->external_id,
                'series' => $row->series,
                'number' => $row->number,
                'document_type_id' => $row->document_type_id,
                'document_type' => $row->document_type_id == '09' ? "GUIA ELECTRONICA" : '',
                'number2' => ''.$row->series.'-'.$row->number,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'time_of_issue' => $row->time_of_issue,
                'state_type_description' => $row->state_type->description,
                'document_type' => $row->document_type->description,
                'customer_name' => $row->customer->name,
                'customer_email' => $row->customer->email,
                'customer_number' => $row->customer->number,
                'customer_address' => $row->customer->address,
                'customer_address_dis' => $row->customer->district->description,
                'customer_address_prov' => $row->customer->province->description,
                'customer_address_dev' => $row->customer->department->description,
                'customer_address_dis_id' => $row->customer->district->id,
                'customer_address_prov_id' => $row->customer->province->id,
                'customer_address_dev_id' => $row->customer->department->id,
                'customer_telephone' => $row->customer->telephone,
                'customer_trade_name' => $row->customer->trade_name,
                'customer_document' => $row->customer->identity_document_type->description,
                'seller_name' => $row->user->name,
                'seller_name' => $row->user->name,
                'seller_name' => $row->user->name,
                'doc_afectado_number' => $row->data_affected_document->number,
                'doc_afectado_series' => $row->data_affected_document->series,
                'doc_afectado_document_type' => $row->data_affected_document->document_type_id == '01' ? "FACTURA ELECTRÓNICA" : 'BOLETA DE VENTA ELECTRÓNICA',
                'doc_afectado_document_type_id' => $row->data_affected_document->document_type_id,
                'observaciones' => $row->observations,
                'motivo' => $row->transfer_reason_description,
                'transfer_reason' => $row->transfer_reason_type->description,
                'fecha_envio' => $row->date_of_shipping->format('Y-m-d'),
                'unit_type_id' => $row->unit_type_id,
                'total_weight' => $row->total_weight,
                'packages_number' => $row->packages_number,
                'origin_address' => $row->origin->address,
                'origin_location_id' => $row->origin->location_id,
                'origin_dis' => $this->DescriptionById($row->origin->location_id, 'dis'),
                'origin_prov' => $this->DescriptionById(substr($row->origin->location_id, 0, -2), 'prov'),
                'origin_dep' => $this->DescriptionById(substr($row->origin->location_id, 0, -4), 'dep'),
                'delivery_address' => $row->delivery->address,
                'delivery_location_id' => $row->delivery->location_id,
                'delivery_dis' => $this->DescriptionById($row->delivery->location_id, 'dis'),
                'delivery_prov' => $this->DescriptionById(substr($row->delivery->location_id, 0, -2), 'prov'),
                'delivery_dep' => $this->DescriptionById(substr($row->delivery->location_id, 0, -4), 'dep'),
                'dispatcher_dispatcher' => $row->dispatcher->name,
                'dispatcher_number' => $row->dispatcher->number,
                'dispatcher_doc' => $row->dispatcher->identity_document_type_id,
                'driver_dispatcher' => isset($row->driver->name) ? $row->driver->name : 'Varios',
                'driver_number' => $row->driver->number,
                'driver_doc' => $row->driver->identity_document_type_id,
                'transport_mode' => $row->transport_mode_type->description,
                'items' => collect($row->items)->transform(function($row) {
                        return [
                            'unit_type_id' => $row->item->unit_type_id,
                            'internal_id' => $row->item->internal_id,
                            'description' => $row->item->description,
                            'quantity' => $row->quantity,
                        ];
                    }),
                'qr' => $row->qr,     
            ];
        });

        return [
            'success' => true,
            'dispatch' => $records[0]
        ];
    }
/////////crear json para guia aquiiiii
    public function dispatches_create(Request $request){

    $document = Document::where("external_id", "=", $request->guide_external_id)->first();

    $data_dispatch_array = [
        "serie_documento"       => $request->serie,
        "numero_documento"      => "#",
        "fecha_de_emision"      => date("Y-m-d"),
        "hora_de_emision"       => date("h:m:s"),
        "codigo_tipo_documento" => "09",

        "datos_del_emisor" => [
            "codigo_pais"                   => $document->establishment->country_id,
            "ubigeo"                        => $document->establishment->district_id,
            "direccion"                     => $document->establishment->address,
            "correo_electronico"            => $document->establishment->email,
            "telefono"                      => $document->establishment->telephone,
            "codigo_del_domicilio_fiscal"   => $document->establishment->code
        ],

        "datos_del_cliente_o_receptor" => [
            "codigo_tipo_documento_identidad"       => $document->customer->identity_document_type_id,
            "numero_documento"                      => $document->customer->number,
            "apellidos_y_nombres_o_razon_social"    => $document->customer->name,
            "nombre_comercial"                      => $document->customer->trade_name,
            "codigo_pais"                           => $document->customer->country_id,
            "ubigeo"                                => $document->customer->district_id,
            "direccion"                             => $document->customer->address,
            "correo_electronico"                    => $document->customer->email,
            "telefono"                              => $document->customer->telephone
        ],


        "observaciones"                 => $request->observaciones,
        "codigo_modo_transporte"        => $request->transport_mode_type_id,
        "codigo_motivo_traslado"        => $request->transfer_reason_type_id,
        "descripcion_motivo_traslado"   => $request->motivo,
        "fecha_de_traslado"             => date("Y-m-d"),
        "codigo_de_puerto"              => "",
        "indicador_de_transbordo"       => false,
        "unidad_peso_total"             => $request->unittype,
        "peso_total"                    => $request->peso,
        "numero_de_bultos"              => count($document->items),
        "numero_de_contenedor"          => "",

        "direccion_partida" => [
            "ubigeo"                        => $document->establishment->district_id,
            "direccion"                     => $document->establishment->address,
            "codigo_del_domicilio_fiscal"   => $document->establishment->code
        ],

        "direccion_llegada" => [
            "ubigeo"                        => $request->district_id,
            "direccion"                     => $request->address,
            "codigo_del_domicilio_fiscal"   => "0000"
        ],

//////////////////////aqui chofer o transportista
        // $datos_transporte,

//////////////////////aqui chofer o transportista

        "numero_de_placa"           => isset($request->license_plate) ? $request->license_plate : "",

        "items"                     => collect($document->items)->transform(function($row) 
                    {
                        return [
                            'codigo_interno' => $row->item->internal_id,
                            'cantidad' => $row->quantity,
                        ];
                    }),

        "documento_afectado" => [
            "serie_documento"       => $document->series,
            "numero_documento"      => $document->number,
            "codigo_tipo_documento" => $document->document_type_id
        ],

    ];

// $productos[]

// $datos_transporte = null;

    if ($request->transport_mode_type_id=="01") {

        $dispatcher = Dispatcher::where("id", $request->dispatcher_id)->first();

        $data_dispatch_array["transportista"] = [
            "codigo_tipo_documento_identidad"       => $dispatcher->identity_document_type_id,
            "numero_documento"                      => $dispatcher->number,
            "apellidos_y_nombres_o_razon_social"    => $dispatcher->name,
            "numero_mtc"                            => $dispatcher->number_mtc,
        ];

    }

    if ($request->transport_mode_type_id=="02") {

        $driver = Driver::where("id", "=", $request->driver_id)->first();

        $data_dispatch_array["chofer"] = [
            "codigo_tipo_documento_identidad"   => $driver->identity_document_type_id,
            "numero_documento"                  => $driver->number,
            "nombres"                           => $driver->name,
            "apellidos"                         => $driver->name,
            "numero_licencia"                   => $driver->license,
        ];

        $transport = Transport::where("id", "=", $request->transport_id)->first();

        $data_dispatch_array["vehiculo"] = [
            "numero_de_placa"     => $transport->plate_number,
            "modelo"    => $transport->model,
            "marca"     => $transport->brand,
        ];

    }


// return $data_dispatch_array;


    $data_dispatch = json_encode($data_dispatch_array);

// return $data_dispatch;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => ''.url("/api/dispatches").'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => 2,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $data_dispatch,
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.auth()->user()->api_token.'',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$dataFinal = json_decode($response, true);


// return $response;
        // $records = Dispatch::where('external_id', '=', $dataFinal["data"]["external_id"])->get();
        if ($dataFinal != null) {
            if ($dataFinal["success"]==true) {

                $records = Dispatch::where('external_id', '=', $dataFinal["data"]["external_id"])->get()->transform(function ($row) {

                    return [
                        'id' => $row->id,
                        'external_id' => $row->external_id,
                        'series' => $row->series,
                        'number' => $row->number,
                        'document_type_id' => $row->document_type_id,
                        'document_type' => $row->document_type_id == '09' ? "GUIA ELECTRONICA" : '',
                        'number2' => '' . $row->series . '-' . $row->number,
                        'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                        'time_of_issue' => $row->time_of_issue,
                        'state_type_description' => $row->state_type->description,
                        'customer_name' => $row->customer->name,
                        'customer_email' => $row->customer->email,
                        'customer_number' => $row->customer->number,
                        'customer_address' => $row->customer->address,
                        'customer_address_dis' => $row->customer->district->description,
                        'customer_address_prov' => $row->customer->province->description,
                        'customer_address_dev' => $row->customer->department->description,
                        'customer_address_dis_id' => $row->customer->district->id,
                        'customer_address_prov_id' => $row->customer->province->id,
                        'customer_address_dev_id' => $row->customer->department->id,
                        'customer_telephone' => $row->customer->telephone,
                        'customer_trade_name' => $row->customer->trade_name,
                        'customer_document' => $row->customer->identity_document_type->description,
                        'seller_name' => $row->user->name,
                        'doc_afectado_number' => $row->data_affected_document->number,
                        'doc_afectado_series' => $row->data_affected_document->series,
                        'doc_afectado_document_type' => $row->data_affected_document->document_type_id == '01' ? "FACTURA ELECTRÓNICA" : 'BOLETA DE VENTA ELECTRÓNICA',
                        'doc_afectado_document_type_id' => $row->data_affected_document->document_type_id,
                        'observaciones' => $row->observations,
                        'motivo' => $row->transfer_reason_description,
                        'transfer_reason' => $row->transfer_reason_type->description,
                        'fecha_envio' => $row->date_of_shipping->format('Y-m-d'),
                        'unit_type_id' => $row->unit_type_id,
                        'total_weight' => $row->total_weight,
                        'packages_number' => $row->packages_number,
                        'origin_address' => $row->origin->address,
                        'origin_location_id' => $row->origin->location_id,
                        'origin_dis' => $this->DescriptionById($row->origin->location_id, 'dis'),
                        'origin_prov' => $this->DescriptionById(substr($row->origin->location_id, 0, -2), 'prov'),
                        'origin_dep' => $this->DescriptionById(substr($row->origin->location_id, 0, -4), 'dep'),
                        'delivery_address' => $row->delivery->address,
                        'delivery_location_id' => $row->delivery->location_id,
                        'delivery_dis' => $this->DescriptionById($row->delivery->location_id, 'dis'),
                        'delivery_prov' => $this->DescriptionById(substr($row->delivery->location_id, 0, -2), 'prov'),
                        'delivery_dep' => $this->DescriptionById(substr($row->delivery->location_id, 0, -4), 'dep'),
                        'dispatcher_dispatcher' => isset($row->dispatcher_data->name) ? $row->dispatcher_data->name : null,
                        'dispatcher_number' => isset($row->dispatcher_data->number) ? $row->dispatcher_data->number : null,
                        'dispatcher_doc' => isset($row->dispatcher_data->identity_document_type_id) ? $row->dispatcher_data->identity_document_type_id : null,
                        'driver_dispatcher' => isset($row->driver_data->lastnames) ? $row->driver_data->lastnames : null,
                        'driver_number' => isset($row->driver_data->number) ? $row->driver_data->number : null,
                        'driver_doc' => isset($row->driver_data->identity_document_type_id) ? $row->driver_data->identity_document_type_id : null,
                        'transport_plate' => isset($row->transport_data->plate_number) ? $row->transport_data->plate_number : null,
                        'transport_model' => isset($row->transport_data->model) ? $row->transport_data->model : null,
                        'transport_brand' => isset($row->transport_data->brand) ? $row->transport_data->brand : null,
                        'transport_mode' => $row->transport_mode_type->description,
                        'items' => collect($row->items)->transform(function ($row) {
                            return [
                                'unit_type_id' => $row->item->unit_type_id,
                                'internal_id' => $row->item->internal_id,
                                'description' => $row->item->description,
                                'quantity' => $row->quantity,
                            ];
                        }),

                    ];
                });

                return [
                    'success' => true,
                    'dispatch' => $records[0],
                    'response' => $dataFinal,
                    'jsonSend' => $data_dispatch
                ];
            }else{
                // $dataFinal = array("success"=>false, "message"=> substr($response, 61, 60));
                $dataFinal = array("success"=>false, "jsonSend"=> $data_dispatch, "message"=> $response);
                return [
                    '1' => false,
                    'success' => false,
                    'response' => $dataFinal
                ];
            }
        }else{
            // $dataFinal = array("success"=>false, "message"=> substr($response, 61, 60));
            $dataFinal = array("success"=>false, "jsonSend"=> $data_dispatch, "message"=> $response);
            return [
                '2' => false,
                'success' => false,
                'response' => $dataFinal
            ];

        }

    }
 
    //enviar cpe por medio de id

    private function getStateTypeDescription($id)
    {
        return StateType::find($id)->description;
    }
    
     public function send($document_id)
    {
        if ($document_id) {
            $external_id = $document_id;
            $document = Document::where('external_id', $external_id)->first();
            if (!$document) {
                return [
                    'success' => false,
                    'message' => 'El documento con código externo {$external_id}, no se encuentra registrado.'
                ];
            }
            if ($document->group_id !== '01') {
                return [
                    'success' => false,
                    'message' => 'El tipo de documento {$document->document_type_id} es inválido, no es posible enviar.'
                ];
            }
            $fact = new Facturalo();
            $fact->setDocument($document);
            $fact->loadXmlSigned();
            $fact->onlySenderXmlSignedBill();
            $response = $fact->getResponse();
            return [
                'success' => true,
                'data' => [
                    'number' => $document->number_full,
                    'filename' => $document->filename,
                    'external_id' => $document->external_id,
                    'state_type_id' => $document->state_type_id,
                    'state_type_description' => $this->getStateTypeDescription($document->state_type_id),
                ],
                'links' => [
                    'cdr' => $document->download_external_cdr,
                ],
                'response' => array_except($response, 'sent'),
            ];
        }else{
            
            return [
                'success' => false,
                'message' => 'No existe el documento con el external_id enviado.'
            ];
        }
    }

// reporte general exportar account/format/donwload

    public function download_report(Request $request)
    {
        $type = $request->input('type');
        $month = $request->input('month');

        $d_start = Carbon::parse($month.'-01')->format('Y-m-d');
        $d_end = Carbon::parse($month.'-01')->endOfMonth()->format('Y-m-d');

            $company = $this->getCompany();

// dd($company);
            $filename = 'Reporte_Formato_Compras_'.date('YmdHis');
            $data = [
                'period' => $month,
                'company' => $company,
                'params' => $request->all(),
            ];

            if ($type === 'sale') 
            {
                $filename = 'Reporte_Formato_Ventas_'.date('YmdHis');
                $data['records'] = $this->getSaleDocuments($d_start, $d_end);
                $reportFormatSaleExport = new ReportFormatSaleExport();
                $reportFormatSaleExport->data($data);
                // return $reportFormatSaleExport->view();
                return $reportFormatSaleExport
                    ->download($filename.'.xlsx');
            }
            else if($type === 'garage-gll')
            {
                
                $data['records'] = $this->getSaleGarageGll($d_start, $d_end);
                return (new ReportFormatSaleGarageGllExport())->data($data)->download('Reporte_Formato_Ventas_Grifo'.date('YmdHis').'.xlsx');
            }

            $data['records'] = $this->getPurchaseDocuments($d_start, $d_end);

            $reportFormatPurchaseExport = new ReportFormatPurchaseExport();
            $reportFormatPurchaseExport->data($data);
            // return $reportFormatPurchaseExport->view();
            return $reportFormatPurchaseExport
                ->download($filename.'.xlsx');

    }

    /**
     * @return array
     */
    public function getCompany() {
        $company = Company::query()->first();

        return [
            'name' => $company->name,
            'number' => $company->number,
        ];
    }

    /**
     * @param                                               $d_start
     * @param                                               $d_end
     *
     * @return \App\Models\Tenant\Document[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|mixed
     */
    private function getSaleDocuments($d_start, $d_end) {
        $data = Document::query()
                        ->whereBetween('date_of_issue', [$d_start, $d_end])
            // ->whereIn('document_type_id', ['01', '03'])
            // ->whereIn('currency_type_id', ['PEN', 'USD'])
                        ->orderBy('series')
                        ->orderBy('number')
                        ->get()
                        ->transform(function ($row) {
                            /** @var \App\Models\Tenant\Document $row */
                            $row = $this->AdjustValueToReportByDocumentTypeAndStateType($row);
                            $note_affected_document = new Document();
                            if (!empty($row->note)) {
                                if (!empty($row->note->affected_document)) {
                                    $note_affected_document = $row->note->affected_document;
                                    $row = $this->AdjustValueToReportByDocumentTypeAndStateType($row, 1);
                                } elseif (!empty($row->note->data_affected_document)) {
                                    $data_affected_document = (array)$row->note->data_affected_document;
                                    $note_affected_document = Document::where([
                                                                                  'number'           => $data_affected_document['number'],
                                                                                  'series'           => $data_affected_document['series'],
                                                                                  'document_type_id' => $data_affected_document['document_type_id'],
                                                                              ])->first();
                                    if (!empty($note_affected_document)) {
                                        $row = $this->AdjustValueToReportByDocumentTypeAndStateType($row, 1);
                                    } else {
                                        $note_affected_document = new Document($data_affected_document);
                                        $row = $this->AdjustValueToReportByDocumentTypeAndStateType($row);

                                    }
                                }
                            }
                            $symbol = $row->currency_type->symbol;

                            $total = round($row->total, 2);
                            $total_taxed = round($row->total_taxed, 2);
                            $total_igv = round($row->total_igv, 2);
                            $total_exonerated = $row->total_exonerated;
                            $total_unaffected = $row->total_unaffected;
                            $total_exportation = $row->total_exportation;
                            $total_isc = $row->total_isc;

                            $exchange_rate_sale = $row->exchange_rate_sale;
                            $currency_type_id = $row->currency_type_id;
                            $format_currency_type_id = $row->currency_type_id;

                            // aplicar conversion al tipo de cambio
                            if ($row->currency_type_id === 'USD') 
                            {
                                $total = round($row->generalConvertValueToPen($total, $exchange_rate_sale), 2);
                                $total_taxed = round($row->generalConvertValueToPen($total_taxed, $exchange_rate_sale), 2);
                                $total_igv = round($row->generalConvertValueToPen($total_igv, $exchange_rate_sale), 2);
                                $total_exonerated = round($row->generalConvertValueToPen($total_exonerated, $exchange_rate_sale), 2);
                                $total_unaffected = round($row->generalConvertValueToPen($total_unaffected, $exchange_rate_sale), 2);
                                $total_exportation = round($row->generalConvertValueToPen($total_exportation, $exchange_rate_sale), 2);
                                $total_isc = round($row->generalConvertValueToPen($total_isc, $exchange_rate_sale), 2);
                                $symbol = 'S/';
                                $format_currency_type_id = 'PEN';
                            }


                            return [
                                'date_of_issue'                      => $row->date_of_issue->format('d/m/Y'),
                                'document_type_id'                   => $row->document_type_id,
                                'state_type_id'                      => $row->state_type_id,
                                'state_type_description'             => $row->state_type->description,
                                'series'                             => $row->series,
                                'number'                             => $row->number,
                                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                                'customer_number'                    => $row->customer->number,
                                'customer_name'                      => $row->customer->name,
                                'total_exportation'                  => $total_exportation,
                                'total_taxed'                        => $total_taxed,
                                'total_exonerated'                   => $total_exonerated,
                                'total_unaffected'                   => $total_unaffected,
                                'total_plastic_bag_taxes'            => $row->total_plastic_bag_taxes,
                                'total_isc'                          => $total_isc,
                                'total_igv'                          => $total_igv,
                                'total'                              => $total,
                                'observation'=>$row->additional_information,
                                // 'selected_currency'                              => $currencyRequested,
                                'exchange_rate_sale'                 => $exchange_rate_sale,
                                'currency_type_symbol'               => $symbol,
                                'format_currency_type_id'            => $format_currency_type_id,
                                'affected_document'                  => (in_array($row->document_type_id,
                                                                                  ['07', '08'])) ? [
                                    'date_of_issue'    => !empty($note_affected_document->date_of_issue)
                                        ? $note_affected_document->date_of_issue->format('d/m/Y') : null,
                                    'document_type_id' => $note_affected_document->document_type_id,
                                    'series'           => $note_affected_document->series,
                                    'number'           => $note_affected_document->number,

                                ] : null,
                            ];
                        });

        return $data;

    }

    /**
     * Establece a 0 los totales para los documentos que se habiliten en $type_document_to_evalue
     * y que el status se encuentre en $type_document_to_evalue.
     *
     * Normalmente se evalua Factura electronica (01) y Boleta de venta electronica (03)
     * Si $is_affected es verdadero, evalua tambien nota de credito (07) y debito (08)
     *
     * @param Document $row
         * @param bool     $is_affected
     *
     * @return Document
     */
    public function AdjustValueToReportByDocumentTypeAndStateType(Document $row, $is_affected = false){

        $document_type_id = $row->document_type_id;
        $state_type_id = $row->state_type_id;
        $type_document_to_evalue = [
            '01',//    FACTURA ELECTRÓNICA
            '03',//    BOLETA DE VENTA ELECTRÓNICA
            //'07',//    NOTA DE CRÉDITO
            //'08',//    NOTA DE DÉBITO
            //'09',//    GUIA DE REMISIÓN REMITENTE
            //'20',//    COMPROBANTE DE RETENCIÓN ELECTRÓNICA
            //'31',//    Guía de remisión transportista
            //'40',//    COMPROBANTE DE PERCEPCIÓN ELECTRÓNICA
            //'71',//    Guia de remisión remitente complementaria
            //'72',//   Guia de remisión transportista complementaria
            //'GU75',// GUÍA
            //'NE76',// NOTA DE ENTRADA
            //'80',//   NOTA DE VENTA
            //'02',//   RECIBO POR HONORARIOS
            //'14',//   SERVICIOS PÚBLICOS
        ];
        if($is_affected == true){
            $type_document_to_evalue = [
                '01',//    FACTURA ELECTRÓNICA
                '03',//    BOLETA DE VENTA ELECTRÓNICA
                '07',//    NOTA DE CRÉDITO
                '08',//    NOTA DE DÉBITO
            ];
        }
        $document_state_to_evalue = [
            // '01',//  Registrado
            // '03',//  Enviado
            // '05',//  Aceptado
            // '07',//  Observado
            '09',// Rechazado
            '11',// Anulado
            // '13',//  Por anular
        ];
        if (
            in_array($document_type_id, $type_document_to_evalue) &&
            in_array($state_type_id, $document_state_to_evalue)
        ) {
            $row->total_exportation = 0 ;
            $row->total_taxed = 0 ;
            $row->total_exonerated = 0 ;
            $row->total_unaffected = 0 ;
            $row->total_plastic_bag_taxes = 0 ;
            $row->total_igv = 0 ;
            $row->total = 0 ;
        }
        return $row;
    }

        /**
         * @param                                               $d_start
         * @param                                               $d_end
         *
         * @return \App\Models\Tenant\Purchase[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|mixed
         */
        private function getPurchaseDocuments($d_start, $d_end) {
            $data = Purchase::query()
            ->whereBetween('date_of_issue', [$d_start, $d_end])
            ->whereIn('document_type_id', ['01', '03', '14'])
                // ->whereIn('currency_type_id', ['PEN','USD'])
            ->orderBy('series')
            ->orderBy('number')
                            ->get()
                            ->transform(function ($row) {
                                /** @var \App\Models\Tenant\Purchase $row */
                                $symbol = $row->currency_type->symbol;
                                $currency_type_id = $row->currency_type_id;


                                $total = round($row->total, 2);
                                $total_taxed = round($row->total_taxed, 2);
                                $total_igv = round($row->total_igv, 2);
                                $exchange_rate_sale = $row->exchange_rate_sale;
                                // $exchange_rate_sale = round($row->exchange_rate_sale, 2);
                                $total_exportation = round($row->total_exportation, 2);
                                $total_exonerated = round($row->total_exonerated, 2);
                                $total_unaffected = round($row->total_unaffected, 2);
                                $total_isc = round($row->total_isc, 2);

                return [
                    'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                    'date_of_due' => $row->date_of_due->format('d/m/Y'),
                    'state_type_id' => $row->state_type_id,
                    'document_type_id' => $row->document_type_id,
                    'series' => $row->series,
                    'number' => $row->number,
                    'supplier_identity_document_type_id' => $row->supplier->identity_document_type_id,
                    'supplier_number' => $row->supplier->number,
                    'supplier_name' => $row->supplier->name,
                                    'total_exportation'                  => $total_exportation,
                                    'total_exonerated'                   => $total_exonerated,
                                    'total_unaffected'                   => $total_unaffected,
                                    'total_isc'                          => $total_isc,
                                    'total_taxed'                        => $total_taxed,
                                    'total_igv'                          => $total_igv,
                                    'total'                              => $total,
                                    'exchange_rate_sale'                 => $exchange_rate_sale,
                                    'currency_type_symbol'               => $symbol,
                ];
            });
            return $data;

    }
//final de reportes


    public function statusTicket($ticket)
    {
        $data = (new ServiceDispatchController())->statusTicket($ticket);

        if(sizeof($data["res"]["cdr_data"])) {
            $qrCode = new QrCodeGenerate();
            $data["qr"] = $qrCode->displayPNGBase64($data["res"]["cdr_data"]["qr_text"]);
        }
            
        return $data;
    }

    public function sendDispatch($external_id)
    {

        $res = ((new ServiceDispatchController())->send($external_id));

        return $res;
        
    }


}
