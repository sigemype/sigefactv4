<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPointSystemToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('point_system')->default(false)->after('exchange_rate_sale')->comment('indica si el documento se uso en sistema por puntos');
            $table->json('point_system_data')->nullable()->after('point_system')->comment('datos de sistema por puntos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('point_system');
            $table->dropColumn('point_system_data');
        });
    }
}
