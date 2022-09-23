<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddInventoryReviewToInventoryConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_configurations', function (Blueprint $table) {
            $table->boolean('inventory_review')->default(false)->after('stock_control');
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
            $table->dropColumn('inventory_review');
        });
    }
}
