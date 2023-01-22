<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\CoreFacturalo\Helpers\Functions\SetDataHelper;

class AddDataDistrict050511ToDistricts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        SetDataHelper::createDistrict('050511', 'Oronccoy');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        SetDataHelper::deleteDistrict('050511');
    }
}
