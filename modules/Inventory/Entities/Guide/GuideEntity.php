<?php

namespace Modules\Inventory\Entities\Guide;

use Modules\Inventory\Entities\Company\CompanyEntity;
use Modules\Inventory\Entities\Company\EstablishmentEntity;

class GuideEntity
{
    /**
     * @var CompanyEntity
     */
    public $company;

    /**
     * @var EstablishmentEntity
     */
    public $establishment;

    /**
     * @var GuideItemEntity[]
     */
    public $items;

    public $inventory_transaction_name;

    public $user_name;

    public $observations;
}
