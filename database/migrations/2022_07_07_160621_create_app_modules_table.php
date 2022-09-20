<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppModulesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
            $table->string('description');
            $table->integer('order_menu');
            $table->timestamps();
        });

        DB::table('app_modules')->insert([
            ['value' => 'invoice', 'description' => 'Factura electrónica', 'order_menu' => 1],
            ['value' => 'invoice-ticket', 'description' => 'Boleta electrónica', 'order_menu' => 2],
            ['value' => 'sale-note', 'description' => 'Nota de venta', 'order_menu' => 3],
            ['value' => 'order-note', 'description' => 'Pedido', 'order_menu' => 4],
            ['value' => 'purchase', 'description' => 'Compra', 'order_menu' => 5],
            ['value' => 'documents', 'description' => 'Lista de comprobantes', 'order_menu' => 6],
            ['value' => 'report-sales', 'description' => 'Reportes', 'order_menu' => 7],
            ['value' => 'validate-document', 'description' => 'Validar cpe', 'order_menu' => 8],
            ['value' => 'customers', 'description' => 'Clientes', 'order_menu' => 9],
            ['value' => 'items', 'description' => 'Productos', 'order_menu' => 10],
            ['value' => 'cash', 'description' => 'Caja', 'order_menu' => 11],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_modules');
    }
    
}
