<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantUpdateToDispatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->unsignedInteger('dispatcher_id')->nullable()->after('delivery');
            $table->unsignedInteger('driver_id')->nullable()->after('dispatcher');
            $table->unsignedInteger('transport_id')->nullable()->after('license_plate');

            $table->foreign('dispatcher_id')->references('id')->on('dispatchers');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->foreign('transport_id')->references('id')->on('transports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropForeign(['dispatcher_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['transport_id']);

            $table->dropColumn('dispatcher_id');
            $table->dropColumn('driver_id');
            $table->dropColumn('transport_id');
        });
    }
}
