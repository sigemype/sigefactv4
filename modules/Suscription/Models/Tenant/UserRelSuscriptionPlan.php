<?php

    /**
     */

    namespace Modules\Suscription\Models\Tenant;


    use App\Models\Tenant\ModelTenant;
    use App\Models\Tenant\Person;
    use App\Models\Tenant\User;
    use Carbon\Carbon;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * Class UserRelSuscriptionPlan
     *
     * @property int                            $id
     * @property int|null                       $user_id
     * @property int|null                       $suscription_plan_id
     * @property int|null                       $cat_period_id
     * @property string                         $items
     * @property bool                           $editable
     * @property bool                           $deletable
     * @property string|null                    $start_date
     * @property Carbon|null                    $created_at
     * @property Carbon|null                    $updated_at
     * @property CatPeriod|null                 $cat_period
     * @property SuscriptionPlan|null           $suscription_plan
     * @property User|null                      $user
     * @property int|null                       $quantity_period
     * @property int|null                       $children_customer_id
     * @property \App\Models\Tenant\Person|null $children_customer_relation
     * @property int|null                       $parent_customer_id
     * @property \App\Models\Tenant\Person|null $parent_customer_relation
     * @property int|null                       $customer_id
     * @property \App\Models\Tenant\Person|null $customer_relation
     * @property Carbon|null                    $automatic_date_of_issue
     * @property string|null                    $dates_of_documents
     * @property string|null                    $documents
     * @property string|null                    $sale_notes
     * @property string                         $customer
     * @property string                         $parent_customer
     * @property string                         $children_customer
     * @property bool                           $apply_concurrency
     * @property bool                           $enabled_concurrency
     * @property float|null                     $total
     * @property string|null                    $currency_type_id
     * @property string|null                    $payment_method_type_id
     * @property float|null                     $exchange_rate_sale
     * @property float|null                     $total_prepayment
     * @property float|null                     $total_charge
     * @property float|null                     $total_discount
     * @property float|null                     $total_exportation
     * @property float|null                     $total_free
     * @property float|null                     $total_taxed
     * @property float|null                     $total_unaffected
     * @property float|null                     $total_exonerated
     * @property float|null                     $total_igv
     * @property float|null                     $total_igv_free
     * @property float|null                     $total_base_isc
     * @property float|null                     $total_isc
     * @property float|null                     $total_base_other_taxes
     * @property float|null                     $total_other_taxes
     * @property float|null                     $total_taxes
     * @property float|null                     $total_value
     * @property string|null                    $charges
     * @property string|null                    $attributes
     * @property string|null                    $discounts
     * @property string|null                    $prepayments
     * @property string|null                    $related
     * @property string|null                    $perception
     * @property string|null                    $detraction
     * @property string|null                    $legends
     * @property string|null                    $terms_condition
     * @package App\Models\Tenant\ModelTenant
     * @method static Builder|UserRelSuscriptionPlan newModelQuery()
     * @method static Builder|UserRelSuscriptionPlan newQuery()
     * @method static Builder|UserRelSuscriptionPlan query()
     * @mixin \Eloquent
     *
     *
     *
     */
    class UserRelSuscriptionPlan extends ModelTenant
    {
        use UsesTenantConnection;

        protected $casts = [
            'user_id' => 'int',
            'suscription_plan_id' => 'int',
            'cat_period_id' => 'int',
            'editable' => 'bool',
            'deletable' => 'bool',
            'total' => 'float',
            'quantity_period' => 'int',
            'children_customer_id' => 'int',
            'parent_customer_id' => 'int',
            'customer_id' => 'int',
            'apply_concurrency' => 'bool',
            'enabled_concurrency' => 'bool',
            'start_date' => 'date',
            'automatic_date_of_issue' => 'date',
            'exchange_rate_sale' => 'float',
            'total_prepayment' => 'float',
            'total_charge' => 'float',
            'total_discount' => 'float',
            'total_exportation' => 'float',
            'total_free' => 'float',
            'total_taxed' => 'float',
            'total_unaffected' => 'float',
            'total_exonerated' => 'float',
            'total_igv' => 'float',
            'total_igv_free' => 'float',
            'total_base_isc' => 'float',
            'total_isc' => 'float',
            'total_base_other_taxes' => 'float',
            'total_other_taxes' => 'float',
            'total_taxes' => 'float',
            'total_value' => 'float'
        ];


        protected $fillable = [
            'user_id',
            'suscription_plan_id',
            'cat_period_id',
            'items',
            'editable',
            'deletable',
            'start_date',
            'quantity_period',
            'children_customer_id',
            'parent_customer_id',
            'customer_id',
            'automatic_date_of_issue',
            'dates_of_documents',
            'documents',
            'sale_notes',
            'customer',
            'parent_customer',
            'children_customer',
            'apply_concurrency',
            'enabled_concurrency',


            'total',
            'currency_type_id',
            'payment_method_type_id',
            'exchange_rate_sale',
            'total_prepayment',
            'total_charge',
            'total_discount',
            'total_exportation',
            'total_free',
            'total_taxed',
            'total_unaffected',
            'total_exonerated',
            'total_igv',
            'total_igv_free',
            'total_base_isc',
            'total_isc',
            'total_base_other_taxes',
            'total_other_taxes',
            'total_taxes',
            'total_value',
            'charges',
            'attributes',
            'discounts',
            'prepayments',
            'related',
            'perception',
            'detraction',
            'legends',
            'terms_condition'
        ];

        public static function boot()
        {
            parent::boot();
            static::saving(function (self $item) {

            });
        }

        public function getCollectionData()
        {
            $data = $this->toArray();

            return $data;
        }


        public function getCustomerAttribute($value)
        {
            return ($value === null) ? null : (object)json_decode($value);
        }

        public function setCustomerAttribute($value)
        {
            $this->attributes['customer'] = json_encode($value ?? []);
        }

        public function getChildrenCustomerAttribute($value)
        {
            return ($value === null) ? null : (object)json_decode($value);
        }

        public function setChildrenCustomerAttribute($value)
        {
            $this->attributes['children_customer'] = json_encode($value ?? []);
        }

        public function getParentCustomerAttribute($value)
        {
            return ($value === null) ? null : (object)json_decode($value);
        }

        public function setParentCustomerAttribute($value)
        {
            $this->attributes['parent_customer'] = json_encode($value ?? []);
        }

        public function getItemsAttribute($value)
        {
            return ($value === null) ? null : (object)json_decode($value);
        }

        public function setItemsAttribute($value)
        {
            $this->attributes['items'] = json_encode($value ?? []);
        }


        /**
         * @return \Modules\Suscription\Models\Tenant\CatPeriod|null
         */
        public function getCatPeriod(): ?CatPeriod
        {
            return $this->cat_period;
        }

        /**
         * @param \Modules\Suscription\Models\Tenant\CatPeriod|null $cat_period
         *
         * @return UserRelSuscriptionPlan
         */
        public function setCatPeriod(?CatPeriod $cat_period): UserRelSuscriptionPlan
        {
            $this->cat_period = $cat_period;
            return $this;
        }

        /**
         * @return \Modules\Suscription\Models\Tenant\SuscriptionPlan|null
         */
        public function getSuscriptionPlan(): ?SuscriptionPlan
        {
            return $this->suscription_plan;
        }

        /**
         * @param \Modules\Suscription\Models\Tenant\SuscriptionPlan|null $suscription_plan
         *
         * @return UserRelSuscriptionPlan
         */
        public function setSuscriptionPlan(?SuscriptionPlan $suscription_plan): UserRelSuscriptionPlan
        {
            $this->suscription_plan = $suscription_plan;
            return $this;
        }

        /**
         * @return \App\Models\Tenant\User|null
         */
        public function getUser(): ?User
        {
            return $this->user;
        }

        /**
         * @param \App\Models\Tenant\User|null $user
         *
         * @return UserRelSuscriptionPlan
         */
        public function setUser(?User $user): UserRelSuscriptionPlan
        {
            $this->user = $user;
            return $this;
        }

        /**
         * @return int|null
         */
        public function getQuantityPeriod(): ?int
        {
            return $this->quantity_period;
        }

        /**
         * @param int|null $quantity_period
         *
         * @return UserRelSuscriptionPlan
         */
        public function setQuantityPeriod(?int $quantity_period): UserRelSuscriptionPlan
        {
            $this->quantity_period = $quantity_period;
            return $this;
        }

        /**
         * @return \App\Models\Tenant\Person|null
         */
        public function getChildrenCustomerRelation(): ?Person
        {
            return $this->children_customer_relation;
        }

        /**
         * @param \App\Models\Tenant\Person|null $children_customer_relation
         *
         * @return UserRelSuscriptionPlan
         */
        public function setChildrenCustomerRelation(?Person $children_customer_relation): UserRelSuscriptionPlan
        {
            $this->children_customer_relation = $children_customer_relation;
            return $this;
        }

        /**
         * @return \App\Models\Tenant\Person|null
         */
        public function getCustomerRelation(): ?Person
        {
            return $this->customer_relation;
        }

        /**
         * @param \App\Models\Tenant\Person|null $customer_relation
         *
         * @return UserRelSuscriptionPlan
         */
        public function setCustomerRelation(?Person $customer_relation): UserRelSuscriptionPlan
        {
            $this->customer_relation = $customer_relation;
            return $this;
        }

        /**
         * @return \Carbon\Carbon|null
         */
        public function getAutomaticDateOfIssue(): ?Carbon
        {
            return $this->automatic_date_of_issue;
        }

        /**
         * @param \Carbon\Carbon|null $automatic_date_of_issue
         *
         * @return UserRelSuscriptionPlan
         */
        public function setAutomaticDateOfIssue(?Carbon $automatic_date_of_issue): UserRelSuscriptionPlan
        {
            $this->automatic_date_of_issue = $automatic_date_of_issue;
            return $this;
        }

        /**
         * @return string|null
         */
        public function getDatesOfDocuments(): ?string
        {
            return $this->dates_of_documents;
        }

        /**
         * @param string|null $dates_of_documents
         *
         * @return UserRelSuscriptionPlan
         */
        public function setDatesOfDocuments(?string $dates_of_documents): UserRelSuscriptionPlan
        {
            $this->dates_of_documents = $dates_of_documents;
            return $this;
        }

        /**
         * @return string|null
         */
        public function getDocuments(): ?string
        {
            return $this->documents;
        }

        /**
         * @param string|null $documents
         *
         * @return UserRelSuscriptionPlan
         */
        public function setDocuments(?string $documents): UserRelSuscriptionPlan
        {
            $this->documents = $documents;
            return $this;
        }

        /**
         * @return string|null
         */
        public function getSaleNotes(): ?string
        {
            return $this->sale_notes;
        }

        /**
         * @param string|null $sale_notes
         *
         * @return UserRelSuscriptionPlan
         */
        public function setSaleNotes(?string $sale_notes): UserRelSuscriptionPlan
        {
            $this->sale_notes = $sale_notes;
            return $this;
        }

        /**
         * @return string
         */
        public function getCustomer(): string
        {
            return $this->customer;
        }

        /**
         * @param string $customer
         *
         * @return UserRelSuscriptionPlan
         */
        public function setCustomer(string $customer): UserRelSuscriptionPlan
        {
            $this->customer = $customer;
            return $this;
        }

        /**
         * @return string
         */
        public function getParentCustomer(): string
        {
            return $this->parent_customer;
        }

        /**
         * @param string $parent_customer
         *
         * @return UserRelSuscriptionPlan
         */
        public function setParentCustomer(string $parent_customer): UserRelSuscriptionPlan
        {
            $this->parent_customer = $parent_customer;
            return $this;
        }


        /**
         * @return bool
         */
        public function isApplyConcurrency(): bool
        {
            return $this->apply_concurrency;
        }


        /**
         * @param bool $apply_concurrency
         *
         * @return UserRelSuscriptionPlan
         */
        public function setApplyConcurrency(bool $apply_concurrency): UserRelSuscriptionPlan
        {
            $this->apply_concurrency = $apply_concurrency;
            return $this;
        }

        /**
         * @return bool
         */
        public function isEnabledConcurrency(): bool
        {
            return $this->enabled_concurrency;
        }

        /**
         * @param bool $enabled_concurrency
         *
         * @return UserRelSuscriptionPlan
         */
        public function setEnabledConcurrency(bool $enabled_concurrency): UserRelSuscriptionPlan
        {
            $this->enabled_concurrency = $enabled_concurrency;
            return $this;
        }

        /**
         * @return int|null
         */
        public function getChildrenCustomerId(): ?int
        {
            return $this->children_customer_id;
        }


        /**
         * @return int|null
         */
        public function getParentCustomerId(): ?int
        {
            return $this->parent_customer_id;
        }

        /**
         * @return int|null
         */
        public function getCustomerId(): ?int
        {
            return $this->customer_id;
        }


        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function cat_period()
        {
            return $this->belongsTo(CatPeriod::class);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function suscription_plan()
        {
            return $this->belongsTo(SuscriptionPlan::class);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function user()
        {
            return $this->belongsTo(User::class);
        }

        /**
         * @return int
         */
        public function getUserId(): int
        {
            return $this->user_id;
        }

        /**
         * @param int $user_id
         *
         * @return UserRelSuscriptionPlan
         */
        public function setUserId(int $user_id): UserRelSuscriptionPlan
        {
            $this->user_id = $user_id;
            return $this;
        }

        /**
         * @return int
         */
        public function getSuscriptionPlanId(): int
        {
            return $this->suscription_plan_id;
        }

        /**
         * @param int $suscription_plan_id
         *
         * @return UserRelSuscriptionPlan
         */
        public function setSuscriptionPlanId(int $suscription_plan_id): UserRelSuscriptionPlan
        {
            $this->suscription_plan_id = $suscription_plan_id;
            return $this;
        }

        /**
         * @return int
         */
        public function getCatPeriodId(): int
        {
            return $this->cat_period_id;
        }

        /**
         * @param int $cat_period_id
         *
         * @return UserRelSuscriptionPlan
         */
        public function setCatPeriodId(int $cat_period_id): UserRelSuscriptionPlan
        {
            $this->cat_period_id = $cat_period_id;
            return $this;
        }


        /**
         * @return bool
         */
        public function isEditable(): bool
        {
            return $this->editable;
        }

        /**
         * @param bool $editable
         *
         * @return UserRelSuscriptionPlan
         */
        public function setEditable(bool $editable): UserRelSuscriptionPlan
        {
            $this->editable = $editable;
            return $this;
        }

        /**
         * @return bool
         */
        public function isDeletable(): bool
        {
            return $this->deletable;
        }

        /**
         * @param bool $deletable
         *
         * @return UserRelSuscriptionPlan
         */
        public function setDeletable(bool $deletable): UserRelSuscriptionPlan
        {
            $this->deletable = $deletable;
            return $this;
        }

        /**
         * @return string|null
         */
        public function getStartDate(): ?string
        {
            return $this->start_date;
        }

        /**
         * @param string|null $start_date
         *
         * @return UserRelSuscriptionPlan
         */
        public function setStartDate(?string $start_date): UserRelSuscriptionPlan
        {
            if (empty($start_date)) $start_date = Carbon::now()->format('Y-m-d');
            $this->start_date = $start_date;
            return $this;
        }


    }
