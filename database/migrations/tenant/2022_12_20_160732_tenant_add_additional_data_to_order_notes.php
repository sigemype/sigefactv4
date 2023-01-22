<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddAdditionalDataToOrderNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_notes', function (Blueprint $table) {
            $table->json('additional_data')->nullable()->after('legends');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_notes', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });
    }
}
