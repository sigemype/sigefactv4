<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddAdditionalDataToDispatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dispatches', 'additional_data')) {
            Schema::table('dispatches', function (Blueprint $table) {
                $table->json('additional_data')->nullable()->after('terms_condition');
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
        if (Schema::hasColumn('dispatches', 'additional_data')) {
            Schema::table('dispatches', function (Blueprint $table) {
                $table->dropColumn('additional_data');
            });
        }
    }
}
