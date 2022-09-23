<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsRestrictSellerDiscountToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('restrict_seller_discount')->default(false)->comment('limitar descuento a los vendedores');
            $table->decimal('sellers_discount_limit', 12, 2)->default(0)->comment('limitar descuento a los vendedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('restrict_seller_discount');
            $table->dropColumn('sellers_discount_limit');
        });
    }
}
