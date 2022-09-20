<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddAdditionalDataToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('documents', 'state_type_sunat_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->json('additional_data')->nullable()->after('additional_information');
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
        if (Schema::hasColumn('documents', 'state_type_sunat_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('additional_data');
            });
        }
    }
}
