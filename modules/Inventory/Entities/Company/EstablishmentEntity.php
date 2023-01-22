<?php

namespace Modules\Inventory\Entities\Company;

class EstablishmentEntity
{
    public $code;
    public $name;
    public $country_id;
    public $location_id;
    public $location_name;
    public $urbanization;
    public $address;
    public $email;
    public $phone;
    public $cellphone;
    public $web;
    public $department_name;
    public $province_name;
    public $district_name;

    public $print_header_text;
    public $print_footer_text;
    public $print_general_conditions;
    public $print_only_total;

    public $watermark_image_url;
    public $watermark_image_width;
    public $watermark_image_height;

    public $logo_url;
    public $logo_public_path;
    public $print_logo;
}
