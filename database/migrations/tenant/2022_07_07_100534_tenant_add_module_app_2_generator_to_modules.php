<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use App\CoreFacturalo\Helpers\Functions\SetDataHelper;


class TenantAddModuleApp2GeneratorToModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $module_data = SetDataHelper::getModuleData('app_2_generator', 'Generador APP 2.0', 20);
        $find_row = SetDataHelper::getModuleConnection('tenant')->where($module_data)->first();

        if (!$find_row) 
        {
            SetDataHelper::getModuleConnection('tenant')->insert($module_data);
        }
        
    }

 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $module_data = SetDataHelper::getModuleData('app_2_generator', 'Generador APP 2.0', 20);
        $find_row = SetDataHelper::getModuleConnection('tenant')->where($module_data)->first();

        if ($find_row) 
        {
            SetDataHelper::getModuleConnection('tenant')->delete($find_row->id);
        }
    }

}
