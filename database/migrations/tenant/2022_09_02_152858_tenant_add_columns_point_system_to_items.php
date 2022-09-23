<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsPointSystemToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('exchange_points')->after('has_isc')->default(false)->comment('sistema por puntos');
            $table->decimal('quantity_of_points', 12, 2)->after('exchange_points')->default(0)->comment('sistema por puntos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('exchange_points');
            $table->dropColumn('quantity_of_points');
        });
    }
}
