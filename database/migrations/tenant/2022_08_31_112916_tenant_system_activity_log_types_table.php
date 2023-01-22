<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantSystemActivityLogTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_activity_log_types', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('description');
        });
        
        DB::table('system_activity_log_types')->insert([

            ['id' => 'failed', 'description' => 'Error de inicio de sesión'],
            ['id' => 'logout', 'description' => 'Cerrar sesión'],
            ['id' => 'login', 'description' => 'Iniciar sesión'],

            ['id' => 'companies_number', 'description' => 'Actualización del campo número en configuración de empresa'],
            ['id' => 'companies_name', 'description' => 'Actualización del campo nombre en configuración de empresa'],
            ['id' => 'companies_soap_send_id', 'description' => 'Actualización del campo SOAP envío en configuración de empresa'],
            ['id' => 'companies_soap_type_id', 'description' => 'Actualización del campo SOAP tipo en configuración de empresa'],
            ['id' => 'companies_soap_username', 'description' => 'Actualización del campo SOAP Usuario en configuración de empresa'],
            ['id' => 'companies_soap_password', 'description' => 'Actualización del campo SOAP Contraseña en configuración de empresa'],
            ['id' => 'companies_soap_url', 'description' => 'Actualización del campo SOAP url envío en configuración de empresa'],
            ['id' => 'companies_certificate', 'description' => 'Actualización del campo certificado en configuración de empresa'],

            ['id' => 'module_access_error', 'description' => 'Error de acceso al módulo (no tiene permiso)'],
            ['id' => 'level_module_access_error', 'description' => 'Error de acceso al submódulo (no tiene permiso)'],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_activity_log_types');
    }
}
