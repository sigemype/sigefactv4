<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddAppModeToAppConfigurations extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->string('app_mode')->default('default')->after('header_waves');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->dropColumn('app_mode');
        });
    }

}
