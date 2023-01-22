<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddEnvironmentToRestaurantConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->boolean('enabled_environment_1')->default(true);
            $table->boolean('enabled_environment_2')->default(false);
            $table->boolean('enabled_environment_3')->default(false);
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
            $table->dropColumn(['enabled_environment_1', 'enabled_environment_2', 'enabled_environment_3']);
        });
    }
}
