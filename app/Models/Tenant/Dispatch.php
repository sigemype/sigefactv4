<?php

namespace App\Models\Tenant;

use App\CoreFacturalo\Facturalo;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Catalogs\TransferReasonType;
use App\Models\Tenant\Catalogs\TransportModeType;
use App\Models\Tenant\Catalogs\UnitType;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\OrderForm;
use Modules\Inventory\Models\InventoryKardex;
use Modules\Order\Models\OrderNote;

/**
 * Class Dispatch
 *
 * @package App\Models\Tenant
 * @mixin ModelTenant
 */
class Dispatch extends ModelTenant
{
    protected $with = ['user', 'soap_type', 'state_type', 'document_type', 'unit_type', 'transport_mode_type',
                       'transfer_reason_type', 'items', 'reference_document'];

    protected $fillable = [
        'user_id',
        'external_id',
        'establishment_id',
        'establishment',
        'soap_type_id',
        'state_type_id',
        'ubl_version',
        'document_type_id',
        'series',
        'number',
        'date_of_issue',
        'time_of_issue',
        'customer_id',
        'customer',
        'observations',
        'transport_mode_type_id',
        'transfer_reason_type_id',
        'transfer_reason_description',
        'date_of_shipping',
        'transshipment_indicator',
        'port_code',
        'unit_type_id',
        'total_weight',
        'packages_number',
        'container_number',
        'origin',
        'delivery',
        'dispatcher',
        'driver',
        'license_plate',

        'legends',

        'filename',
        'hash',

        'has_xml',
        'has_pdf',
        'has_cdr',

        'reference_document_id',
        'reference_quotation_id',
        'reference_order_note_id',
        'reference_order_form_id',
        'secondary_license_plates',
        'reference_sale_note_id',
        'soap_shipping_response',
        'data_affected_document',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'date_of_shipping' => 'date',
    ];

    public function getEstablishmentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setEstablishmentAttribute($value)
    {
        $this->attributes['establishment'] = (is_null($value))?null:json_encode($value);
    }

    public function getCustomerAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setCustomerAttribute($value)
    {
        $this->attributes['customer'] = (is_null($value))?null:json_encode($value);
    }

    public function getOriginAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setOriginAttribute($value)
    {
        $this->attributes['origin'] = (is_null($value))?null:json_encode($value);
    }

    public function getDeliveryAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDeliveryAttribute($value)
    {
        $this->attributes['delivery'] = (is_null($value))?null:json_encode($value);
    }

    public function getDispatcherAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDispatcherAttribute($value)
    {
        $this->attributes['dispatcher'] = (is_null($value))?null:json_encode($value);
    }

    public function getDriverAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDriverAttribute($value)
    {
        $this->attributes['driver'] = (is_null($value))?null:json_encode($value);
    }

    public function getLegendsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setLegendsAttribute($value)
    {
        $this->attributes['legends'] = (is_null($value))?null:json_encode($value);
    }

    public function getSoapShippingResponseAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSoapShippingResponseAttribute($value)
    {
        $this->attributes['soap_shipping_response'] = (is_null($value))?null:json_encode($value);
    }

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
    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
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
    public function document_type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference_document()
    {
        return $this->belongsTo(Document::class, 'reference_document_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit_type()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transport_mode_type()
    {
        return $this->belongsTo(TransportModeType::class, 'transport_mode_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transfer_reason_type()
    {
        return $this->belongsTo(TransferReasonType::class, 'transfer_reason_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(DispatchItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function generate_document()
    {
        return $this->hasOne(Document::class);
    }

    /**
     * @return string
     */
    public function getNumberFullAttribute()
    {
        return $this->series.'-'.$this->number;
    }

    /**
     * @return string
     */
    public function getDownloadExternalXmlAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'dispatch', 'type' => 'xml', 'external_id' => $this->external_id]);
    }

    /**
     * @return string
     */
    public function getDownloadExternalPdfAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'dispatch', 'type' => 'pdf', 'external_id' => $this->external_id]);
    }

    /**
     * @return string
     */
    public function getDownloadExternalCdrAttribute()
    {
        return route('tenant.download.external_id', ['model' => 'dispatch', 'type' => 'cdr', 'external_id' => $this->external_id]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order_form()
    {
        return $this->belongsTo(OrderForm::class, 'reference_order_form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function inventory_kardex()
    {
        return $this->morphMany(InventoryKardex::class, 'inventory_kardexable');
    }

    public function getSecondaryLicensePlatesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSecondaryLicensePlatesAttribute($value)
    {
        $this->attributes['secondary_license_plates'] = (is_null($value))?null:json_encode($value);
    }

    /**
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|null
     */
    public function scopeWhereTypeUser($query)
    {
        $user = auth()->user();
        return ($user->type == 'seller') ? $query->where('user_id', $user->id) : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_note()
    {
        return $this->belongsTo(SaleNote::class, 'reference_sale_note_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order_note()
    {
        return $this->belongsTo(OrderNote::class, 'reference_order_note_id');
    }


    /**
     * Retorna un standar de nomenclatura para el modelo
     *
     * @return array
     */
    public function  getCollectionData() {

        $has_cdr = false;

        if (in_array($this->state_type_id, ['05', '07'])) {
            $has_cdr = true;
        }

        $documents = [];

        if($this->generate_document) $documents [] = ['description' => $this->generate_document->number_full];
        if($this->reference_document) $documents [] = ['description' => $this->reference_document->number_full];
        
        // 
        return [
            'id'                     => $this->id,
            'external_id'            => $this->external_id,
            'group_id'               => $this->group_id,
            'soap_type_id'           => $this->soap_type_id,
            'date_of_issue'          => $this->date_of_issue->format('Y-m-d'),
            'number'                 => $this->number_full,
            'customer_id'            => $this->customer_id,
            'customer_name'          => $this->customer->name,
            'customer_number'        => $this->customer->identity_document_type->description.' '.$this->customer->number,
            'user_id'                => $this->user_id,
            'user_name'              => $this->user->name,
            'date_of_shipping'       => $this->date_of_shipping->format('Y-m-d'),
            'state_type_id'          => $this->state_type_id,
            'state_type_description' => $this->state_type->description,
            'has_xml'                => $this->has_xml,
            'has_pdf'                => $this->has_pdf,
            // 'has_cdr' => $this->has_cdr,
            'has_cdr'                => $has_cdr,
            'download_external_xml'  => $this->download_external_xml,
            'download_external_pdf'  => $this->download_external_pdf,
            'download_external_cdr'  => $this->download_external_cdr,
            'reference_document_id'  => $this->reference_document_id,
            'created_at'             => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'             => $this->updated_at->format('Y-m-d H:i:s'),
            'soap_shipping_response' => $this->soap_shipping_response,
            'btn_generate_document' => $this->generate_document || $this->reference_document_id ? false : true,
            'documents' => $documents
        ];
        
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
            return $facturalo->loadDocument($model->id, 'dispatch') ;
        });

    }

    /**
     * @return bool
     */
    public function wasSend(){
        $temp = $this->soap_shipping_response;
        if (empty($temp)) {
            return false;
        }
        return $temp->sent;
    }

    public function getDataAffectedDocumentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDataAffectedDocumentAttribute($value)
    {
        $this->attributes['data_affected_document'] = (is_null($value))?null:json_encode($value);
    }

}
