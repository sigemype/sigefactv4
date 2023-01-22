<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDispatchTicketPdfToDocumentSaleNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('dispatch_ticket_pdf')->default(false)->after('total');
        });
        
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->boolean('dispatch_ticket_pdf')->default(false)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('dispatch_ticket_pdf');
        });
        
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->dropColumn('dispatch_ticket_pdf');
        });
    }
}
