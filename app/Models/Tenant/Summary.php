<?php

namespace App\Models\Tenant;

use App\CoreFacturalo\Facturalo;
use App\Models\Tenant\Catalogs\SummaryStatusType;
use Illuminate\Support\Facades\DB;

/**
 * Class Summary
 *
 * @package App\Models\Tenant
 * @mixin ModelTenant
 */
class Summary extends ModelTenant
{
    // protected $with = ['user', 'soap_type', 'state_type', 'summary_status_type', 'documents'];

    protected $fillable = [
        'user_id',
        'external_id',
        'soap_type_id',
        'state_type_id',
        'summary_status_type_id',
        'ubl_version',
        'date_of_issue',
        'date_of_reference',
        'identifier',
        'filename',
        'ticket',
        'has_ticket',
        'has_cdr',
        'soap_shipping_response',
        'unknown_error_status_response',
        'manually_regularized',
        'error_manually_regularized',
        'unique_filename',

        'send_to_pse',
        'response_signature_pse',
        'response_send_cdr_pse',

    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'date_of_reference' => 'date',
        'unknown_error_status_response' => 'boolean',
        'manually_regularized' => 'boolean',
        'send_to_pse' => 'bool',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function soap_type()
    {
        return $this->belongsTo(SoapType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function summary_status_type()
    {
        return $this->belongsTo(SummaryStatusType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(SummaryDocument::class);
    }

    /**
     * @return string
     */
    public function getDownloadExternalXmlAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'summary', 'type' => 'xml', 'external_id' => $this->external_id]);
    }

    /**
     * @return string
     */
    public function getDownloadExternalPdfAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'summary', 'type' => 'pdf', 'external_id' => $this->external_id]);
    }

    /**
     * @return string
     */
    public function getDownloadExternalCdrAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'summary', 'type' => 'cdr', 'external_id' => $this->external_id]);
    }

    public function getSoapShippingResponseAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSoapShippingResponseAttribute($value)
    {
        $this->attributes['soap_shipping_response'] = (is_null($value))?null:json_encode($value);
    }

    public function getErrorManuallyRegularizedAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setErrorManuallyRegularizedAttribute($value)
    {
        $this->attributes['error_manually_regularized'] = (is_null($value))?null:json_encode($value);
    }
    
    /**
     * Devuelve la clase Facturalo con los elementos cargados
     *
     * @return \App\CoreFacturalo\Facturalo
     */
    public function getFacturalo(){

        $model = $this;
        return DB::connection('tenant')->transaction(function () use ($model) {
            $facturalo = new Facturalo();
            return $facturalo->loadDocument($model->id, 'summary');
        });
    }

        
    /**
     * 
     * Verificar si es un resumen para adicionar o modificar
     *
     * @return bool
     */
    public function isAddModifySummary()
    {
        return in_array($this->summary_status_type_id, ['1', '2']);
    }


    /**
     * Obtener tipo de documento válido para enviar el xml a firmar al pse
     *
     * Usado en:
     * App\CoreFacturalo\Services\Helpers\SendDocumentPse
     * 
     * @return string
    */
    public function getDocumentTypeForPse()
    {
        return $this->isAddModifySummary() ? 'RESU' : 'REAN';
    }


    public function getResponseSendCdrPseAttribute($value)
    {
        return (is_null($value)) ? null : (object)json_decode($value);
    }


    public function setResponseSendCdrPseAttribute($value)
    {
        $this->attributes['response_send_cdr_pse'] = (is_null($value)) ? null : json_encode($value);
    }


    public function getResponseSignaturePseAttribute($value)
    {
        return (is_null($value)) ? null : (object)json_decode($value);
    }


    public function setResponseSignaturePseAttribute($value)
    {
        $this->attributes['response_signature_pse'] = (is_null($value)) ? null : json_encode($value);
    }

    
    /**
     * 
     * Validar si el resumen se firma y envia a pse
     *
     * @param  SendDocumentPse $sendDocumentPse
     * @return bool
     */
    public function getSendToPse($sendDocumentPse)
    {
        $send_to_pse = true;
        // $send_to_pse = false;

        // $summary_voided_documents = $this->documents;
        // $filter_quantity_documents = $summary_voided_documents->where('document.send_to_pse', true)->count();
        
        // if($summary_voided_documents->count() === $filter_quantity_documents)
        // {
        //     $send_to_pse = true;
        // }
        // else
        // {
        //     $difference = $summary_voided_documents->count() - $filter_quantity_documents;
        //     $sendDocumentPse->throwException("La cantidad de documentos firmados por el PSE, debe ser igual al total de documentos a enviar en el resumen: {$difference} documento(s) no fueron firmados por el PSE.");
        // }

        return $send_to_pse;
    }

}
