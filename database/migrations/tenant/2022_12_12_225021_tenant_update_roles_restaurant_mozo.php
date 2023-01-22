<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantUpdateRolesRestaurantMozo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::table('restaurant_roles')->where('code', 'KIT')->delete();
        DB::table('restaurant_roles')->where('code', 'BAR')->delete();

        DB::table('users')->whereIn('restaurant_role_id', [4, 5])->update(['restaurant_role_id' => null ]);

        DB::table('restaurant_roles')->insert([
            [
                'code' => 'KITBAR',
                'name' => 'Cocina/Bar',
                'description' => 'Usuario con acceso a cocina y bar',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('restaurant_roles')->insert([
            [
                'code' => 'KIT',
                'name' => 'Comanda/Cocina',
                'description' => 'Usuario con acceso a cocina',
            ],
            [
                'code' => 'BAR',
                'name' => 'Comanda/Bar',
                'description' => 'Usuario con acceso a bar',
            ],
        ]);

        DB::table('restaurant_roles')->where('code', 'KITBAR')->delete();
    }
}
