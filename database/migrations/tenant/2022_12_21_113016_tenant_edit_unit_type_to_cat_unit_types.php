<?php

use App\Models\Tenant\Catalogs\UnitType;
use Illuminate\Database\Migrations\Migration;

class TenantEditUnitTypeToCatUnitTypes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        UnitType::query()
            ->where('id', 'TM')
            ->update([
                'active' => false
            ]);

        UnitType::query()->updateOrCreate([
            'id' => 'TNE'
        ], [
            'description' => 'Toneladas',
            'active' => true
        ]);
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
