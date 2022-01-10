<?php


    namespace Modules\Production\Models;


    use App\Models\Tenant\Item;
    use App\Models\Tenant\ModelTenant;
    use Carbon\Carbon;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    /**
     * Class MillItem
     *
     * @property int         $id
     * @property int|null    $item_id
     * @property int|null    $mill_id
     * @property float|null  $quantity
     * @property float|null  $height_to_mill
     * @property float|null  $total_height
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Item|null   $item
     * @property Mill|null   $mill
     * @package Modules\Production\Models
     */
    class MillItem extends ModelTenant
    {
        use UsesTenantConnection;

        protected $perPage = 25;

        protected $casts = [
            'item_id' => 'int',
            'mill_id' => 'int',
            'height_to_mill' => 'float',
            'quantity' => 'float',
            'total_height' => 'float'
        ];

        protected $fillable = [
            'item_id',
            'item',
            'mill_id',
            'height_to_mill',
            'total_height',
            'quantity'
        ];

        /**
         * @return BelongsTo
         */
        public function item()
        {
            return $this->belongsTo(Item::class);
        }
        /**
         * @return BelongsTo
         */
        public function item_relation()
        {
            return $this->belongsTo(Item::class);
        }

        /**
         * @return BelongsTo
         */
        public function mill()
        {
            return $this->belongsTo(Mill::class);
        }

        /**
         * @param $value
         *
         * @return object|null
         */
        public function getItemAttribute($value)
        {
            return (null === $value) ? null : (object) json_decode($value);
        }

        /**
         * @param $value
         */
        public function setItemAttribute($value)
        {
            $this->attributes['item'] = (null === $value) ? null : json_encode($value);
        }
    }

