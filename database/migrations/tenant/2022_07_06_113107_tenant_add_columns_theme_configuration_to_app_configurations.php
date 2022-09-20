<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsThemeConfigurationToAppConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_configurations', function (Blueprint $table) {

            $table->string('theme_color')->default('blue')->after('print_format_pdf');
            $table->string('card_color')->default('multicolored')->after('print_format_pdf');

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
            $table->dropColumn('theme_color');
            $table->dropColumn('card_color');
        });
    }
}
