<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddAgentIdToDocumentsSaleNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->unsignedInteger('agent_id')->nullable()->after('reference_data');
            $table->foreign('agent_id')->references('id')->on('agents');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedInteger('agent_id')->nullable()->after('reference_data');
            $table->foreign('agent_id')->references('id')->on('agents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });
        
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });
    }
}
