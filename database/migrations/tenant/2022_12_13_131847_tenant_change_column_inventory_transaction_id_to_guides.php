<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeColumnInventoryTransactionIdToGuides extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropForeign(['inventory_transaction_id']);	
        });

        Schema::table('guides', function (Blueprint $table) {
            $table->string('inventory_transaction_id')->change();
            $table->foreign('inventory_transaction_id')->references('id')->on('inventory_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropForeign(['inventory_transaction_id']);	
        });

        Schema::table('guides', function (Blueprint $table) {
            $table->string('inventory_transaction_id', 2)->change();
            $table->foreign('inventory_transaction_id')->references('id')->on('inventory_transactions');
        });
    }

}
