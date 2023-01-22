<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class AddDataRestaurantTableEnvs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('restaurant_table_envs')->insert([
            ['id' => 1, 'active' => true, 'name' => 'Ambiente 1', 'tables_quantity' => 25],
            ['id' => 2, 'active' => true, 'name' => 'Ambiente 2', 'tables_quantity' => 25],
            ['id' => 3, 'active' => true, 'name' => 'Ambiente 3', 'tables_quantity' => 25],
            ['id' => 4, 'active' => true, 'name' => 'Ambiente 4', 'tables_quantity' => 25],
        ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('restaurant_table_envs')->where('id',1)->delete();
        DB::table('restaurant_table_envs')->where('id',2)->delete();
        DB::table('restaurant_table_envs')->where('id',3)->delete();
        DB::table('restaurant_table_envs')->where('id',4)->delete();

    }
}
