<?php

namespace Modules\Inventory\Entities\Guide;

class GuideItemEntity
{
    public $name;
    public $description;
    public $internal_id;
    public $item_code;
    public $item_code_gs1;
    public $brand_name;
    public $unit_type_id;
    public $unit_type_abbreviation;
    public $presentation_name;
    public $quantity;
    public $quantity_base;
    public $unit_value;
    public $price_type_id;
    public $unit_price;
    public $unit_cost;
    public $subtotal;
    public $affectation_igv_type_id;
    public $total_base_igv;
    public $percentage_igv;
    public $total_igv;
    public $system_isc_type_id;
    public $total_base_isc;
    public $percentage_isc;
    public $total_isc;
    public $total_base_other_taxes;
    public $percentage_other_taxes;
    public $total_other_taxes;
    public $factor_icbper;
    public $total_icbper;
    public $total_taxes;
    public $total_value;
    public $total_charge;
    public $total_discount;
    public $total;
    public $commentary;
}
