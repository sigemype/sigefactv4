<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchSaleNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatch_sale_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sale_note_id');
            $table->date('date_dispatch')->nullable();
            $table->time('time_dispatch', $precision = 0)->nullable();
            $table->string('person_pick')->nullable();
            $table->string('reference')->nullable();
            $table->string('person_dispatch')->nullable();
            $table->boolean('status')->nullable();
            $table->foreign('sale_note_id')->references('id')->on('sale_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dispatch_sale_notes');
    }
}
