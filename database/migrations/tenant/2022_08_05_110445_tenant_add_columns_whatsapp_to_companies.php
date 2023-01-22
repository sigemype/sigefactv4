<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsWhatsappToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('ws_api_token')->nullable()->after('url_signature_pse');
            $table->string('ws_api_phone_number_id')->nullable()->after('url_signature_pse');
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
            $table->dropColumn('ws_api_token');
            $table->dropColumn('ws_api_phone_number_id');
        });
    }
}
