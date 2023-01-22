<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddApiSunatToCompanies extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('soap_sunat_username', 20)->nullable()->after('ws_api_token');
            $table->string('soap_sunat_password', 20)->nullable()->after('soap_sunat_username');
            $table->string('api_sunat_id', 36)->nullable()->after('soap_sunat_password');
            $table->string('api_sunat_secret', 50)->nullable()->after('api_sunat_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('soap_sunat_username');
            $table->dropColumn('soap_sunat_password');
            $table->dropColumn('api_sunat_id');
            $table->dropColumn('api_sunat_secret');
        });
    }
}
