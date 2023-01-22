<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSeriesAndNumberToInventoriesTransfer extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventories_transfer', function (Blueprint $table) {
            $table->uuid('external_id')->nullable()->after('id');
            $table->char('soap_type_id', 2)->nullable()->after('external_id');
            $table->char('document_type_id', 2)->nullable()->after('soap_type_id');
            $table->string('series')->nullable()->after('document_type_id');
            $table->integer('number')->nullable()->after('series');
            $table->string('filename')->nullable()->after('number');

            $table->foreign('soap_type_id')->references('id')->on('soap_types');
            $table->foreign('document_type_id')->references('id')->on('cat_document_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories_transfer', function (Blueprint $table) {
            $table->dropColumn('external_id');
            $table->dropColumn('soap_type_id');
            $table->dropColumn('document_type_id');
            $table->dropColumn('series');
            $table->dropColumn('number');
            $table->dropColumn('filename');
        });
    }

}
