<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantGuideItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guide_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('guide_id');
            $table->unsignedInteger('item_id');
            $table->string('item_name');
            $table->decimal('quantity', 20, 8);
            $table->decimal('unit_cost', 20, 8);
            $table->decimal('total', 12, 2);

            $table->foreign('guide_id')->references('id')->on('guides')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guide_items');
    }
}
