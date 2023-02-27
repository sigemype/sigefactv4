<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantUpdateToDrivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('telephone');
            $table->boolean('is_active')->default(true)->after('is_default');
        });

        Schema::table('dispatchers', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('number_mtc');
            $table->boolean('is_active')->default(true)->after('is_default');
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->unsignedInteger('origin_address_id')->nullable();
            $table->unsignedInteger('delivery_address_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropColumn('is_active');
        });

        Schema::table('dispatchers', function (Blueprint $table) {
            $table->dropColumn('is_default');
            $table->dropColumn('is_active');
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn('origin_address_id');
            $table->dropColumn('delivery_address_id');
        });
    }
}
