<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDashboardOptionsToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('dashboard_sales')->default(true);
            $table->boolean('dashboard_general')->default(true);
            $table->boolean('dashboard_clients')->default(true);
            $table->boolean('dashboard_products')->default(false);
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
            $table->dropColumn('dashboard_sales');
            $table->dropColumn('dashboard_general');
            $table->dropColumn('dashboard_clients');
            $table->dropColumn('dashboard_products');
        });
    }
}
