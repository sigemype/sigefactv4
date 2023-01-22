<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDiscountToPersons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->boolean('has_discount')->default(false)->after('seller_id');
            $table->char('discount_type', 2)->default('01')->after('has_discount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('has_discount');
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_amount');
        });
    }
}
