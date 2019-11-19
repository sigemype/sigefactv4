<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class DetractionType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_detraction_types";
    public $incrementing = false;
}