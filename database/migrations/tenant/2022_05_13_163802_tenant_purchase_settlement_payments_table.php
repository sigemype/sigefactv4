<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantPurchaseSettlementPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_settlement_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_settlement_id');
            $table->date('date_of_payment');
            $table->char('payment_method_type_id', 2);
            $table->string('reference')->nullable();
            $table->decimal('change',12, 2)->default(0);
            $table->decimal('payment', 12, 2);

            $table->foreign('purchase_settlement_id')->references('id')->on('purchase_settlements')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_settlement_payments');
    }
}
