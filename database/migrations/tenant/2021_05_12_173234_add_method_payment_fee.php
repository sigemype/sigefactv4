<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMethodPaymentFee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_fee', function (Blueprint $table) {
            $table->char('cat_payment_method_type_id', 3)->nullable()->after('document_id');
            $table->string('reference')->nullable()->after('currency_type_id');

            $table->foreign('cat_payment_method_type_id')->references('id')->on('cat_payment_method_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_fee');
        Schema::dropIfExists('payment_method_types');
    }
}
