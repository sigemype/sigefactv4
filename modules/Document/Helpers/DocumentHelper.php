<?php

namespace Modules\Document\Helpers;

use Exception;
use Carbon\Carbon;
use App\Models\Tenant\{
    Configuration,
    Document,
    SaleNote,
};
use Hyn\Tenancy\Environment;
use App\Models\System\Client;
use App\Traits\LockedEmissionTrait;


class DocumentHelper
{

    use LockedEmissionTrait;
    
    /**
     * Obtener fecha de ciclo de facturacion desde client (system), relacionado al tenant
     */
    public static function getStartBillingCycleFromSystem()
    {
        $tenancy = app(Environment::class);
        $hostname = $tenancy->hostname();
        $client = Client::select('start_billing_cycle')->where('hostname_id', $hostname->id)->first();

        return $client->start_billing_cycle;
    }

            
    /**
     * 
     * Validar si los documentos emitidos superan el limite permitido por el plan (ciclo facturacion)
     *
     * Usado en: 
     * App\Providers\LockedEmissionProvider
     * App\Http\Controllers\Tenant\DocumentController
     * 
     * @param  string $type
     * @return array
     */     
    public function exceedLimitDocuments($type = 'document')
    {
        /*
        $configuration = $configuration ?? Configuration::firstOrFail();

        //cantidad limite de documentos permitidos para emitir (0 = ilimitado)
        $limit_documents = $configuration->limit_documents;
        */

        //obtener limite desde el plan en bd admin
        $plan = $this->getClientPlan(['id', 'name', 'limit_documents', 'include_sale_notes_limit_documents']);

        //cantidad limite de documentos permitidos para emitir (0 = ilimitado)
        $limit_documents = $plan->limit_documents;

        if($limit_documents !== 0)
        {
            if($type === 'document' || ($type === 'sale-note' && $plan->includeSaleNotesLimitDocuments()))
            {
                
            //fecha de inicio del ciclo de facturacion
            $start_billing_cycle = self::getStartBillingCycleFromSystem();
            
            if($start_billing_cycle){

                //obtener fecha inicio y fin
                $start_end_date = self::getStartEndDateForFilterDocument($start_billing_cycle);
    
                //cantidad de documentos emitidos en el rango de fechas obtenido desde el ciclo de facturacion
                $quantity_documents = Document::whereBetween('date_of_issue', [ $start_end_date['start_date'], $start_end_date['end_date'] ])->count();

                if($plan->includeSaleNotesLimitDocuments())
                {
                    $quantity_documents += $this->getQuantitySaleNotesByDates($start_end_date['start_date']->format('Y-m-d'), $start_end_date['end_date']->format('Y-m-d'));
                }
    
                if($quantity_documents > $limit_documents)
                {
                    return [
                        'success' => true,
                        'message' => 'Ha superado el límite permitido para la emisión de comprobantes'
                    ];
                }

            }

            }
        }

        return [
            'success' => false,
            'message' => ''
        ];

    }


    /**
     * 
     * Obtener fecha de inicio y fin para filtrar documentos en base 
     * a la fecha de inicio del ciclo de facturacion (planes) del cliente
     *
     * Usado en: 
     * App\Http\Controllers\System\ClientController
     * 
     * @param  $start_billing_cycle
     * @return array
     */
    public static function getStartEndDateForFilterDocument($start_billing_cycle)
    { 
        
        $day_start_billing = date_format($start_billing_cycle, 'j');
        $day_now = (int) date('j');
        $end = Carbon::parse(date('Y-m-d'));
        // $day_now = 6;
        // $end = Carbon::parse(date('2022-01-06'));

        if ($day_now <= $day_start_billing) {

            $init = Carbon::parse(date('Y') . '-' . ((int)date('n') - 1) . '-' . $day_start_billing);

        } else {

            $init = Carbon::parse(date('Y') . '-' . ((int)date('n')) . '-' . $day_start_billing);
            
        }

        return [
            'start_date' => $init,
            'end_date' => $end,
        ];

    }

        
    /**
     * Obtener modelo por tipo de documento
     *
     * @param  string $document_type_id
     * @return string
     */
    public function getModelByDocumentType($document_type_id)
    {
        $model = null;

        switch ($document_type_id)
        {
            case '01':
            case '03':
                $model = Document::class;
                break;
            
            case '80':
                $model = SaleNote::class;
                break;
        }

        if(is_null($model)) throw new Exception('No se encontró un modelo para el tipo de documento.');

        return $model;
    }

    
    /**
     * 
     * Obtener documento para envio de mensaje por ws
     *
     * @param  string $model
     * @param  string $id
     * @return Document|SaleNote
     */
    public function getDocumentDataForSendMessage($model, $id)
    {
        return $model::filterDataForSendMessage()->findOrFail($id);
    }

    
    /**
     *
     *  Obtener parametros para envio de mensaje por ws
     *
     * @param  mixed $phone_number
     * @param  mixed $format
     * @param  mixed $document
     * @return void
     */
    public function getParamsForAppSendMessage($phone_number, $format, $document)
    {
        return [
            'send_type' => 'text',
            'phone_number' => $phone_number,
            'message' => "Su comprobante {$document->number_full} ha sido generado correctamente.",
            'document' => [
                'filename'=> "{$document->filename}.pdf",
                'link'=> $document->getUrlPrintByFormat($format)
            ]
        ];
    }
 
}
