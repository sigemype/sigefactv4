<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPurchaseSettlementIdToKardex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kardex', function (Blueprint $table) {

            $table->unsignedInteger('purchase_settlement_id')->nullable()->after('purchase_id');
            $table->foreign('purchase_settlement_id')->references('id')->on('purchase_settlements')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kardex', function (Blueprint $table) {
            $table->dropForeign(['purchase_settlement_id']);
            $table->dropColumn('purchase_settlement_id');     
        });
    }
}
