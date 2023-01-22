<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddQuantityMesasAndAndEnv4ToEnvironmentRestaurant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->integer('tables_quantity_environment_2')->default(5)->after('enabled_environment_2');
            $table->integer('tables_quantity_environment_3')->default(5)->after('enabled_environment_3');
            $table->boolean('enabled_environment_4')->default(false);
            $table->integer('tables_quantity_environment_4')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->dropColumn(['tables_quantity_environment_2', 'tables_quantity_environment_3', 'enabled_environment_4', 'tables_quantity_environment_4']);
        });
    }
}
