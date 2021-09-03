<?php

namespace Modules\Order\Models;

use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\PriceType;
use App\Models\Tenant\Catalogs\SystemIscType;
use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;
use Modules\Inventory\Models\Warehouse;

/**
 * Class OrderNoteItem
 *
 * @package Modules\Order\Models
 * @mixin ModelTenant
 */
class OrderNoteItem extends ModelTenant
{
    protected $with = ['affectation_igv_type', 'system_isc_type', 'price_type'];
    public $timestamps = false;

    protected $fillable = [
        'order_note_id',
        'item_id',
        'item',
        'quantity',
        'unit_value',

        'affectation_igv_type_id',
        'total_base_igv',
        'percentage_igv',
        'total_igv',

        'system_isc_type_id',
        'total_base_isc',
        'percentage_isc',
        'total_isc',

        'total_base_other_taxes',
        'percentage_other_taxes',
        'total_other_taxes',
        'total_taxes',

        'price_type_id',
        'unit_price',

        'total_value',
        'total_charge',
        'total_discount',
        'total',

        'attributes',
        'charges',
        'discounts',
        'warehouse_id',
        'total_plastic_bag_taxes',
        'additional_information',
        'name_product_pdf'
    ];

    public function getItemAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setItemAttribute($value)
    {
        $this->attributes['item'] = (is_null($value))?null:json_encode($value);
    }

    public function getAttributesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setAttributesAttribute($value)
    {
        $this->attributes['attributes'] = (is_null($value))?null:json_encode($value);
    }

    public function getChargesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setChargesAttribute($value)
    {
        $this->attributes['charges'] = (is_null($value))?null:json_encode($value);
    }

    public function getDiscountsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDiscountsAttribute($value)
    {
        $this->attributes['discounts'] = (is_null($value))?null:json_encode($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affectation_igv_type()
    {
        return $this->belongsTo(AffectationIgvType::class, 'affectation_igv_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function system_isc_type()
    {
        return $this->belongsTo(SystemIscType::class, 'system_isc_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function price_type()
    {
        return $this->belongsTo(PriceType::class, 'price_type_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order_note()
    {
        return $this->belongsTo(OrderNote::class, 'order_note_id');
    }


    public function scopeWherePendingState($query, $params)
    {

        if($params['person_id']){

            return $query->whereHas('order_note', function($q) use($params){
                            $q->doesntHave('documents')
                                ->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                                ->where('customer_id', $params['person_id'])
                                ->whereTypeUser();
                        });
        }


        return $query->whereHas('order_note', function($q) use($params){
                    $q->doesntHave('documents')
                        ->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                        ->where('user_id', $params['seller_id'])
                        ->whereTypeUser();
                });

    }


    public function scopeWhereProcessedState($query, $params)
    {

        if($params['person_id']){

            return $query->whereHas('order_note', function($q) use($params){
                            $q->whereHas('documents')
                                ->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                                ->where('customer_id', $params['person_id'])
                                ->whereTypeUser();
                        });

        }


        return $query->whereHas('order_note', function($q) use($params){
                    $q->whereHas('documents')
                        ->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                        ->where('user_id', $params['seller_id'])
                        ->whereTypeUser();
                });

    }

    public function scopeWhereDefaultState($query, $params)
    {

        if($params['person_id']){

            return $query->whereHas('order_note', function($q) use($params){
                            $q->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                                ->where('customer_id', $params['person_id'])
                                ->whereTypeUser();
                        });

        }


        return $query->whereHas('order_note', function($q) use($params){
                    $q->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                        ->where('user_id', $params['seller_id'])
                        ->whereTypeUser();
                });

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relation_item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * @return float
     */
    public function getTotalPlasticBagTaxes()
    : float {
        return $this->total_plastic_bag_taxes;
    }

    /**
     * @param float $total_plastic_bag_taxes
     *
     * @return OrderNoteItem
     */
    public function setTotalPlasticBagTaxes(float $total_plastic_bag_taxes)
    : OrderNoteItem {
        $this->total_plastic_bag_taxes = $total_plastic_bag_taxes;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalInformation()
    : string {
        return $this->additional_information;
    }

    /**
     * @param string $additional_information
     *
     * @return OrderNoteItem
     */
    public function setAdditionalInformation(string $additional_information)
    : OrderNoteItem {
        $this->additional_information = $additional_information;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameProductPdf()
    : string {
        return $this->name_product_pdf;
    }

    /**
     * @param string $name_product_pdf
     *
     * @return OrderNoteItem
     */
    public function setNameProductPdf(string $name_product_pdf)
    : OrderNoteItem {
        $this->name_product_pdf = $name_product_pdf;
        return $this;
    }


    /**
     * Devuelve el numero de la cantidad, si es int, devuelve el numero con 0 decimales
     * @return mixed|string
     */
    public function getStringQty(){
        $int_qty = (int) $this->quantity;
        $qty = $this->quantity;
        if(is_int($qty)){
            $qty = number_format($qty,0);
        }
        return $qty;
    }


    /**
     * Devuelve unit_price formateado a string con N decimales
     * @param int $decimal
     *
     * @return string
     */
    public function getStringUnitPrice($decimal = 2){
        return number_format($this->unit_price, $decimal) ;
    }

    /**
     *  Devuelve total formateado a string con N decimales
     * @param int $decimal
     *
     * @return string
     */
    public function getStringTotal($decimal = 2){
        return number_format($this->total, $decimal) ;
    }

    /**
     *  Devuelve la descripcion del producto
     * @return string
     */
    public function getTemplateDescription(){
        if(empty($this->name_product_pdf)) {
            return $this->item->description;
        }
        return $this->getNameProductPdf();

    }
}
