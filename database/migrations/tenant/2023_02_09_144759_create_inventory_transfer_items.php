<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTransferItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_transfer_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inventory_transfer_id');
            $table->foreign('inventory_transfer_id')->references('id')->on('inventories_transfer');

            $table->unsignedInteger('item_lots_group_id')->nullable();
            $table->foreign('item_lots_group_id')->references('id')->on('item_lots_group');

            $table->unsignedInteger('item_lot_id')->nullable();
            $table->foreign('item_lot_id')->references('id')->on('item_lots');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_transfer_items');
    }
}
