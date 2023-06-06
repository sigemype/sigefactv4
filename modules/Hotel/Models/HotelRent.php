<?php

    namespace Modules\Hotel\Models;

    use App\Models\Tenant\Item;
    use App\Models\Tenant\ModelTenant;
    use Carbon\Carbon;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use App\Models\Tenant\Person;
    use Modules\Hotel\Models\HotelRoomRate;

    /**
     * Class \Modules\Hotel\Models\HotelRent
     *
     * @property int                                  $id
     * @property int                                  $customer_id
     * @property string                               $customer
     * @property string|null                          $notes
     * @property int                                  $towels
     * @property int                                  $hotel_room_id
     * @property int                                  $duration
     * @property int                                  $quantity_persons
     * @property Carbon|null                          $input_date
     * @property string|null                          $input_time
     * @property string                               $payment_status
     * @property Carbon                               $output_date
     * @property string                               $output_time
     * @property int                                  $arrears
     * @property string                               $status
     * @property Carbon|null                          $created_at
     * @property Carbon|null                          $updated_at
     * @property Collection|Item[]                    $items
     * @property Collection|Item[]                    $products
     * @method static Builder|HotelRent newModelQuery()
     * @method static Builder|HotelRent newQuery()
     * @method static Builder|HotelRent query()
     * @method static Builder|HotelRent SearchByDate()
     * @mixin ModelTenant
     * @mixin \Eloquent
     * @property-read int|null                        $items_count
     * @property-read int|null                        $products_count
     * @property-read \Modules\Hotel\Models\HotelRoom $room
     */
    class HotelRent extends ModelTenant
    {
        use UsesTenantConnection;
        protected $table = 'hotel_rents';

        protected $fillable = [
            'customer_id',
            'customer',
            'notes',
            'towels',
            'hotel_room_id',
            'hotel_rate_id',
            'duration',
            'quantity_persons',
            'payment_status',
            'output_date',
            'output_time',
            'input_date',
            'input_time',
            'arrears',
            'status'
        ];

        protected $casts = [
            'customer_id' => 'int',
            'towels' => 'int',
            'hotel_room_id' => 'int',
            'duration' => 'int',
            'quantity_persons' => 'int',
            'arrears' => 'int'
        ];

        public function getCustomerAttribute($value)
        {
            return (is_null($value)) ? null : (object)json_decode($value);
        }

        public function setCustomerAttribute($value)
        {
            $this->attributes['customer'] = (is_null($value)) ? null : json_encode($value);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function room()
        {
            return $this->belongsTo(HotelRoom::class, 'hotel_room_id');
        }

        public function rate()
        {
            return $this->belongsTo(HotelRate::class, 'hotel_rate_id');
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function items()
        {
            return $this->hasMany(HotelRentItem::class, 'hotel_rent_id');
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function products()
        {
            return $this->hasMany(HotelRentItem::class, 'hotel_rent_id')->where('type','PRO');
        }


        /**
         * @param Builder $query
         * @param null    $date_start
         * @param null    $date_end
         *
         * @return Builder
         */
        public function scopeSearchByDate(Builder $query, $date_start, $date_end)
        {

            if ($date_start) {
                $query->where('input_date', '>=', $date_start);
            }
            if ($date_end) {
                $query->where('output_date', '<=', $date_end);
            }

            return $query;
        }

        public function searchPersonDetails($value)
        {
            $id ='';

            if ($value->customer->id) {
                $id = $value->customer->id;
                $data=Person::with('identity_document_type', 'country')
                ->orderBy('id', 'DESC');
                return $data = $data->where('id',$id)->get();
            }
        }

        public function searchPersonNationality($value)
        {
            $id ='';

            if ($value->customer->id) {
                $id = $value->customer->id;
                $data=Person::with('nationality')
                ->orderBy('id', 'DESC');
                return $data = $data->where('id',$id)->get();
            }
        }

        public function searchRateRoom($value)
        {
            $id ='';
            $room_id='';
            if ($value->rate) {
                if ($value->rate->id) {
                    $id = $value->rate->id;
                    $room_id=$value->hotel_room_id;
                    return $data = HotelRoomRate::where('hotel_rate_id',$id)->where('hotel_room_id',$room_id)->get();
                }
                
            }
        }
        
        
        /**
         * Retorna moneda nacional por defecto
         * 
         * @TODO considerar registro de moneda al rentar habitacion
         *
         * @return string
         */
        public function getDefaultCurrency()
        {
            return self::NATIONAL_CURRENCY_ID;
        }

    }
