<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddReceiverToDispatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->unsignedInteger('sender_id')->nullable()->after('license_plate');
            $table->json('sender_data')->nullable()->after('sender_id');

            $table->unsignedInteger('sender_address_id')->nullable()->after('sender_data');
            $table->json('sender_address_data')->nullable()->after('sender_address_id');

            $table->unsignedInteger('receiver_id')->nullable()->after('sender_address_data');
            $table->json('receiver_data')->nullable()->after('receiver_id');

            $table->unsignedInteger('receiver_address_id')->nullable()->after('receiver_data');
            $table->json('receiver_address_data')->nullable()->after('receiver_address_id');

            $table->foreign('sender_id')->references('id')->on('persons');
            $table->foreign('sender_address_id')->references('id')->on('dispatch_addresses');
            $table->foreign('receiver_id')->references('id')->on('persons');
            $table->foreign('receiver_address_id')->references('id')->on('dispatch_addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn('sender_id');
            $table->dropColumn('sender_data');

            $table->dropForeign(['sender_address_id']);
            $table->dropColumn('sender_address_id');
            $table->dropColumn('sender_address_data');

            $table->dropForeign(['receiver_id']);
            $table->dropColumn('receiver_id');
            $table->dropColumn('receiver_data');

            $table->dropForeign(['receiver_address_id']);
            $table->dropColumn('receiver_address_id');
            $table->dropColumn('receiver_address_data');
        });
    }
}
