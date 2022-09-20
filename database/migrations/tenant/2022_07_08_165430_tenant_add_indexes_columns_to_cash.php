<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIndexesColumnsToCash extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash', function (Blueprint $table) {
            $table->index('date_opening');
            $table->index('income');
            $table->index('state');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash', function (Blueprint $table) {
            $table->dropIndex(['date_opening']);
            $table->dropIndex(['income']);
            $table->dropIndex(['state']);
            $table->dropIndex(['reference_number']);
        });
    }
}
