<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPersonalInformationToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('names')->nullable()->after('locked');
            $table->string('last_names')->nullable()->after('locked');

            $table->string('personal_email')->nullable()->after('locked');
            $table->string('corporate_email')->nullable()->after('locked');

            $table->string('personal_cell_phone')->nullable()->after('locked');
            $table->string('corporate_cell_phone')->nullable()->after('locked');

            $table->date('date_of_birth')->nullable()->after('locked');
            $table->date('contract_date')->nullable()->after('locked');

            $table->string('position')->nullable()->after('locked');
            $table->string('photo_filename')->nullable()->after('locked');

            $table->boolean('multiple_default_document_types')->default(false)->after('locked');
            
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

            $table->dropColumn('names');
            $table->dropColumn('last_names');

            $table->dropColumn('personal_email');
            $table->dropColumn('corporate_email');

            $table->dropColumn('personal_cell_phone');
            $table->dropColumn('corporate_cell_phone');

            $table->dropColumn('date_of_birth');
            $table->dropColumn('contract_date');

            $table->dropColumn('position');
            $table->dropColumn('photo_filename');

            $table->dropColumn('multiple_default_document_types');

        });
    }
}
