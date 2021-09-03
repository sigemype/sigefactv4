<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\DocumentType;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Modules\Purchase\Models\PurchaseOrder;

/**
 * Class Purchase
 *
 * @package App\Models\Tenant
 * @mixin ModelTenant
 * @property-read CurrencyType $currency_type
 * @property-read \App\Models\Tenant\Person $customer
 * @property-read DocumentType $document_type
 * @property-read \App\Models\Tenant\Establishment $establishment
 * @property mixed $charges
 * @property mixed $detraction
 * @property mixed $discounts
 * @property mixed $guides
 * @property mixed $legends
 * @property-read mixed $number_full
 * @property-read mixed $number_to_letter
 * @property mixed $perception
 * @property mixed $prepayments
 * @property-read mixed $related
 * @property \App\Models\Tenant\Person $supplier
 * @property-read \App\Models\Tenant\Group $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\InventoryKardex[] $inventory_kardex
 * @property-read int|null $inventory_kardex_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\PurchaseItem[] $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\Kardex[] $kardex
 * @property-read int|null $kardex_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\PurchasePayment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\PurchaseItem[] $purchase_items
 * @property-read int|null $purchase_items_count
 * @property-read PurchaseOrder $purchase_order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\PurchasePayment[] $purchase_payments
 * @property-read int|null $purchase_payments_count
 * @property-write mixed $related_documents
 * @property-read \App\Models\Tenant\SoapType $soap_type
 * @property-read \App\Models\Tenant\StateType $state_type
 * @property-read \App\Models\Tenant\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase dasboardSalePurchase($establishment_id = 0)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase onlyDateOfIssueByYear($year = 0)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereStateTypeAccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereTypeUser()
 */
class Purchase extends ModelTenant
{
    // use SoftDeletes;

    protected $with = ['user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items', 'purchase_payments'];

