<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSunatToCompanies extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('companies', 'soap_sunat_username')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('soap_sunat_username', 20)->nullable()->after('ws_api_token');
                $table->string('soap_sunat_password', 20)->nullable()->after('soap_sunat_username');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
