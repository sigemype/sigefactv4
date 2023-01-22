<?php

namespace Modules\Restaurant\Models;

use App\Models\Tenant\ModelTenant;
use Modules\Restaurant\Models\RestaurantTable;

class RestaurantConfiguration extends ModelTenant
{
    protected $fillable = [
        'menu_pos',
        'menu_order',
        'menu_tables',
        'first_menu',
        'menu_bar',
        'menu_kitchen',
        'enabled_environment_1',
        'enabled_environment_2',
        'enabled_environment_3',
        'enabled_environment_4',
        'items_maintenance',
        'tables_quantity',
        'tables_quantity_environment_2',
        'tables_quantity_environment_3',
        'tables_quantity_environment_4'

    ];

    public $timestamps = false;

    public function getCollectionData() {
        return [
            'menu_pos' => (bool)$this->menu_pos,
            'menu_order' => (bool)$this->menu_order,
            'menu_tables' => (bool)$this->menu_tables,
            'menu_bar' => (bool)$this->menu_bar,
            'menu_kitchen' => (bool)$this->menu_kitchen,
            'first_menu' => $this->first_menu,
            'tables_quantity' => $this->tables_quantity,
            'tables_quantity_environment_2' => $this->tables_quantity_environment_2,
            'tables_quantity_environment_3' => $this->tables_quantity_environment_3,
            'tables_quantity_environment_4' => $this->tables_quantity_environment_4,
            'enabled_environment_1' => (bool)$this->enabled_environment_1,
            'enabled_environment_2' => (bool)$this->enabled_environment_2,
            'enabled_environment_3' => (bool)$this->enabled_environment_3,
            'enabled_environment_4' => (bool)$this->enabled_environment_4,
            'items_maintenance' => (bool)$this->items_maintenance
        ];
    }
}
