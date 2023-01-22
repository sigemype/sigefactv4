<?php

use App\Models\Tenant\Catalogs\DocumentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('external_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('warehouse_id');
            $table->char('soap_type_id', 2);
            $table->char('document_type_id', 2);
            $table->string('series');
            $table->integer('number');
            $table->date('date_of_issue');
            $table->time('time_of_issue');
            $table->char('inventory_transaction_id', 2);
            $table->unsignedInteger('guideable_id')->nullable();
            $table->string('guideable_type')->nullable();
            $table->longText('observations')->nullable();
            $table->string('filename')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
            $table->foreign('document_type_id')->references('id')->on('cat_document_types');
            $table->foreign('inventory_transaction_id')->references('id')->on('inventory_transactions');

            $table->unique(['soap_type_id', 'document_type_id', 'series', 'number'], 'guides_series_number_unique');
        });

        DocumentType::query()
            ->updateOrCreate([
                'id' => 'U2'
            ], [
                'description' => 'Guía de Ingreso Almacén',
                'active' => true,
            ]);
        DocumentType::query()
            ->updateOrCreate([
                'id' => 'U3'
            ], [
                'description' => 'Guía de Salida Almacén',
                'active' => true,
            ]);

        DocumentType::query()
            ->updateOrCreate([
                'id' => 'U4'
            ], [
                'description' => 'Guía de Transferencia Almacén',
                'active' => true,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guides');
    }
}
