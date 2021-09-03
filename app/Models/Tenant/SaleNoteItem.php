<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\PriceType;
use App\Models\Tenant\Catalogs\SystemIscType;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Warehouse;

class SaleNoteItem extends ModelTenant
{
    protected $with = ['affectation_igv_type', 'system_isc_type', 'price_type'];
    public $timestamps = false;

    protected $fillable = [
        'sale_note_id',
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
        'inventory_kardex_id',
        'warehouse_id',
        'total_plastic_bag_taxes',
        'additional_information',
        'name_product_pdf',

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

    public function affectation_igv_type()
    {
        return $this->belongsTo(AffectationIgvType::class, 'affectation_igv_type_id');
    }

    public function system_isc_type()
    {
        return $this->belongsTo(SystemIscType::class, 'system_isc_type_id');
    }

    public function price_type()
    {
        return $this->belongsTo(PriceType::class, 'price_type_id');
    }

    public function sale_note()
    {
        return $this->belongsTo(SaleNote::class, 'sale_note_id');
    }

    public function relation_item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }


    public function scopeWhereDefaultDocumentType($query, $params)
    {

        $db_raw =  DB::raw("sale_note_items.id as id, sale_notes.series as series, sale_notes.number as number,
                            sale_note_items.item as item, sale_note_items.quantity as quantity, sale_note_items.item_id as item_id,sale_notes.date_of_issue as date_of_issue");

        if (isset($params['establishment_id'])) {
            $query->where('establishment_id', $params['establishment_id']);
        }
        if($params['person_id']){

            return $query->whereHas('sale_note', function($q) use($params){
                            $q->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                                ->where('customer_id', $params['person_id'])
                                ->whereStateTypeAccepted()
                                ->whereTypeUser();
                        })
                        ->join('sale_notes', 'sale_note_items.sale_note_id', '=', 'sale_notes.id')
                        ->select($db_raw)
                        ->latest('id');

        }


        $data = $query->whereHas('sale_note', function($q) use($params){
                    $q->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                        // ->where('user_id', $params['seller_id'])
                        ->whereStateTypeAccepted()
                        ->whereTypeUser();
                })
                ->join('sale_notes', 'sale_note_items.sale_note_id', '=', 'sale_notes.id')
                ->select($db_raw)
                ->latest('id');


        $sellers = json_decode($params['sellers']);

        if(count($sellers) > 0){
            $data = $data->whereHas('sale_note', function($q) use($params, $sellers){$q->whereIn('user_id', $sellers);});
        }

        return $data;
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return Item|Item[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function getModelItem(){ return Item::find($this->item_id);}

}
