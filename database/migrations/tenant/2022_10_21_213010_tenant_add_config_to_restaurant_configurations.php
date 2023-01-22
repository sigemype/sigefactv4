<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddConfigToRestaurantConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurant_configurations', function (Blueprint $table) {
            $table->boolean('items_maintenance')->default(false)->after('menu_kitchen');
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
            $table->dropColumn(['items_maintenance']);
        });
    }
}
