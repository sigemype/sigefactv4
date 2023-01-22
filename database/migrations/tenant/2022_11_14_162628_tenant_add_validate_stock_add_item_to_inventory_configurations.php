<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddValidateStockAddItemToInventoryConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_configurations', function (Blueprint $table) {
            $table->boolean('validate_stock_add_item')->default(false)->after('stock_control');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_configurations', function (Blueprint $table) {
            $table->dropColumn('validate_stock_add_item');
        });
    }
}
