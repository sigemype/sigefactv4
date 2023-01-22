<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddAdditionalDataToDispatchItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dispatch_items', 'additional_data')) {
            Schema::table('dispatch_items', function (Blueprint $table) {
                $table->json('additional_data')->nullable()->after('name_product_pdf');
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
        if (Schema::hasColumn('dispatch_items', 'additional_data')) {
            Schema::table('dispatch_items', function (Blueprint $table) {
                $table->dropColumn('additional_data');
            });
        }
    }
}
