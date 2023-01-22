<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\SalenotePayment;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\Document;
use App\Models\Tenant\SaleNote;

class StateAccountCollection extends ResourceCollection
{


    public function toArray($request) {


        return $this->collection->transform(function($row, $key){

            //dd(get_class($row));
            $affected_document = null;
            $payment_state='';
            $description='';
            $type_description='';
            if(strpos($row->series, 'F001') !== false||strpos($row->series, 'B001') !== false){

                if(in_array($row->document_type_id, ['07', '08']) && $row->note) {

                    $series = ($row->note->affected_document) ? $row->note->affected_document->series : $row->note->data_affected_document->series;
                    $number = ($row->note->affected_document) ? $row->note->affected_document->number : $row->note->data_affected_document->number;
                    $affected_document = $series . ' - ' . $number;
                }
                $pays = DocumentPayment::where('document_id', $row->id);

                $total_paid = number_format($pays->sum('payment'), 2, '.', '');
                $payment_state = number_format($row->total - $total_paid, 2, '.', '');

                $description=$row->state_type?$row->state_type->description:null;
                

                $document_type_id = Document::where('id', $row->id)->select('document_type_id')->get();
                $document_type_id=$document_type_id[0]['document_type_id'];
                //dd($document_type_id);
                $type_description= DocumentType::where('id',$document_type_id)->select('description')->get();
                $type_description=$type_description[0]['description'];
            }else{
                $document_type = DocumentType::find('80');

                $document_type_id = $document_type->id;

                $row->document_type = $document_type;
                /** @var SaleNote $row */

                $affected_document = null;
                
                if(in_array($document_type_id, ['07', '08']) && $row->note) {

                    $series = ($row->note->affected_document) ? $row->note->affected_document->series : $row->note->data_affected_document->series;
                    $number = ($row->note->affected_document) ? $row->note->affected_document->number : $row->note->data_affected_document->number;
                    $affected_document = $series . ' - ' . $number;
                }

                $pays = SalenotePayment::where('sale_note_id', $row->id);
                $total_paid = number_format($pays->sum('payment'), 2, '.', '');
                $payment_state = number_format($row->total - $total_paid, 2, '.', '');
                $description=$row->state_type?$row->state_type->description:null;
                $date_of_due=SaleNote::where('id',$row->id)->select('due_date')->get();
                $date_of_due=$date_of_due[0]['due_date'];
                $type_description='NOTA DE VENTA';
                //dd($row);
            }

            $state = $row->state_type_id;

            $seller = $row->getSellerData();
            //dd($seller->name);
            return [
                'id' => $row->id,
                'group_id' => $row->group_id,
                'soap_type_id' => $row->soap_type_id,
                'soap_type_description' => isset($row->soap_type)?$row->soap_type->description:null,
                'date_of_issue' => $row->date_of_issue?$row->date_of_issue->format('Y-m-d'):null,
                'date_of_due' => (in_array($document_type_id, ['01', '03'])) ?$row->invoice->date_of_due->format('Y-m-d') : (($date_of_due) ? $date_of_due->format('Y-m-d') : null),
                'number' => $row->number_full,
                'customer_name' => (in_array($document_type_id, ['01', '03']) && isset($row->person)) ? $row->person->name : ($document_type_id=='80' ? $row->person->name : null),
                'customer_number' => (in_array($document_type_id, ['01', '03'])&& isset($row->person)) ? $row->person->number : ($document_type_id=='80' ? $row->person->number : null),
                'currency_type_id' => $row->currency_type_id,
                'series' => $row->series,
                'establishment_id' => $row->establishment_id,
                'alone_number' => $row->number,
                'purchase_order' => $row->purchase_order,
                'guides' => !empty($row->guides)?(array)$row->guides:null,

                'total_exportation' => (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total_exportation,2, ".",""),
                'total_exonerated' =>  (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total_exonerated,2, ".",""),
                'total_unaffected' =>  (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total_unaffected,2, ".",""),
                'total_free' =>  (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total_free,2, ".",""),
                'total_taxed' => (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total_taxed,2, ".",""),
                'total_igv' =>  (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total_igv,2, ".",""),
                'total' =>  (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total,2, ".",""),
                'total_isc' =>  (in_array($document_type_id,['01','03', '07']) && in_array($row->state_type_id,['09','11'])) ? number_format(0,2, ".","") : number_format($row->total_isc,2, ".",""),
                'total_charge' => $row->total_charge,
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $description,
                'document_type_description' => $type_description,
                'document_type_id' => $document_type_id,
                'affected_document' => $affected_document,
                'user_name' => $seller->name ,
                'user_email' => $seller->email ,

                'notes' => (in_array($document_type_id, ['01', '03'])) ? $row->affected_documents->transform(function($row) {
                    return [
                        'id' => $row->id,
                        'document_id' => $row->document_id,
                        'note_type_description' => ($row->note_type == 'credit') ? 'NC':'ND',
                        'description' => $row->document->number_full,
                    ];
                }) : null,
                'quotation_number_full' => ($row->quotation) ? $row->quotation->number_full : '',
                'sale_opportunity_number_full' => isset($row->quotation->sale_opportunity) ? $row->quotation->sale_opportunity->number_full : '',
                'plate_number' => $row->plate_number,
                'payment_state' => $payment_state,
                'items' => $row->items->transform(function($row, $key) {
                    return [
                        'key' => $key + 1,
                        'id' => $row->id,
                        'description' => $row->item->description,
                        'quantity' => round($row->quantity,2)
                    ];
                }),

            ];
        });
    }
}
