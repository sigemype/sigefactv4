<?php

    namespace Modules\Expense\Models;

    use App\Models\Tenant\CardBrand;
    use App\Models\Tenant\CashDocument;
    use App\Models\Tenant\ModelTenant;
    use Carbon\Carbon;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphOne;
    use Modules\Finance\Models\GlobalPayment;
    use Modules\Finance\Models\PaymentFile;

    /**
     * Class Modules\Expense\Models\ExpensePayment
     *
     * @property int                                  $id
     * @property int                                  $expense_id
     * @property Carbon                               $date_of_payment
     * @property int                                  $expense_method_type_id
     * @property bool                                 $has_card
     * @property string|null                          $card_brand_id
     * @property string|null               $reference
     * @property float                     $payment
     * @property CardBrand|null            $card_brand
     * @property Expense                   $expense
     * @property ExpenseMethodType         $expense_method_type
     * @property Collection|CashDocument[] $cash_documents
     * @method static Builder|ExpensePayment newModelQuery()
     * @method static Builder|ExpensePayment newQuery()
     * @method static Builder|ExpensePayment query()
     * @mixin ModelTenant
     * @property-read Expense              $associated_record_payment
     * @property-read int|null             $cash_documents_count
     * @property-read GlobalPayment|null   $global_payment
     * @property-read PaymentFile|null     $payment_file
     */
    class ExpensePayment extends ModelTenant
    {
        use UsesTenantConnection;

        // protected $with = ['payment_method_type', 'card_brand'];
        public $timestamps = false;

        protected $casts = [
            'expense_id' => 'int',
            'expense_method_type_id' => 'int',
            'has_card' => 'bool',
            'date_of_payment' => 'date',
            'payment' => 'float'
        ];

        protected $fillable = [
            'expense_id',
            'date_of_payment',
            'expense_method_type_id',
            'has_card',
            'card_brand_id',
            'reference',
            'payment',
        ];

        /**
         * @return BelongsTo
         */
        public function expense()
        {
            return $this->belongsTo(Expense::class);
        }

        /**
         * @return BelongsTo
         */
        public function expense_method_type()
        {
            return $this->belongsTo(ExpenseMethodType::class);
        }

        /**
         * @return BelongsTo
         */
        public function card_brand()
        {
            return $this->belongsTo(CardBrand::class);
        }

        /**
         * @return MorphOne
         */
        public function global_payment()
        {
            return $this->morphOne(GlobalPayment::class, 'payment');
        }

        /**
         * @return BelongsTo
         */
        public function associated_record_payment()
        {
            return $this->belongsTo(Expense::class, 'expense_id');
        }

        /**
         * @return MorphOne
         */
        public function payment_file()
        {
            return $this->morphOne(PaymentFile::class, 'payment');
        }

        /**
         * @return HasMany
         */
        public function cash_documents()
        {
            return $this->hasMany(CashDocument::class);
        }
    }
