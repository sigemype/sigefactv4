<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsPointSystemToSaleNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->boolean('point_system')->default(false)->after('exchange_rate_sale')->comment('indica si se uso en sistema por puntos');
            $table->json('point_system_data')->nullable()->after('point_system')->comment('datos de sistema por puntos');
            $table->boolean('created_from_pos')->default(false)->after('point_system_data')->comment('indica si se registro desde pos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->dropColumn('point_system');
            $table->dropColumn('point_system_data');
            $table->dropColumn('created_from_pos');
        });
    }
}
