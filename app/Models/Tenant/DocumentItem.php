<?php

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Warehouse;
use App\Models\Tenant\Catalogs\PriceType;
use App\Models\Tenant\Catalogs\SystemIscType;
use App\Models\Tenant\Catalogs\AffectationIgvType;

/**
 * App\Models\Tenant\DocumentItem
 *
 * @property-read AffectationIgvType $affectation_igv_type
 * @property-read \App\Models\Tenant\Document $document
 * @property-read mixed $additional_information
 * @property mixed $attributes
 * @property mixed $charges
 * @property mixed $discounts
 * @property mixed $item
 * @property-read \App\Models\Tenant\Item $m_item
 * @property-read PriceType $price_type
 * @property-read \App\Models\Tenant\Item $relation_item
 * @property-read SystemIscType $system_isc_type
 * @property-read Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentItem whereDefaultDocumentType($params)
 * @mixin \Eloquent
 */
class DocumentItem extends ModelTenant
{
    protected $with = ['affectation_igv_type', 'system_isc_type', 'price_type'];
    public $timestamps = false;

    protected $fillable = [
        'document_id',
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
        'total_plastic_bag_taxes',
        'warehouse_id',
        'name_product_pdf',
        'additional_information'
    ];

    public static function boot() {
        parent::boot();
        static::creating(function (self $item) {
            $document = $item->document;
            if ($document !== null && empty($item->warehouse_id)) {
                $warehouse = Warehouse::find($document->establishment_id) ;
                if($warehouse !== null) {
                    $item->warehouse_id = $document->establishment_id;
                }
            }
        });
    }

    /**
     * Ajusta el stock en ItemWarehouse que es usado como stock por almacen
     * @param self $item
     * @param string $event
     */
    public static function UpdateItemWarehous(&$item, $event = 'created'){
        $document = $item->document;
        if ($document !== null) {
            $establishment_id = $document->establishment_id;
            $search = [
                'item_id' => $item->item_id,
                'warehouse_id' => $establishment_id,
            ];
            $ItemWarehouse = ItemWarehouse::where($search)->first();
            if ($ItemWarehouse !== null) {
                $qty = (float)$item->quantity;
                if($event === 'created') {
                    $ItemWarehouse->addStock($qty * (-1))->push();
                }else{
                    $ItemWarehouse->addStock($qty * (1))->push();
                    self::FixKardex($item);
                }
            }
        }
    }

    /**
     * Devuelve o quita la cantidad del item a kardex.
     * @param self $model
     * @param bool $deleting
     */
    public static function FixKardex(&$model, $deleting = true){
        $search = [
            'inventory_kardexable_id'=>$model->document_id,
            'item_id'=>$model->item_id,
            'inventory_kardexable_type'=>Document::class
        ];
        $kardex = \Modules\Inventory\Models\InventoryKardex::where($search)->orderBy('id','desc')->first();
        if(!empty($kardex)) {
            $qty = abs((float)$kardex->quantity * 1);
            if($deleting !== true){
                $qty = $qty * (-1);
            }
            $newKardex = new \Modules\Inventory\Models\InventoryKardex([
                'date_of_issue' => Carbon::now()->format('Y-m-d'),
                'warehouse_id' => $kardex->warehouse_id,
                'quantity' => $qty,
                'inventory_kardexable_id' => $kardex->inventory_kardexable_id,
                'inventory_kardexable_type' => $kardex->inventory_kardexable_type,
                'item_id' => $kardex->item_id,
            ]);
            $newKardex->push();
        }
    }
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
    public function m_item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relation_item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * @param $value
     *
     * @return false|string[]
     */
    public function getAdditionalInformationAttribute($value)
    {
        // if($value){
            $arr = explode('|', $value);
            return $arr;
        // }

        // return null;

    }


