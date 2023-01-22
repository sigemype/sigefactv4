<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDirectSendDocumentsWhatsappToAppConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->boolean('direct_send_documents_whatsapp')->default(false)->after('header_waves');
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
            $table->dropColumn('direct_send_documents_whatsapp');
        });
    }
}
