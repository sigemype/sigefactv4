<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddForeignTransactionTypeToSystemActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_activity_logs', function (Blueprint $table) {
            $table->renameColumn('transaction_type', 'system_activity_log_type_id');
            $table->foreign('system_activity_log_type_id')->references('id')->on('system_activity_log_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_activity_logs', function (Blueprint $table) {
            $table->dropForeign(['system_activity_log_type_id']);
            $table->renameColumn('system_activity_log_type_id', 'transaction_type');
        });
    }
}
