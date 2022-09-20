<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnHotelRateIdToHotelRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotel_rents', function (Blueprint $table) {
            $table->unsignedInteger('hotel_rate_id')->nullable()->after('hotel_room_id');

			$table->foreign('hotel_rate_id')->references('id')->on('hotel_rates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_rents', function (Blueprint $table) {
            $table->dropForeign('hotel_rents_hotel_rate_id_foreign');
			$table->dropColumn('hotel_rate_id');
        });
    }
}