    protected $fillable = [
        'user_id',
        'external_id',
        'establishment_id',
        // 'establishment',
        'soap_type_id',
        'state_type_id',
        'group_id',
        'document_type_id',
        'series',
        'number',
        'date_of_issue',
        'time_of_issue',
        'supplier_id',
        'supplier',
        'currency_type_id',
        'exchange_rate_sale',
        'total_prepayment',
        'total_discount',
        'total_charge',
        'total_exportation',
        'total_free',
        'total_taxed',
        'total_unaffected',
        'total_exonerated',
        'total_igv',
        'total_base_isc',
        'total_isc',
        'total_base_other_taxes',
        'total_other_taxes',
        'total_taxes',
        'total_value',
        'total',
        'total_perception',
        'perception_number',
        'perception_date',

        'charges',
        'discounts',
        'prepayments',
        'guides',
        'related',
        'perception',
        'detraction',
        'legends',
        'date_of_due',
        'purchase_order_id',
        'customer_id',
        'total_canceled'
    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'date_of_due' => 'date',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    // public function getEstablishmentAttribute($value)
    // {
    //     return (is_null($value))?null:(object) json_decode($value);
    // }

    // public function setEstablishmentAttribute($value)
    // {
    //     $this->attributes['establishment'] = (is_null($value))?null:json_encode($value);
    // }


    public function getSupplierAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSupplierAttribute($value)
    {
        $this->attributes['supplier'] = (is_null($value))?null:json_encode($value);
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

    public function getPrepaymentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPrepaymentsAttribute($value)
    {
        $this->attributes['prepayments'] = (is_null($value))?null:json_encode($value);
    }

    public function getGuidesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setGuidesAttribute($value)
    {
        $this->attributes['guides'] = (is_null($value))?null:json_encode($value);
    }

    public function getRelatedAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setRelatedDocumentsAttribute($value)
    {
        $this->attributes['related'] = (is_null($value))?null:json_encode($value);
    }

    public function getPerceptionAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPerceptionAttribute($value)
    {
        $this->attributes['perception'] = (is_null($value))?null:json_encode($value);
    }

    public function getDetractionAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDetractionAttribute($value)
    {
        $this->attributes['detraction'] = (is_null($value))?null:json_encode($value);
    }

    public function getLegendsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setLegendsAttribute($value)
    {
        $this->attributes['legends'] = (is_null($value))?null:json_encode($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_payments()
    {
        return $this->hasMany(PurchasePayment::class);
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
    public function group()
    {
        return $this->belongsTo(Group::class);
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
    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier() {
        return $this->belongsTo(Person::class, 'supplier_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * @return string
     */
    public function getNumberFullAttribute()
    {
        return $this->series.'-'.$this->number;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kardex()
    {
        return $this->hasMany(Kardex::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function inventory_kardex()
    {
        return $this->morphMany(InventoryKardex::class, 'inventory_kardexable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * @return mixed
     */
    public function getNumberToLetterAttribute()
    {
        $legends = $this->legends;
        $legend = collect($legends)->where('code', '1000')->first();
        return $legend->value;
    }

    /**
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|null
     */
    public function scopeWhereTypeUser( $query)
    {
        /** @var \App\Models\Tenant\User $user */
        $user = auth()->user();
        return ($user->type === 'seller') ? $query->where('user_id', $user->id) : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }


    /**
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereStateTypeAccepted($query)
    {
        return $query->whereIn('state_type_id', ['01','03','05','07','13']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer() {
        return $this->belongsTo(Person::class, 'customer_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $establishment_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDasboardSalePurchase( $query, $establishment_id = 0) {
        $query->without(
            [
                'user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items',
                'purchase_payments',
            ]
        );
        $query->WhereStateTypeAccepted();
        $query->where('establishment_id', $establishment_id);
        $query->select(
            'id', 'state_type_id', 'establishment_id', 'currency_type_id', 'total', 'exchange_rate_sale',
            'total_perception', 'date_of_issue',
            \DB::raw( "(CASE WHEN currency_type_id = 'PEN' THEN total ELSE (exchange_rate_sale * total) END) as total_purchase"),
            \DB::raw( "(CASE WHEN currency_type_id = 'PEN' THEN total_perception ELSE (exchange_rate_sale * total_perception) END) as total_perception_purchase")
        );

        return $query;
    }


    /**
     * Filtra por año basandose en date_of_issue
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $year
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyDateOfIssueByYear($query, $year = 0) {
        if ($year == 0) {
            $year = (int)Carbon::now()->format('Y');
        }
        $query->where('date_of_issue', '>=', "$year-01-01");
        return $query;
    }

    /**
     * @param     $number
     * @param int $decimal
     *
     * @return string
     */
    protected static function NumberFormat($number,$decimal = 2){
        return number_format($number,$decimal,'.','');
    }

    /**
     * @return array
     */
    public function  getCollectionData() {
        $total = $this->total;
        if ($this->total_perception) {
            $total += round($this->total_perception, 2);
        }
        $customer_number = '';
        $customer_name = '';
        $customer = $this->customer;
        if (null !== $customer) {
            $customer = $customer->first();
            if (!empty($customer)) {
                $customer_number = $customer->number;
                $customer_name = $customer->name;
            }
        }
            /*
            alone_number
            internal_id
            brand
            description
            quantity

            lot_has_sale

            web_platform_name
            unit_value
            */


                            // --    total_item_purchase
                            // --    utility_item


        return [
            'id'                             => $this->id,
            'customer_number'                             => $customer_number,
            'customer_name'                             => $customer_name,
            'series'                             => $this->series,
            'document_type_description'      => $this->document_type->description,
            'group_id'                       => $this->group_id,
            'soap_type_id'                   => $this->soap_type_id,
            'date_of_issue'                  => $this->date_of_issue->format('Y-m-d'),
            'date_of_due'                    => ($this->date_of_due) ? $this->date_of_due->format('Y-m-d') : '-',
            'number'                         => $this->number_full,
            'supplier_name'                  => $this->supplier->name,
            'supplier_number'                => $this->supplier->number,
            'currency_type_id'               => $this->currency_type_id,
            'total_exportation'              => $this->total_exportation,
            'total_free'                     => self::NumberFormat($this->total_free),
            'total_unaffected'               => self::NumberFormat($this->total_unaffected),
            'total_exonerated'               => self::NumberFormat($this->total_exonerated),
            'total_taxed'                    => self::NumberFormat($this->total_taxed),
            'total_igv'                      => self::NumberFormat($this->total_igv),
            'total_perception'               => self::NumberFormat($this->total_perception),
            'total'                          => self::NumberFormat($total),
            'state_type_id'                  => $this->state_type_id,
            'state_type_description'         => $this->state_type->description,
            'state_type_payment_description' => $this->total_canceled ? 'Pagado' : 'Pendiente de pago',
            // 'payment_method_type_description' => isset($this->purchase_payments['payment_method_type']['description'])?$this->purchase_payments['payment_method_type']['description']:'-',
            'created_at'                     => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'                     => $this->updated_at->format('Y-m-d H:i:s'),
            'payments'                       => $this->purchase_payments->transform(function ($row, $key) {
                return [
                    'id'                              => $row->id,
                    'payment_method_type_description' => $row->payment_method_type->description,
                    'reference'                       => $row->reference,
                    'payment'                         => $row->payment,
                    'payment_method_type_id'          => $row->payment_method_type_id,
                ];
            }),
            'items'                          => $this->items->transform(function ($row, $key) {
                return [
                    'key'         => $key + 1,
                    'id'          => $row->id,
                    'description' => $row->item->description,
                    'quantity'    => round($row->quantity, 2)
                ];
            }),
            'print_a4'                       => url('')."/purchases/print/{$this->external_id}/a4",
        ];
    }
    }
