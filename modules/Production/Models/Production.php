<?php


    namespace Modules\Production\Models;


    use App\Models\Tenant\Item;
    use App\Models\Tenant\ModelTenant;
    use App\Models\Tenant\User;
    use Carbon\Carbon;
    use Eloquent;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    /**
     * Class Production
     *
     * @property int         $id
     * @property int|null    $user_id
     * @property int|null    $item_id
     * @property float|null  $quantity
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property int|null    $inventory_id_reference
     * @property int|null    $machine_id
     * @property string|null $production_order
     * @property string|null $name
     * @property string|null $comment
     * @property Carbon|null $date_start
     * @property Carbon|null $time_start
     * @property Carbon|null $date_end
     * @property Carbon|null $time_end
     * @property Machine $machine
     * @package Modules\Production\Models
     * @property-read Item   $item
     * @property-read User   $user
     * @method static Builder|Production newModelQuery()
     * @method static Builder|Production newQuery()
     * @method static Builder|Production query()
     * @mixin Eloquent
     */
    class Production extends ModelTenant
    {
        use UsesTenantConnection;

        protected $table = 'production';
        protected $perPage = 25;


        protected $casts = [
            'user_id' => 'int',
            'item_id' => 'int',
            'quantity' => 'float',
            'inventory_id_reference' => 'int',
            'machine_id' => 'int'
        ];

        protected $fillable = [
            'user_id',
            'item_id',
            'quantity',
            'inventory_id_reference',
            'machine_id',
            'production_order',
            'name',
            'date_start',
            'time_start',
            'date_end',
            'time_end',
            'comment',
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
        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function getCollectionData()
        {

            $data = $this->toArray();
            $data['machine'] = $this->machine;
            $data['item'] = $this->item;
            $data['item_supply'] = $this->item->supplies;
            $data['user'] = $this->user->name;
            $data['quantity'] = $this->quantity;
            $data['item_name'] = $this->item->description;
            $data['created_at'] = $this->created_at->format('Y-m-d H:i:s');
            return $data;
        }


        public function machine()
        {
            return $this->belongsTo(Machine::class);
        }
    }
