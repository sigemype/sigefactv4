<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPrintFormatPdfToAppConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->string('print_format_pdf')->default('ticket')->after('show_image_item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_configurations', function (Blueprint $table) {
            $table->dropColumn('print_format_pdf');
        });
    }
}
