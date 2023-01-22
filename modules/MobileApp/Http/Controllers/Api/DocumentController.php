<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Tenant\{
    Document,
    PaymentCondition,
    Series,
    PaymentMethodType,
    Person,
};
use App\Http\Resources\Tenant\DocumentCollection;
use App\Models\Tenant\StateType;
use App\Http\Resources\Tenant\DocumentResource;
use App\Models\Tenant\Catalogs\{
    DocumentType,
    ChargeDiscountType
};
use Modules\Finance\Traits\FinanceTrait;
use Modules\MobileApp\Http\Requests\Api\SendDocumentWhatsappRequest;
use Modules\WhatsAppApi\Services\WhatsAppCloudApi;
use Modules\Document\Helpers\DocumentHelper;


class DocumentController extends Controller
{
      
    use FinanceTrait;
    
    /**
     *
     * @return array
     */
    public function record($id)
    {
        return new DocumentResource(Document::findOrFail($id));
    }

    
    /**
     *
     * @return array
     */
    public function tables()
    {
        $state_types = StateType::getDataApiApp();

        return compact('state_types');
    }
    

    /**
     *
     * Modo POS App
     * 
     * @return array
     */
    public function getTablesSaleDetail()
    {
        $affectation_igv_types = app(ItemController::class)->table('affectation_igv_types');

        $document_types = $this->table('document_types');

        $item_discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();

        return compact('affectation_igv_types', 'document_types', 'item_discount_types');
    }

    
    /**
     * Tablas individuales
     *
     * @param  string $table
     * @return array
     */
    public function table($table)
    {
        $data = [];

        switch ($table) 
        {
            case 'document_types':
                $data = DocumentType::onlySaleDocuments()->get();
                break;
            
        }

        return $data;
    }

    
    /**
     *
     * Modo POS App
     * 
     * @return array
     */
    public function getTablesSalePayment()
    {
        $payment_method_types = PaymentMethodType::get();
        $payment_destinations = $this->getPaymentDestinations();
        $payment_conditions = PaymentCondition::selectGeneralColumns()->get();

        $customers = Person::filterApiInitialCustomers()->get()
                            ->transform(function($row) {
                                return $row->getApiRowResource();
                            });

        $series = Series::onlySaleDocuments()->get()
                        ->transform(function($row) {
                            return $row->getApiRowResource();
                        });

        return compact(
            'series', 
            'payment_conditions', 
            'payment_method_types',
            'payment_destinations', 
            'customers'
        );
    }

    
    /**
     * 
     * Listado de documentos
     *
     * @param  Request $request
     * @return DocumentCollection
     */
    public function records(Request $request)
    {
        $records = Document::filterRecordsAppApi($request);

        return new DocumentCollection($records->latest()->take(config('tenant.items_per_page'))->get());
    }

    /**
     * 
     * Obtener notificaciones
     * 
     * Comprobantes enviados/por enviar
     * Comprobantes pendientes de rectificación
     *
     * @return array
     */
    public function getNotifications()
    {
        
        $documents_not_sent = Document::whereNotSent()->count();
        $documents_regularize_shipping = Document::whereRegularizeShipping()->count();

        return [
            'success' => true,
            'data' => [
                'documents_not_sent' => $documents_not_sent,
                'documents_regularize_shipping' => $documents_regularize_shipping,
            ]
        ];
    }

    
    /**
     * 
     * Enviar comprobante directo a Whatsapp (Texto/Archivo pdf) 
     *
     * @param  SendDocumentWhatsappRequest $request
     * @return array
     */
    public function sendDocumentToWhatsapp(SendDocumentWhatsappRequest $request)
    {
        $document_helper = new DocumentHelper();
        
        $model = $document_helper->getModelByDocumentType($request->document_type_id);
        $document = $document_helper->getDocumentDataForSendMessage($model, $request->id);
        $params = $document_helper->getParamsForAppSendMessage($request->phone_number, $request->format, $document);

        // dd($params, $model, $document);

        $whatsapp_cloud_api = new WhatsAppCloudApi();

        $send_text_message = $whatsapp_cloud_api->sendMessage($params);
        if(!$send_text_message['success']) return $send_text_message;

        $params['send_type'] = 'document';

        return $whatsapp_cloud_api->sendMessage($params);
    }


    


}
