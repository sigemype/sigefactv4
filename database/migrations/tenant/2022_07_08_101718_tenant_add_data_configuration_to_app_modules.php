<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDataConfigurationToAppModules extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('app_modules')->insert([ 
            ['value' => 'configuration', 'description' => 'Configuración', 'order_menu' => 12],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        DB::table('app_modules')->where('value', 'configuration')->delete();
    }

}
