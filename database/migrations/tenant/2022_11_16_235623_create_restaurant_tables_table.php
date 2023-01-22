<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestaurantTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->json('products')->nullable();
            $table->decimal('total', 12, 2);
            $table->integer('personas');
            $table->string('cliente')->nullable();
            $table->string('comentarios')->nullable();
            $table->string('label');
            $table->string('shape');
            $table->string('environment');
            $table->string('waiter')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_tables');
    }
}
