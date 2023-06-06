<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantHotelRentItemPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_rent_item_payments', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('hotel_rent_item_id');
            
            $table->date('date_of_payment')->index();
            $table->char('payment_method_type_id', 2);
            $table->string('reference')->nullable();
            $table->decimal('change', 12, 2)->nullable();
            $table->decimal('payment', 12, 2);

            $table->foreign('hotel_rent_item_id')->references('id')->on('hotel_rent_items')->onDelete('cascade');
            $table->foreign('payment_method_type_id')->references('id')->on('payment_method_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_rent_item_payments');
    }
}
