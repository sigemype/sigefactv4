<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddTicketToDispatches extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->string('ticket', 36)->nullable()->after('document_id');
            $table->timestamp('reception_date')->nullable()->after('ticket');
            $table->string('qr_url')->nullable()->after('reception_date');
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
            $table->dropColumn('qr_url');
            $table->dropColumn('reception_date');
            $table->dropColumn('ticket');
        });
    }
}
