<?php

namespace Modules\Hotel\Models;

use App\Models\Tenant\ModelTenant;

class HotelRentItem extends ModelTenant
{
	protected $table = 'hotel_rent_items';

	protected $fillable = [
		'type',
		'hotel_rent_id',
		'item_id',
		'item',
		'payment_status',
	];

	public function getItemAttribute($value)
	{
		return (is_null($value)) ? null : (object) json_decode($value);
	}

	public function setItemAttribute($value)
	{
		$this->attributes['item'] = (is_null($value)) ? null : json_encode($value);
	}

    public function payments()
    {
        return $this->hasOne(HotelRentItemPayment::class, 'hotel_rent_item_id');
    }

	public function hotel_rent()
	{
		return $this->belongsTo(HotelRent::class);
	}


	/**
	 * Validar si se encuentra pagado
	 *
	 * @return bool
	 */
	public function isPaid()
	{
		return $this->payment_status === 'PAID';
	}

	
	/**
	 * 
	 * Descripcion dependiendo del tipo, habitacion o producto
	 *
	 * @return string
	 */
	public function getDescriptionFromType()
	{
		return $this->type == 'HAB' ? 'Renta habitación' : 'Venta producto';
	}

	
    /**
     *
     * Obtener relaciones necesarias o aplicar filtros para reporte pagos
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeFilterRelationsGlobalPayment($query)
    {
        return $query->with([
			'hotel_rent' => function ($q) {
				$q->select([
					'id',
					'customer_id',
					'customer',
					'payment_status',
					'input_date'
				]);
			},
		]);
    }

}
