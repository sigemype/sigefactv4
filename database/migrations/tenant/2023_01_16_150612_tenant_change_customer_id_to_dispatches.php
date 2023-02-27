<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeCustomerIdToDispatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable()->change();
            $table->json('customer')->nullable()->change();
            $table->string('transport_mode_type_id')->nullable()->change();
            $table->string('transfer_reason_type_id')->nullable()->change();
            $table->json('origin')->nullable()->change();
            $table->json('delivery')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
