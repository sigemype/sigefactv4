<?php

namespace Modules\ApiPeruDev\Http\Controllers;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Helpers\Xml\XmlFormat;
use App\CoreFacturalo\Template;
use App\Models\Tenant\Company;
use App\Models\Tenant\Dispatch;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ApiPeruDev\Helpers\CdrRead;
use Modules\ApiPeruDev\Helpers\ServiceDispatch;
use Modules\Store\Helpers\StorageHelper;

class ServiceDispatchController extends Controller
{
    use StorageDocument;

    private function getServiceInitial()
    {
        $cp = Company::query()
            ->select('number', 'soap_type_id', 'soap_sunat_username', 'soap_sunat_password', 'api_sunat_id', 'api_sunat_secret')
            ->first();

        $serviceDispatch = new ServiceDispatch();
        $serviceDispatch->setCredentials(
            $cp->number,
            ($cp->soap_type_id === '01'),
            $cp->soap_sunat_username,
            $cp->soap_sunat_password,
            $cp->api_sunat_id,
            $cp->api_sunat_secret
        );

        return $serviceDispatch;
    }

    public function send($external_id)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $dispatch = Dispatch::query()
                ->select('id', 'document_type_id', 'series', 'number', 'filename', 'ticket')
                ->where('external_id', $external_id)->first();
            if ($dispatch) {
                $xml_signed = (new StorageHelper())->getXmlSigned($dispatch->filename);
                $res = $this->getServiceInitial()->send(
                    $dispatch->filename,
                    $xml_signed
                );

                if ($res['success']) {
                    $data = $res['data'];
                    if (key_exists('numTicket', $data)) {
                        $ticket = $data['numTicket'];
                        $reception_date = $data['fecRecepcion'];
                        Dispatch::query()
                            ->where('id', $dispatch->id)
                            ->update([
                                'ticket' => $ticket,
                                'reception_date' => $reception_date,
                                'state_type_id' => '03'
                            ]);
                        DB::connection('tenant')->commit();
                    }
                    return [
                        'success' => true,
                        'message' => 'Se obtuvo el nro. de ticket correctamente',
                    ];
                } else {
                    return $res;
                }
            }
            return [
                'success' => false,
                'message' => 'El external id es incorrecto'
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::connection('tenant')->rollBack();
            return [
                'success' => false,
                'message' => 'No fue posible enviar a SUNAT'
            ];
        }
    }

    public function statusTicket($external_id)
    {
        $dispatch = Dispatch::query()
            ->select('id', 'series', 'number', 'state_type_id', 'ticket', 'filename', 'external_id')
            ->where('external_id', $external_id)->first();

        if ($dispatch) {
            $storage = new StorageHelper();
            $res = $this->getServiceInitial()->ticket($dispatch->ticket);

            if (key_exists('codRespuesta', $res)) {
                $has_cdr = false;
                $qr_url = null;
                $state_type_id = '01';
                $message = '';
                $success = true;
                switch ($res['codRespuesta']) {
                    case '98':
                        $state_type_id = '03';
                        $message = 'La guía aún está en proceso, vuelva a consultar.';
                        break;
                    case '0':
                        $state_type_id = '05';
                        $has_cdr = true;
                        break;
                    case '99':
                        $state_type_id = '09';
                        if ($res['indCdrGenerado'] === '1') {
                            $has_cdr = true;
                        } else {
                            $message = $res['error']['desError'];
                        }
                        break;
                }

                if ($has_cdr) {
                    $file_content_cdr = (new CdrRead())->getCrdContent($res['arcCdr']);
                    $storage->uploadCdr($dispatch->filename, $file_content_cdr);
                    $cdr_content = (new StorageHelper())->getCdr($dispatch->filename);
                    $res['cdr_data'] = (new CdrRead())->getCdrData($cdr_content);
                    $qr_url = $res['cdr_data']['qr_url'];
                }

                Dispatch::query()
                    ->where('id', $dispatch->id)
                    ->update([
                        'state_type_id' => $state_type_id,
                        'qr_url' => $qr_url
                    ]);

                $record = Dispatch::query()
                    ->select('id', 'series', 'number', 'state_type_id', 'filename', 'external_id')
                    ->where('external_id', $external_id)->first();

                $download_external_cdr = null;
                if ($has_cdr) {
                    $download_external_cdr = $record->download_external_cdr;
                    $message = $res['cdr_data']['message'];
                }

                return [
                    'success' => $success,
                    'data' => [
                        'number' => $record->number_full,
                        'filename' => $record->filename,
                        'external_id' => $record->external_id,
                        'state_type_id' => $record->state_type_id,
                    ],
                    'links' => [
                        'xml' => $record->download_external_xml,
                        'pdf' => $record->download_external_pdf,
                        'cdr' => $download_external_cdr,
                    ],
                    'message' => $message,
                ];
            }

            return $res;
        }

        return [
            'success' => false,
            'message' => 'El external id es incorrecto'
        ];
    }


    public function createXmlUnsigned($document)
    {
        $template = new Template();
        $template_name = ($document['document_type_id'] === '31')?'dispatch_carrier':'dispatch';
        Log::info($template_name);
        $xmlUnsigned = XmlFormat::format($template->xml($template_name, null, $document));
        $this->uploadStorage($document['filename'], $xmlUnsigned, 'unsigned');

        return $xmlUnsigned;
    }

    public function getData($id)
    {
        $company = Company::query()
            ->first();

        $record = Dispatch::query()
            ->find($id);

        $items = [];
        foreach ($record->items as $it) {
            $items[] = [
                'internal_id' => $it->item->internal_id,
                'name' => $it->item->description,
                'unit_type_id' => $it->item->unit_type_id,
                'quantity' => $it->quantity,
            ];
        }
        return [
            'company_name' => $company->name,
            'company_number' => $company->number,
            'company_trade_name' => $company->trade_name,
            'customer_identity_document_type_id' => optional($record->customer)->identity_document_type_id,
            'customer_number' => optional($record->customer)->number,
            'customer_name' => optional($record->customer)->name,
            'document_type_id' => $record->document_type_id,
            'series' => $record->series,
            'number' => $record->number,
            'date_of_issue' => $record->date_of_issue->format('Y-m-d'),
            'time_of_issue' => $record->time_of_issue,
            'transfer_reason_type_id' => $record->transfer_reason_type_id,
            'transfer_reason_type_name' => optional($record->transfer_reason_type)->description,
            'unit_type_id' => $record->unit_type_id,
            'total_weight' => $record->total_weight,
            'transport_mode_type_id' => $record->transport_mode_type_id,
            'date_of_shipping' => $record->date_of_shipping->format('Y-m-d'),
            'observations' => $record->observations,
            'filename' => $record->filename,
            'origin_location_id' => optional($record->origin)->location_id,
            'origin_address' => optional($record->origin)->address,
            'origin_code' => optional($record->origin)->code,
            'delivery_location_id' => optional($record->delivery)->location_id,
            'delivery_address' => optional($record->delivery)->address,
            'delivery_code' => optional($record->delivery)->code,
            'driver_identity_document_type_id' => optional($record->driver)->identity_document_type_id,
            'driver_number' => optional($record->driver)->number,
            'driver_names' => optional($record->driver)->name,
            'driver_lastnames' => optional($record->driver)->name,
            'driver_license' => optional($record->driver)->license,
            'transport_plate_number' => $record->transport_data ? $record->transport_data['plate_number'] : null,
            'dispatcher_identity_document_type_id' => optional($record->dispatcher)->identity_document_type_id,
            'dispatcher_number' => optional($record->dispatcher)->number,
            'dispatcher_name' => optional($record->dispatcher)->name,
            'dispatcher_number_mtc' => optional($record->dispatcher)->number_mtc,

            'sender_identity_document_type_id' => $record->sender_data ? $record->sender_data['identity_document_type_id'] : null,
            'sender_number' => $record->sender_data ? $record->sender_data['number'] : null,
            'sender_name' => $record->sender_data ? $record->sender_data['name'] : null,

            'receiver_identity_document_type_id' => $record->receiver_data ? $record->receiver_data['identity_document_type_id'] : null,
            'receiver_number' => $record->receiver_data ? $record->receiver_data['number'] : null,
            'receiver_name' => $record->receiver_data ? $record->receiver_data['name'] : null,

            'sender_address_location_id' => $record->sender_address_data ? $record->sender_address_data['location_id'] : null,
            'sender_address_address' => $record->sender_address_data ? $record->sender_address_data['address'] : null,

            'receiver_address_location_id' => $record->receiver_address_data ? $record->receiver_address_data['location_id'] : null,
            'receiver_address_address' => $record->receiver_address_data ? $record->receiver_address_data['address'] : null,

            'items' => $items,
        ];
    }

    public function getDataCarrier($id)
    {
        $company = Company::query()
            ->first();

        $record = Dispatch::query()
            ->find($id);

        $items = [];
        foreach ($record->items as $it) {
            $items[] = [
                'internal_id' => $it->item->internal_id,
                'name' => $it->item->description,
                'unit_type_id' => $it->item->unit_type_id,
                'quantity' => $it->quantity,
            ];
        }
        return [
            'company_name' => $company->name,
            'company_number' => $company->number,
            'company_trade_name' => $company->trade_name,
//            'customer_identity_document_type_id' => $record->customer->identity_document_type_id,
//            'customer_number' => $record->customer->number,
//            'customer_name' => $record->customer->name,
            'document_type_id' => $record->document_type_id,
            'series' => $record->series,
            'number' => $record->number,
            'date_of_issue' => $record->date_of_issue->format('Y-m-d'),
            'time_of_issue' => $record->time_of_issue,
//            'transfer_reason_type_id' => $record->transfer_reason_type_id,
//            'transfer_reason_type_name' => $record->transfer_reason_type->description,
            'unit_type_id' => $record->unit_type_id,
            'total_weight' => $record->total_weight,
//            'transport_mode_type_id' => $record->transport_mode_type_id,
            'date_of_shipping' => $record->date_of_shipping->format('Y-m-d'),
            'observations' => $record->observations,
            'filename' => $record->filename,
//            'origin_location_id' => $record->origin->location_id,
//            'origin_address' => $record->origin->address,
//            'origin_code' => $record->origin->code,
//            'delivery_location_id' => $record->delivery->location_id,
//            'delivery_address' => $record->delivery->address,
//            'delivery_code' => $record->delivery->code,
            'driver_identity_document_type_id' => optional($record->driver)->identity_document_type_id,
            'driver_number' => optional($record->driver)->number,
            'driver_names' => optional($record->driver)->name,
            'driver_lastnames' => optional($record->driver)->name,
            'driver_license' => optional($record->driver)->license,
            'transport_plate_number' => $record->transport_data ? $record->transport_data['plate_number'] : null,
//            'dispatcher_identity_document_type_id' => optional($record->dispatcher)->identity_document_type_id,
//            'dispatcher_number' => optional($record->dispatcher)->number,
//            'dispatcher_name' => optional($record->dispatcher)->name,
//            'dispatcher_number_mtc' => optional($record->dispatcher)->number_mtc,
            'items' => $items,
        ];
    }
}
