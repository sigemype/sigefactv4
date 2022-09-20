<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPurchasePermissionsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('edit_purchase')->default(true)->after('create_payment');
            $table->boolean('annular_purchase')->default(true)->after('create_payment');
            $table->boolean('delete_purchase')->default(true)->after('create_payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('edit_purchase');
            $table->dropColumn('annular_purchase');
            $table->dropColumn('delete_purchase');
        });
    }
}
