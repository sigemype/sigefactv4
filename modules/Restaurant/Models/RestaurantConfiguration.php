<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;

class RestaurantConfiguration extends ModelTenant
{
    protected $fillable = [
        'menu_pos',
        'menu_order',
        'menu_tables',
        'first_menu',
        'tables_quantity'
    ];

    public $timestamps = false;

    public function getCollectionData() {
        return [
            'menu_pos' => (bool)$this->menu_pos,
            'menu_order' => (bool)$this->menu_order,
            'menu_tables' => (bool)$this->menu_tables,
            'first_menu' => $this->first_menu,
            'tables_quantity' => $this->tables_quantity
        ];
    }
}
