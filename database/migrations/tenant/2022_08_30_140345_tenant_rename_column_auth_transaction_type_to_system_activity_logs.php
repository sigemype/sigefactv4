<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantRenameColumnAuthTransactionTypeToSystemActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_activity_logs', function (Blueprint $table) {
            $table->renameColumn('auth_transaction_type', 'transaction_type');
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
            $table->renameColumn('transaction_type', 'auth_transaction_type');
        });
    }
}
