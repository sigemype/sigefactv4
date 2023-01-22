<?php

use App\Models\Tenant\Catalogs\TransferReasonType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantUpdateDataToDispatches extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = [
            ['id' => '01', 'name' => 'Venta'],
            ['id' => '02', 'name' => 'Compra'],
            ['id' => '03', 'name' => 'Venta con entrega a terceros'],
            ['id' => '04', 'name' => 'Traslado entre establecimientos de la misma empresa'],
            ['id' => '05', 'name' => 'Consignación'],
            ['id' => '06', 'name' => 'Devolución'],
            ['id' => '07', 'name' => 'Recojo de bienes transformados'],
            ['id' => '08', 'name' => 'Importación'],
            ['id' => '09', 'name' => 'Exportación'],
            ['id' => '13', 'name' => 'Otros no comprendido en ningún código del presente catálogo'],
            ['id' => '14', 'name' => 'Venta sujeta a confirmación del comprador'],
            ['id' => '17', 'name' => 'Traslado de bienes para transformación'],
            ['id' => '18', 'name' => 'Traslado emisor itinerante de comprobantes de pago Aquí no se está considerando el traslado a zona primaria.'],
        ];

        TransferReasonType::query()->update([
            'active' => false,
        ]);

        foreach ($data as $row) {
            TransferReasonType::query()->updateOrCreate([
                'id' => $row['id'],
            ], [
                'description' => $row['name'],
                'active' => true
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
