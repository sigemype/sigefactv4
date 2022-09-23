<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsPointSystemToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('enabled_point_system')->default(false)->comment('sistema por puntos');
            $table->decimal('point_system_sale_amount', 12, 2)->default(1)->comment('sistema por puntos');
            $table->decimal('quantity_of_points', 12, 2)->default(1)->comment('sistema por puntos');
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
            $table->dropColumn('enabled_point_system');
            $table->dropColumn('point_system_sale_amount');
            $table->dropColumn('quantity_of_points');
        });
    }
}
