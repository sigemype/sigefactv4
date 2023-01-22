<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestaurantTableEnvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_table_envs', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary()->unique()->unique();
            $table->string('name');
            $table->boolean('active');
            $table->integer('tables_quantity');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_table_envs');
    }
}
