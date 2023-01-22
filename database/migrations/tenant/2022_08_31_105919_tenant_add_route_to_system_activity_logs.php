<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddRouteToSystemActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_activity_logs', function (Blueprint $table) {
            $table->string('route')->nullable()->after('browser_version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_activity_logs', function (Blueprint $table) {
            $table->dropColumn('route');
        });
    }
}