    /**
     * @param $query
     * @param $params
     *
     * @return mixed
     */
    public function scopeWhereDefaultDocumentType($query, $params)
    {

        $db_raw = DB::raw("document_items.id as id, documents.series as series, documents.number as number,
                            document_items.item as item, document_items.quantity as quantity, document_items.item_id as item_id,
                            documents.date_of_issue as date_of_issue");

        if (isset($params['series'])) {
            $query->where('series', $params['series']);
        }
        if (isset($params['establishment_id'])) {
            $query->where('establishment_id', $params['establishment_id']);
        }
        if($params['person_id']){

            return $query->whereHas('document', function($q) use($params){
                            $q->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                                ->where('customer_id', $params['person_id'])
                                ->whereStateTypeAccepted()
                                ->whereTypeUser();
                        })
                        ->join('documents', 'document_items.document_id', '=', 'documents.id')
                        ->select($db_raw)
                        ->latest('id');

        }


        $data = $query->whereHas('document', function($q) use($params){
                    $q->whereBetween($params['date_range_type_id'], [$params['date_start'], $params['date_end']])
                        // ->where('user_id', $params['seller_id'])
                        ->whereStateTypeAccepted()
                        ->whereTypeUser();
                })
                ->join('documents', 'document_items.document_id', '=', 'documents.id')
                ->select($db_raw)
                ->latest('id');


        $sellers = json_decode($params['sellers']);

        // dd($sellers, count($sellers));

        if(count($sellers) > 0){
            $data = $data->whereHas('document', function($q) use($params, $sellers){$q->whereIn('user_id', $sellers);});
        }

        return $data;

    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return Item|Item[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function getModelItem(){ return Item::find($this->item_id);}

    /**
     * Devuelve una estructura en conjunto para datos extra al momento de generar un pdf
     *
     * @return array
     */
    public function getPrintExtraData()
    {


        $item = $this->item;
        $extra = (property_exists($item, 'extra')) ? $item->extra : null;
        $extra_string = ($extra != null && property_exists($extra, 'string')) ? $extra->string : null;
        $colors = ($extra_string != null && property_exists($extra_string, 'colors')) ? $extra_string->colors : null;
        $CatItemUnitsPerPackage = ($extra_string != null && property_exists($extra_string, 'CatItemUnitsPerPackage')) ? $extra_string->CatItemUnitsPerPackage : null;
        $CatItemMoldProperty = ($extra_string != null && property_exists($extra_string, 'CatItemMoldProperty')) ? $extra_string->CatItemMoldProperty : null;
        $CatItemProductFamily = ($extra_string != null && property_exists($extra_string, 'CatItemProductFamily')) ? $extra_string->CatItemProductFamily : null;
        $CatItemMoldCavity = ($extra_string != null && property_exists($extra_string, 'CatItemMoldCavity')) ? $extra_string->CatItemMoldCavity : null;
        $CatItemPackageMeasurement = ($extra_string != null && property_exists($extra_string, 'CatItemPackageMeasurement')) ? $extra_string->CatItemPackageMeasurement : null;
        $CatItemStatus = ($extra_string != null && property_exists($extra_string, 'CatItemStatus')) ? $extra_string->CatItemStatus : null;
        $CatItemUnitBusiness = ($extra_string != null && property_exists($extra_string, 'CatItemUnitBusiness')) ? $extra_string->CatItemUnitBusiness : null;
        $data = [
            'colors' => (!empty($colors)) ? $colors : null,
            'CatItemUnitsPerPackage' => (!empty($CatItemUnitsPerPackage)) ? $CatItemUnitsPerPackage : null,
            'CatItemMoldProperty' => (!empty($CatItemMoldProperty)) ? $CatItemMoldProperty : null,
            'CatItemProductFamily' => (!empty($CatItemProductFamily)) ? $CatItemProductFamily : null,
            'CatItemMoldCavity' => (!empty($CatItemMoldCavity)) ? $CatItemMoldCavity : null,
            'CatItemPackageMeasurement' => (!empty($CatItemPackageMeasurement)) ? $CatItemPackageMeasurement : null,
            'CatItemStatus' => (!empty($CatItemStatus)) ? $CatItemStatus : null,
            'CatItemUnitBusiness' => (!empty($CatItemUnitBusiness)) ? $CatItemUnitBusiness : null,
        ];
        // Se añaden campos extra desde el item
        $itemModel = $this->getModelItem();
        $itemModel->getExtraDataToPrint($data);
        return $data;
    }

}
